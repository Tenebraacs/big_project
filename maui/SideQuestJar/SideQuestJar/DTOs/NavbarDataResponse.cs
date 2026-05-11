using System.Collections.Generic;
using System.Text.Json.Serialization;

namespace SideQuestJar.DTOs
{
    public class NavbarDataResponse
    {
        [JsonPropertyName("user")]
        public NavbarUser User { get; set; }

        [JsonPropertyName("streak")]
        public int Streak { get; set; }

        [JsonPropertyName("filled_indexes")]
        public List<int> FilledIndexes { get; set; } = new();

        [JsonPropertyName("today_index")]
        public int TodayIndex { get; set; }
    }

    public class NavbarUser
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("username")]
        public string Username { get; set; }

        [JsonPropertyName("profile_picture_url")]
        public string ProfilePictureUrl { get; set; }

        [JsonPropertyName("is_admin")]
        public bool IsAdmin { get; set; }
    }
}
