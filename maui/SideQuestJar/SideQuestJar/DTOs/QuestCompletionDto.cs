using System.Text.Json.Serialization;
using System.Collections.Generic;

namespace SideQuestJar.DTOs
{
    public class QuestCompletionDto
    {
        [JsonPropertyName("id")]
        public int Id { get; set; }

        [JsonPropertyName("description")]
        public string? Description { get; set; }

        [JsonPropertyName("picture_url")]
        public string? PictureUrl { get; set; }

        [JsonPropertyName("completed_at")]
        public string CompletedAt { get; set; } = "";

        [JsonPropertyName("username")]
        public string Username { get; set; } = "unknown";

        [JsonPropertyName("profile_picture")]
        public string? ProfilePicture { get; set; }

        [JsonPropertyName("quest_title")]
        public string QuestTitle { get; set; } = "";
    }

    public class CompletionListWrapper
    {
        [JsonPropertyName("completions")]
        public List<QuestCompletionDto> Completions { get; set; } = new();
    }

    public class CompleteQuestResponse
    {
        [JsonPropertyName("message")]
        public string Message { get; set; } = "";

        [JsonPropertyName("completion")]
        public QuestCompletionDto? Completion { get; set; }
    }
}
