using SideQuestJar.Models;
using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace SideQuestJar.DTOs
{
    public class JarDetailsResponse
    {
        [JsonPropertyName("jar")]
        public Jar? Jar { get; set; }

        [JsonPropertyName("quests")]
        public List<JarQuest> Quests { get; set; } = new();

        [JsonPropertyName("is_joined")]
        public bool IsJoined { get; set; }

        [JsonPropertyName("members")]
        public List<JarMember> Members { get; set; } = new();

        [JsonPropertyName("current_daily_quest_id")]
        public int? CurrentDailyQuestId { get; set; }
    }

    public class JarMember
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("username")]
        public string Username { get; set; } = "";

        [JsonPropertyName("profile_picture")]
        public string? ProfilePicture { get; set; }

        [JsonPropertyName("streak")]
        public int Streak { get; set; }

        [JsonPropertyName("is_you")]
        public bool IsYou { get; set; }
    }
}
