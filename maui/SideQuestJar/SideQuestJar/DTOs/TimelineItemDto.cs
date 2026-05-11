using System;
using System.Text.Json.Serialization;

namespace SideQuestJar.DTOs
{
    public class TimelineItemDto
    {
        public string Type { get; set; } = "chat"; // "chat" | "invite"
        public int Id { get; set; }
        
        [JsonPropertyName("sender_id")]
        public int SenderId { get; set; }
        
        public string Content { get; set; } = string.Empty;
        
        [JsonPropertyName("created_at")]
        public DateTime CreatedAt { get; set; }

        // Invite specific fields
        [JsonPropertyName("jar_name")]
        public string? JarName { get; set; }
        
        [JsonPropertyName("jar_thumb")]
        public string? JarThumb { get; set; }
        
        public string? Status { get; set; }
        
        [JsonPropertyName("updated_at")]
        public DateTime? UpdatedAt { get; set; }
    }
}
