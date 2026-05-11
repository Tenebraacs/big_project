using SideQuestJar.Data;
using SideQuestJar.DTOs;
using SideQuestJar.Models;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http.Json;
using System.Text.Json;
using System.Text.Json.Serialization;
using Microsoft.EntityFrameworkCore;

namespace SideQuestJar.Services
{
    // Service for handling messaging logic, following the BFF (Backend for Frontend) pattern
    // and ensuring offline-first data storage with SQLite.
    public class MessageService
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://127.0.0.1:8000/api/";
        private readonly JsonSerializerOptions _jsonOptions;

        public MessageService()
        {
            _httpClient = new HttpClient { BaseAddress = new Uri(BaseUrl) };
            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");

            string userAgent = $"{AppInfo.Current.Name} MAUI - ({DeviceInfo.Current.Platform}; {DeviceInfo.Current.VersionString})";
            _httpClient.DefaultRequestHeaders.Add("User-Agent", userAgent);

            _jsonOptions = new JsonSerializerOptions
            {
                PropertyNameCaseInsensitive = true,
                PropertyNamingPolicy = JsonNamingPolicy.SnakeCaseLower,
                Converters = { new JsonStringEnumConverter(JsonNamingPolicy.SnakeCaseLower) }
            };
        }

        private async Task SetAuthorizationHeaderAsync()
        {
            var token = await SecureStorage.Default.GetAsync("auth_token");
            if (!string.IsNullOrEmpty(token))
                _httpClient.DefaultRequestHeaders.Authorization =
                    new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", token);
        }

        // BFF: Fetches the combined timeline of chat messages and invitations
        public async Task<List<TimelineItemDto>> GetChatTimelineAsync(int friendId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync($"chat/{friendId}/messages");

                if (response.IsSuccessStatusCode)
                {
                    var data = await response.Content.ReadFromJsonAsync<List<TimelineItemDto>>(_jsonOptions);
                    if (data != null)
                    {
                        await CacheTimelineAsync(friendId, data);
                        return data;
                    }
                }
                return await GetOfflineTimelineAsync(friendId);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[MessageService] GetChatTimeline error: {ex.Message}");
                return await GetOfflineTimelineAsync(friendId);
            }
        }

        // Send a new message to a friend
        public async Task<TimelineItemDto?> SendMessageAsync(int friendId, string content)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var payload = new { content };
                var response = await _httpClient.PostAsJsonAsync($"chat/{friendId}/messages", payload);

                if (response.IsSuccessStatusCode)
                {
                    var newItem = await response.Content.ReadFromJsonAsync<TimelineItemDto>(_jsonOptions);
                    if (newItem != null)
                    {
                        await SaveLocalMessageAsync(friendId, newItem);
                        return newItem;
                    }
                }
                return null;
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[MessageService] SendMessage error: {ex.Message}");
                return null;
            }
        }

        // ── SQLite Repository logic (Local Data Storing) ─────────────────────────

        private async Task CacheTimelineAsync(int friendId, List<TimelineItemDto> items)
        {
            try
            {
                using var db = new AppDbContext();
                var localUserId = await db.Users.Select(u => u.Id).FirstOrDefaultAsync();
                if (localUserId == 0) return;

                // Wipe existing cached messages for this conversation to sync with server
                var existing = db.Messages.Where(m => m.LocalUserId == localUserId && m.FriendId == friendId);
                db.Messages.RemoveRange(existing);

                foreach (var item in items)
                {
                    db.Messages.Add(ToEntry(item, localUserId, friendId));
                }

                await db.SaveChangesAsync();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[MessageService] CacheTimeline error: {ex.Message}");
            }
        }

        private async Task SaveLocalMessageAsync(int friendId, TimelineItemDto item)
        {
            try
            {
                using var db = new AppDbContext();
                var localUserId = await db.Users.Select(u => u.Id).FirstOrDefaultAsync();
                if (localUserId == 0) return;

                db.Messages.Add(ToEntry(item, localUserId, friendId));
                await db.SaveChangesAsync();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[MessageService] SaveLocalMessage error: {ex.Message}");
            }
        }

        private async Task<List<TimelineItemDto>> GetOfflineTimelineAsync(int friendId)
        {
            try
            {
                using var db = new AppDbContext();
                var localUserId = await db.Users.Select(u => u.Id).FirstOrDefaultAsync();
                
                return await db.Messages
                    .Where(m => m.LocalUserId == localUserId && m.FriendId == friendId)
                    .OrderBy(m => m.CreatedAt)
                    .Select(m => ToDto(m))
                    .ToListAsync();
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[MessageService] GetOfflineTimeline error: {ex.Message}");
                return new List<TimelineItemDto>();
            }
        }

        private static MessageEntry ToEntry(TimelineItemDto dto, int localUserId, int friendId) => new()
        {
            RemoteId = dto.Id,
            LocalUserId = localUserId,
            FriendId = friendId,
            SenderId = dto.SenderId,
            Content = dto.Content,
            Type = dto.Type,
            CreatedAt = dto.CreatedAt,
            JarName = dto.JarName,
            JarThumb = dto.JarThumb,
            InviteStatus = dto.Status
        };

        private static TimelineItemDto ToDto(MessageEntry e) => new()
        {
            Id = e.RemoteId,
            SenderId = e.SenderId,
            Content = e.Content,
            Type = e.Type,
            CreatedAt = e.CreatedAt,
            JarName = e.JarName,
            JarThumb = e.JarThumb,
            Status = e.InviteStatus
        };
    }
}
