using SideQuestJar.DTOs;
using SideQuestJar.Models;
using SideQuestJar;
using System;
using System.Net.Http.Json;
using System.Text;
using System.Text.Json;
using System.Text.Json.Serialization;

namespace SideQuestJar.Services
{
    public class JarService
    {
        private readonly HttpClient _httpClient;
        private const string BaseUrl = "http://127.0.0.1:8000/api/";
        private readonly JsonSerializerOptions jsonOptions;

        public JarService()
        {
            _httpClient = new HttpClient { BaseAddress = new Uri(BaseUrl) };

            _httpClient.DefaultRequestHeaders.Add("Accept", "application/json");

            jsonOptions = new JsonSerializerOptions
            {
                PropertyNameCaseInsensitive = true,
                PropertyNamingPolicy = JsonNamingPolicy.SnakeCaseLower,
                // This converter automatically maps "official_jar" to JarType.OfficialJar
                Converters = { new JsonStringEnumConverter(JsonNamingPolicy.SnakeCaseLower) }
            };
        }

        private async Task SetAuthorizationHeaderAsync()
        {
            var token = await SecureStorage.Default.GetAsync("auth_token");
            if (!string.IsNullOrEmpty(token))
            {
                _httpClient.DefaultRequestHeaders.Authorization =
                    new System.Net.Http.Headers.AuthenticationHeaderValue("Bearer", token);
            }
        }

        public async Task<PaginatedResponse<Jar>?> GetPopularJarsAsync(int page = 1)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync($"jars/popular?page={page}");

                if (response.IsSuccessStatusCode)
                {
                    var data = await response.Content.ReadFromJsonAsync<PaginatedResponse<Jar>>(jsonOptions);
                    if (data?.Data != null)
                    {
                        data.Data = data.Data.Where(j => j.Id != AppConstants.MainQuestJarId).ToList();
                        if (data.Data.Count > 0) await CacheJarsAsync(data.Data);
                    }
                    return data;
                }
                return null;
            }
            catch (HttpRequestException)
            {
                try
                {
                    using var db = new SideQuestJar.Data.AppDbContext();
                    var items = db.Jars.Where(j => j.Id != AppConstants.MainQuestJarId).OrderByDescending(j => j.Likes).Skip((page - 1) * 10).Take(10).ToList();
                    return new PaginatedResponse<Jar> { Data = items, CurrentPage = page, LastPage = page + 1 };
                }
                catch { return null; }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error fetching popular jars: {ex.Message}");
                return null;
            }
        }

        public async Task<PaginatedResponse<Jar>?> GetRecommendedJarsAsync(int page = 1)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync($"jars/recommended?page={page}");

                if (response.IsSuccessStatusCode)
                {
                    var data = await response.Content.ReadFromJsonAsync<PaginatedResponse<Jar>>(jsonOptions);
                    if (data?.Data != null)
                    {
                        data.Data = data.Data.Where(j => j.Id != AppConstants.MainQuestJarId).ToList();
                        if (data.Data.Count > 0) await CacheJarsAsync(data.Data);
                    }
                    return data;
                }
                return null;
            }
            catch (HttpRequestException)
            {
                try
                {
                    using var db = new SideQuestJar.Data.AppDbContext();
                    var items = db.Jars.Where(j => j.Id != AppConstants.MainQuestJarId).OrderByDescending(j => j.Id).Skip((page - 1) * 10).Take(10).ToList();
                    return new PaginatedResponse<Jar> { Data = items, CurrentPage = page, LastPage = page + 1 };
                }
                catch { return null; }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error fetching recommended jars: {ex.Message}");
                return null;
            }
        }

        public async Task<DiscoverResponse?> GetDiscoverDataAsync()
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync("discover");

                if (response.IsSuccessStatusCode)
                {
                    var data = await response.Content.ReadFromJsonAsync<DiscoverResponse>(jsonOptions);
                    if (data != null)
                    {
                        data.PopularJars = (data.PopularJars ?? new List<Jar>())
                            .Where(j => j.Id != AppConstants.MainQuestJarId).ToList();
                        data.RecommendedJars = (data.RecommendedJars ?? new List<Jar>())
                            .Where(j => j.Id != AppConstants.MainQuestJarId).ToList();
                        await CacheJarsAsync(data.PopularJars);
                        await CacheJarsAsync(data.RecommendedJars);
                        if (data.MainJar != null) await CacheJarsAsync(new List<Jar> { data.MainJar });
                    }
                    return data;
                }
                return null;
            }
            catch (HttpRequestException)
            {
                try
                {
                    using var db = new SideQuestJar.Data.AppDbContext();
                    return new DiscoverResponse
                    {
                        MainJar = db.Jars.FirstOrDefault(),
                        PopularJars = db.Jars.Where(j => j.Id != AppConstants.MainQuestJarId)
                            .OrderByDescending(j => j.Likes).Take(10).ToList(),
                        RecommendedJars = db.Jars.Where(j => j.Id != AppConstants.MainQuestJarId)
                            .OrderByDescending(j => j.Id).Take(10).ToList()
                    };
                }
                catch { return null; }
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error fetching discover feed data: {ex.Message}");
                return null;
            }
        }

        private async Task CacheJarsAsync(List<Jar> jars)
        {
            if (jars == null || !jars.Any()) return;
            try
            {
                using var db = new SideQuestJar.Data.AppDbContext();
                foreach (var jar in jars)
                {
                    var existing = db.Jars.Find(jar.Id);
                    if (existing != null)
                    {
                        existing.Name = jar.Name;
                        existing.Description = jar.Description;
                        existing.Picture = jar.Picture;
                        existing.PictureUrl = jar.PictureUrl;
                        existing.Likes = jar.Likes;
                        existing.Type = jar.Type;
                        existing.CreatedBy = jar.CreatedBy;
                        existing.CreatorUsername = jar.User?.Username ?? jar.CreatorUsername;
                        db.Jars.Update(existing);
                    }
                    else
                    {
                        jar.CreatorUsername = jar.User?.Username ?? "unknown";
                        db.Jars.Add(jar);
                    }
                }
                await db.SaveChangesAsync();
            }
            catch (Exception ex) { Console.WriteLine($"Cache error: {ex.Message}"); }
        }

        public async Task<JarDetailsResponse?> GetJarDetailsAsync(int jarId)
        {
            JarDetailsResponse? result = null;

            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync($"jars/{jarId}");

                if (response.IsSuccessStatusCode)
                {
                    result = await response.Content.ReadFromJsonAsync<JarDetailsResponse>(jsonOptions);

                    // Cache to SQLite
                    if (result?.Jar != null)
                    {
                        await CacheJarsAsync(new List<Jar> { result.Jar });
                        await CacheQuestsAsync(result.Quests, jarId);
                    }
                    return result;
                }
                Console.WriteLine($"[JarService] API returned {response.StatusCode} for Jar {jarId}");
            }
            catch (HttpRequestException)
            {
                Console.WriteLine($"[JarService] Offline — loading Jar {jarId} from SQLite.");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error fetching jar details: {ex.Message}");
            }

            // Offline fallback — load from local SQLite
            try
            {
                using var db = new SideQuestJar.Data.AppDbContext();
                var jar = db.Jars.Find(jarId);
                if (jar == null) return null;

                var quests = db.Quests.Where(q => q.JarId == jarId).ToList();
                var userId = db.Users.Select(u => u.Id).FirstOrDefault();
                var isJoined = db.JarUsers.Any(ju => ju.JarId == jarId && ju.UserId == userId);

                return new JarDetailsResponse { Jar = jar, Quests = quests, IsJoined = isJoined };
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] SQLite fallback error: {ex.Message}");
                return null;
            }
        }

        public async Task<(bool IsSuccess, string Message)> JoinJarAsync(int jarId)
        {
            // 1. Optimistic local write first
            try
            {
                using var db = new SideQuestJar.Data.AppDbContext();
                var userId = db.Users.Select(u => u.Id).FirstOrDefault();
                var alreadyJoined = db.JarUsers.Any(ju => ju.JarId == jarId && ju.UserId == userId);
                if (!alreadyJoined && userId > 0)
                {
                    db.JarUsers.Add(new SideQuestJar.Models.JarUser { JarId = jarId, UserId = userId, Streak = 0 });
                    await db.SaveChangesAsync();
                }
            }
            catch (Exception dbEx)
            {
                Console.WriteLine($"[JarService] Local join error: {dbEx.Message}");
            }

            // 2. Try to push to API
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.PostAsync($"jars/{jarId}/join", null);

                if (response.IsSuccessStatusCode)
                    return (true, "Successfully joined the jar.");

                if (response.StatusCode == System.Net.HttpStatusCode.Conflict)
                    return (true, "You are already a member of this jar.");

                return (true, "Joined locally — will sync when online.");
            }
            catch (HttpRequestException)
            {
                return (true, "Joined offline — will sync automatically when connected.");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error joining jar: {ex.Message}");
                return (true, "Joined locally.");
            }
        }
        
        public async Task<(bool IsSuccess, string Message)> LeaveJarAsync(int jarId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.PostAsync($"jars/{jarId}/leave", null);
                
                if (response.IsSuccessStatusCode)
                {
                    using var db = new SideQuestJar.Data.AppDbContext();
                    var userId = db.Users.Select(u => u.Id).FirstOrDefault();
                    var entry = db.JarUsers.FirstOrDefault(ju => ju.JarId == jarId && ju.UserId == userId);
                    if (entry != null)
                    {
                        db.JarUsers.Remove(entry);
                        await db.SaveChangesAsync();
                    }
                    return (true, "Successfully left the jar.");
                }
                return (false, "Failed to leave the jar.");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] Error leaving jar: {ex.Message}");
                return (false, "An error occurred.");
            }
        }

        private async Task CacheQuestsAsync(List<JarQuest> quests, int jarId)
        {
            if (quests == null || !quests.Any()) return;
            try
            {
                using var db = new SideQuestJar.Data.AppDbContext();
                var existing = db.Quests.Where(q => q.JarId == jarId).ToList();
                db.Quests.RemoveRange(existing);
                foreach (var q in quests)
                {
                    // Ensure we don't try to track the same entity if it's already in the list
                    db.Quests.Add(q);
                }
                await db.SaveChangesAsync();
            }
            catch (Exception ex) { Console.WriteLine($"Quest cache error: {ex.Message}"); }
        }

        // ─── Jar CRUD ─────────────────────────────────────────────────────────

        public async Task<(bool IsSuccess, string Message, Jar? Jar)> CreateJarAsync(Jar jar, byte[]? imageBytes = null)
        {
            try
                {
                await SetAuthorizationHeaderAsync();
                using var content = new MultipartFormDataContent();
                content.Add(new StringContent(jar.Name), "name");
                content.Add(new StringContent(jar.Description ?? ""), "description");
                
                string typeStr = jar.Type switch
                {
                    JarType.PublicJar => "public_jar",
                    JarType.PrivateJar => "private_jar",
                    JarType.FriendsOnlyJar => "friends_only_jar",
                    JarType.InviteOnlyJar => "invite_only_jar",
                    JarType.OfficialJar => "official_jar",
                    _ => "public_jar"
                };
                content.Add(new StringContent(typeStr), "type");

                if (imageBytes != null)
                {
                    var imgContent = new ByteArrayContent(imageBytes);
                    imgContent.Headers.ContentType = new System.Net.Http.Headers.MediaTypeHeaderValue("image/jpeg");
                    content.Add(imgContent, "picture", "jar.jpg");
                }

                var response = await _httpClient.PostAsync("jars", content);
                if (response.IsSuccessStatusCode)
                {
                    var created = await response.Content.ReadFromJsonAsync<Jar>(jsonOptions);
                    if (created != null) await CacheJarsAsync(new List<Jar> { created });
                    return (true, "Jar created successfully.", created);
                }
                var error = await response.Content.ReadAsStringAsync();
                return (false, $"Failed to create jar: {error}", null);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] CreateJar error: {ex.Message}");
                return (false, "An error occurred.", null);
            }
        }

        public async Task<(bool IsSuccess, string Message)> UpdateJarAsync(int id, Jar jar, byte[]? imageBytes = null)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                // Since Laravel PUT doesn't handle multipart well, we'll use POST with _method=PUT or just multipart POST if supported.
                // Or better, use a POST request to a specific update endpoint or use X-HTTP-Method-Override.
                using var content = new MultipartFormDataContent();
                content.Add(new StringContent(jar.Name), "name");
                content.Add(new StringContent(jar.Description ?? ""), "description");
                
                string typeStr = jar.Type switch
                {
                    JarType.PublicJar => "public_jar",
                    JarType.PrivateJar => "private_jar",
                    JarType.FriendsOnlyJar => "friends_only_jar",
                    JarType.InviteOnlyJar => "invite_only_jar",
                    JarType.OfficialJar => "official_jar",
                    _ => "public_jar"
                };
                content.Add(new StringContent(typeStr), "type");
                content.Add(new StringContent("PUT"), "_method");

                if (imageBytes != null)
                {
                    var imgContent = new ByteArrayContent(imageBytes);
                    imgContent.Headers.ContentType = new System.Net.Http.Headers.MediaTypeHeaderValue("image/jpeg");
                    content.Add(imgContent, "picture", "jar.jpg");
                }

                var response = await _httpClient.PostAsync($"jars/{id}", content);
                if (response.IsSuccessStatusCode)
                {
                    return (true, "Jar updated successfully.");
                }
                return (false, "Failed to update jar.");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] UpdateJar error: {ex.Message}");
                return (false, "An error occurred.");
            }
        }

        public async Task<(bool IsSuccess, string Message)> DeleteJarAsync(int id)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.DeleteAsync($"jars/{id}");
                if (response.IsSuccessStatusCode)
                {
                    try
                    {
                        using var db = new SideQuestJar.Data.AppDbContext();
                        // Delete related completions and quests locally first to avoid FK issues
                        var questIds = db.Quests.Where(q => q.JarId == id).Select(q => q.Id).ToList();
                        if (questIds.Any())
                        {
                            var completions = db.QuestCompletions.Where(qc => questIds.Contains(qc.QuestId)).ToList();
                            if (completions.Any()) db.QuestCompletions.RemoveRange(completions);
                            
                            var quests = db.Quests.Where(q => q.JarId == id).ToList();
                            db.Quests.RemoveRange(quests);
                        }

                        // Also clean up local members/joins
                        var joins = db.JarUsers.Where(ju => ju.JarId == id).ToList();
                        if (joins.Any()) db.JarUsers.RemoveRange(joins);

                        var jar = db.Jars.Find(id);
                        if (jar != null) db.Jars.Remove(jar);
                        
                        await db.SaveChangesAsync();
                    }
                    catch (Exception ex)
                    {
                        Console.WriteLine($"[JarService] Local cache cleanup error during jar delete: {ex.Message}");
                    }
                    return (true, "Jar deleted successfully.");
                }
                var error = await response.Content.ReadAsStringAsync();
                return (false, $"Failed to delete jar: {error}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] DeleteJar error: {ex.Message}");
                return (false, "An error occurred.");
            }
        }

        // ─── Quest CRUD ───────────────────────────────────────────────────────

        public async Task<(bool IsSuccess, string Message, JarQuest? Quest)> CreateQuestAsync(int jarId, JarQuest quest, byte[]? imageBytes = null)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                using var content = new MultipartFormDataContent();
                content.Add(new StringContent(quest.Title), "title");
                content.Add(new StringContent(quest.Description ?? ""), "description");

                if (imageBytes != null)
                {
                    var imgContent = new ByteArrayContent(imageBytes);
                    imgContent.Headers.ContentType = new System.Net.Http.Headers.MediaTypeHeaderValue("image/jpeg");
                    content.Add(imgContent, "picture", "quest.jpg");
                }

                var response = await _httpClient.PostAsync($"jars/{jarId}/quests", content);
                if (response.IsSuccessStatusCode)
                {
                    var result = await response.Content.ReadFromJsonAsync<QuestWrapper>(jsonOptions);
                    return (true, "Quest created successfully.", result?.Quest);
                }
                var error = await response.Content.ReadAsStringAsync();
                return (false, $"Failed to create quest: {error}", null);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] CreateQuest error: {ex.Message}");
                return (false, "An error occurred.", null);
            }
        }

        public async Task<(bool IsSuccess, string Message)> UpdateQuestAsync(int jarId, int questId, JarQuest quest, byte[]? imageBytes = null)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                using var content = new MultipartFormDataContent();
                content.Add(new StringContent(quest.Title), "title");
                content.Add(new StringContent(quest.Description ?? ""), "description");
                content.Add(new StringContent("PUT"), "_method");

                if (imageBytes != null)
                {
                    var imgContent = new ByteArrayContent(imageBytes);
                    imgContent.Headers.ContentType = new System.Net.Http.Headers.MediaTypeHeaderValue("image/jpeg");
                    content.Add(imgContent, "picture", "quest.jpg");
                }

                var response = await _httpClient.PostAsync($"jars/{jarId}/quests/{questId}", content);
                if (response.IsSuccessStatusCode)
                {
                    return (true, "Quest updated successfully.");
                }
                var error = await response.Content.ReadAsStringAsync();
                return (false, $"Failed to update quest: {error}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] UpdateQuest error: {ex.Message}");
                return (false, "An error occurred.");
            }
        }

        public async Task<(bool IsSuccess, string Message)> DeleteQuestAsync(int jarId, int questId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.DeleteAsync($"jars/{jarId}/quests/{questId}");
                if (response.IsSuccessStatusCode)
                {
                    return (true, "Quest deleted successfully.");
                }
                var error = await response.Content.ReadAsStringAsync();
                return (false, $"Failed to delete quest: {error}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] DeleteQuest error: {ex.Message}");
                return (false, "An error occurred.");
            }
        }

        private class QuestWrapper
        {
            [JsonPropertyName("quest")]
            public JarQuest? Quest { get; set; }
        }

        // ─── Comments ────────────────────────────────────────────────────────

        public async Task<List<JarComment>> GetCommentsAsync(int jarId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync($"jars/{jarId}/comments");
                if (response.IsSuccessStatusCode)
                {
                    var data = await response.Content.ReadFromJsonAsync<CommentListWrapper>(jsonOptions);
                    return data?.Comments ?? new List<JarComment>();
                }
            }
            catch (Exception ex) { Console.WriteLine($"[JarService] GetComments error: {ex.Message}"); }
            return new List<JarComment>();
        }

        public async Task<(bool IsSuccess, JarComment? Comment, string Message)> PostCommentAsync(int jarId, string content)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var payload = new { content };
                var response = await _httpClient.PostAsJsonAsync($"jars/{jarId}/comments", payload);
                if (response.IsSuccessStatusCode)
                {
                    var comment = await response.Content.ReadFromJsonAsync<JarComment>(jsonOptions);
                    return (true, comment, "");
                }
                return (false, null, "Failed to post comment.");
            }
            catch (HttpRequestException)
            {
                return (false, null, "No internet connection.");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] PostComment error: {ex.Message}");
                return (false, null, "An error occurred.");
            }
        }

        // ─── Quest Completion ────────────────────────────────────────────────

        public async Task<(bool IsSuccess, string Message, QuestCompletionDto? Completion)> CompleteQuestAsync(
            int questId, string? description = null, byte[]? imageBytes = null, string? imageFileName = null)
        {
            // 1. Optimistic local write
            try
            {
                using var db = new SideQuestJar.Data.AppDbContext();
                var userId = db.Users.Select(u => u.Id).FirstOrDefault();
                if (userId > 0)
                {
                    var alreadyDone = db.QuestCompletions.Any(qc =>
                        qc.QuestId == questId && qc.UserId == userId &&
                        qc.CompletionDate.Date == DateTime.Today);

                    if (!alreadyDone)
                    {
                        db.QuestCompletions.Add(new QuestCompletion
                        {
                            QuestId        = questId,
                            UserId         = userId,
                            CompletionDate = DateTime.Now,
                            Type           = PostType.PublicPost,
                            Picture        = "placeholder",
                            Description    = description
                        });
                        await db.SaveChangesAsync();
                    }
                    else
                    {
                        return (false, "You already completed this quest today!", null);
                    }
                }
            }
            catch (Exception dbEx) { Console.WriteLine($"[JarService] Local complete error: {dbEx.Message}"); }

            // 2. Push to API with multipart (supports image + description)
            try
            {
                await SetAuthorizationHeaderAsync();

                HttpContent httpContent;
                if (imageBytes != null && imageBytes.Length > 0)
                {
                    var form = new MultipartFormDataContent();
                    if (!string.IsNullOrEmpty(description))
                        form.Add(new StringContent(description), "description");
                    var imgContent = new ByteArrayContent(imageBytes);
                    imgContent.Headers.ContentType = new System.Net.Http.Headers.MediaTypeHeaderValue("image/jpeg");
                    form.Add(imgContent, "picture", imageFileName ?? "completion.jpg");
                    httpContent = form;
                }
                else if (!string.IsNullOrEmpty(description))
                {
                    httpContent = new FormUrlEncodedContent(new[] {
                        new KeyValuePair<string, string>("description", description)
                    });
                }
                else
                {
                    httpContent = new StringContent("", Encoding.UTF8, "application/json");
                }

                var response = await _httpClient.PostAsync($"quests/{questId}/complete", httpContent);

                if (response.IsSuccessStatusCode)
                {
                    var result = await response.Content.ReadFromJsonAsync<CompleteQuestResponse>(jsonOptions);
                    return (true, result?.Message ?? "Quest completed!", result?.Completion);
                }

                if (response.StatusCode == System.Net.HttpStatusCode.Conflict)
                    return (false, "Already completed today!", null);

                return (true, "Completed locally — will sync when online.", null);
            }
            catch (HttpRequestException)
            {
                return (true, "Completed offline — will sync when connected.", null);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] CompleteQuest error: {ex.Message}");
                return (true, "Saved locally.", null);
            }
        }

        public async Task<List<QuestCompletionDto>> GetCompletionsAsync(int jarId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync($"jars/{jarId}/completions");
                if (response.IsSuccessStatusCode)
                {
                    var data = await response.Content.ReadFromJsonAsync<CompletionListWrapper>(jsonOptions);
                    return data?.Completions ?? new List<QuestCompletionDto>();
                }
            }
            catch (Exception ex) { Console.WriteLine($"[JarService] GetCompletions error: {ex.Message}"); }
            return new List<QuestCompletionDto>();
        }

        // ─── Jar Invitations ──────────────────────────────────────────────────

        public async Task<List<Jar>> GetMyJarsAsync()
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.GetAsync("user/jars"); // Assuming this endpoint exists for "my jars"
                if (response.IsSuccessStatusCode)
                {
                    var jars = await response.Content.ReadFromJsonAsync<List<Jar>>(jsonOptions);
                    return jars ?? new List<Jar>();
                }
            }
            catch (Exception ex) { Console.WriteLine($"[JarService] GetMyJars error: {ex.Message}"); }
            return new List<Jar>();
        }

        public async Task<(bool IsSuccess, string Message)> SendJarInvitationAsync(int jarId, int inviteeId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var payload = new { jar_id = jarId, invitee_id = inviteeId };
                var response = await _httpClient.PostAsJsonAsync("jar-invitations", payload);
                if (response.IsSuccessStatusCode)
                {
                    return (true, "Invitation sent successfully.");
                }
                var error = await response.Content.ReadAsStringAsync();
                return (false, $"Failed to send invitation: {error}");
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] SendJarInvitation error: {ex.Message}");
                return (false, "An error occurred while sending invitation.");
            }
        }

        public async Task<(bool Success, bool Liked, int LikesCount, string Message)> ToggleJarLikeAsync(int jarId)
        {
            try
            {
                await SetAuthorizationHeaderAsync();
                var response = await _httpClient.PostAsync($"jars/{jarId}/like", null);
                var body = await response.Content.ReadAsStringAsync();
                if (response.IsSuccessStatusCode)
                {
                    var dto = JsonSerializer.Deserialize<JarLikeToggleResponse>(body, jsonOptions);
                    if (dto != null)
                        return (true, dto.Liked, dto.LikesCount, dto.Message ?? "");
                    return (false, false, 0, "Invalid response.");
                }

                try
                {
                    using var doc = JsonDocument.Parse(body);
                    if (doc.RootElement.TryGetProperty("message", out var msg) && msg.ValueKind == JsonValueKind.String)
                        return (false, false, 0, msg.GetString() ?? "Request failed.");
                }
                catch { /* ignore parse errors */ }

                return (false, false, 0, string.IsNullOrWhiteSpace(body)
                    ? $"Request failed ({(int)response.StatusCode})."
                    : body);
            }
            catch (Exception ex)
            {
                Console.WriteLine($"[JarService] ToggleJarLike error: {ex.Message}");
                return (false, false, 0, "Could not reach the server.");
            }
        }

        private sealed class JarLikeToggleResponse
        {
            [JsonPropertyName("liked")] public bool Liked { get; set; }
            [JsonPropertyName("likes_count")] public int LikesCount { get; set; }
            [JsonPropertyName("message")] public string? Message { get; set; }
        }
    }
}

internal class CommentListWrapper
{
    [System.Text.Json.Serialization.JsonPropertyName("comments")]
    public List<SideQuestJar.Models.JarComment> Comments { get; set; } = new();
}
