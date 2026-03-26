using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace SideQuestJar.DTOs
{
    internal class ValidationErrorResponse
    {
        [JsonPropertyName("message")]
        public required string Message { get; set; }

        [JsonPropertyName("errors")]
        public required Dictionary<string, string[]> Errors { get; set; }
    }
}
