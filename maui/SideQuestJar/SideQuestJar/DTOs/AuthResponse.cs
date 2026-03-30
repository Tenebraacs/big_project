using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace SideQuestJar.DTOs
{
    public class AuthResponse
    {
        [JsonPropertyName("token")]
        public required string Token { get; set; }

        [JsonPropertyName("user")]
        public required ApiUser User { get; set; }
    }

    public class ApiUser
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("username")]
        public required string Username { get; set; }

        [JsonPropertyName("nickname")]
        public string? Nickname { get; set; }

        [JsonPropertyName("fullname")]
        public required string Fullname { get; set; }

        [JsonPropertyName("email")]
        public required string Email { get; set; }

        [JsonPropertyName("profile_picture")]
        public required string ProfilePicture { get; set; }

        [JsonPropertyName("birth_date")]
        public DateTime BirthDate { get; set; }

        [JsonPropertyName("user_xp")]
        public int UserXp { get; set; }

        [JsonPropertyName("is_customized")]
        public int IsCustomized { get; set; }

        [JsonPropertyName("is_admin")]
        public int IsAdmin { get; set; }

        // Átváltjuk a 0/1 értékeket bool típusra a könnyebb használat érdekében
        [JsonIgnore]
        public bool IsAdminBool => IsAdmin == 1;

        [JsonIgnore]
        public bool IsCustomizedBool => IsCustomized == 1;
    }
}
