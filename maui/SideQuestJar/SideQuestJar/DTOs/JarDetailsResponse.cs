using SideQuestJar.Models;
using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace SideQuestJar.DTOs
{
    public class JarDetailsResponse
    {
        // Ne felejtsem el majd említeni a BFF iránnyelvet a dokumentációban (Backend For Frontend)
        [JsonPropertyName("jar")]
        public Jar? Jar { get; set; }

        [JsonPropertyName("quests")]
        public List<JarQuest> Quests { get; set; } = new();

        [JsonPropertyName("is_joined")]
        public bool IsJoined { get; set; }
    }
}
