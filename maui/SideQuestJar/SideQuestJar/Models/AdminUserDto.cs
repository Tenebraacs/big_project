using System;
using System.Collections.Generic;
using System.Text.Json.Serialization;
using SideQuestJar.Serialization;

namespace SideQuestJar.Models
{
    public class AdminUserDto
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("username")]
        public string? Username { get; set; }

        [JsonPropertyName("email")]
        public string? Email { get; set; }

        [JsonPropertyName("profile_picture_url")]
        public string? ProfilePictureUrl { get; set; }

        [JsonPropertyName("is_admin")]
        [JsonConverter(typeof(FlexibleBoolJsonConverter))]
        public bool IsAdmin { get; set; }

        [JsonPropertyName("jars_count")]
        public int JarsCount { get; set; }

        [JsonPropertyName("created_jars_count")]
        public int CreatedJarsCount { get; set; }

        [JsonPropertyName("likes_count")]
        public int LikesCount { get; set; }

        [JsonPropertyName("quest_completions_count")]
        public int QuestCompletionsCount { get; set; }
    }

    public class AdminUserResponse
    {
        [JsonPropertyName("data")]
        public List<AdminUserDto> Data { get; set; } = new();

        [JsonPropertyName("current_page")]
        public int CurrentPage { get; set; }

        [JsonPropertyName("last_page")]
        public int LastPage { get; set; }

        [JsonPropertyName("total")]
        public int Total { get; set; }
    }
}
