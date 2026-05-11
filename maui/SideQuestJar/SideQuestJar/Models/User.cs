using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;
using SideQuestJar.Serialization;

namespace SideQuestJar.Models
{
    public class User
    {
        // Ez a class tárolja az aktuálisan bejelentkezett felhasználót
        public int Id { get; set; }
        public string Username { get; set; } = string.Empty;
        public string? Nickname { get; set; }
        public string Fullname { get; set; } = string.Empty;
        public string Email { get; set; } = string.Empty;
        public string ProfilePicture { get; set; } = string.Empty;
        public DateTime BirthDate { get; set; }

        [JsonPropertyName("is_admin")]
        [JsonConverter(typeof(BoolOrIntJsonConverter))]
        public int IsAdmin { get; set; }
    }
}
