using System;
using System.Collections.Generic;
using System.ComponentModel.DataAnnotations.Schema;
using System.Text;
using System.Text.Json.Serialization;

namespace SideQuestJar.Models
{
    public class JarQuest
    {
        public int Id { get; set; }
        public required string Title { get; set; }
        public string? Description { get; set; }
        public string? Picture { get; set; }

        [JsonPropertyName("picture_url")]
        public string? PictureUrl { get; set; }

        public int Likes { get; set; }
        public int Completions { get; set; }
        public int JarId { get; set; }

        [NotMapped]
        [JsonPropertyName("completed_today")]
        public bool CompletedToday { get; set; }
    }
}
