using System;
using System.ComponentModel.DataAnnotations.Schema;
using System.Text.Json.Serialization;

namespace SideQuestJar.Models
{
    public class JarComment
    {
        public int Id { get; set; }
        public int JarId { get; set; }
        public int UserId { get; set; }
        public string Content { get; set; } = "";
        public DateTime CreatedAt { get; set; }

        // Populated from API join, not stored directly in SQLite
        [NotMapped]
        public string Username { get; set; } = "unknown";

        [NotMapped]
        public string? ProfilePicture { get; set; }
    }
}
