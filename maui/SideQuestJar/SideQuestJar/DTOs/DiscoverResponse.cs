using SideQuestJar.Models;
using System;
using System.Collections.Generic;
using System.Text;
using System.Text.Json.Serialization;

namespace SideQuestJar.DTOs
{
    public class DiscoverResponse
    {
        [JsonPropertyName("mainJar")]
        public Jar? MainJar { get; set; }

        [JsonPropertyName("mainQuest")]
        public JarQuest? MainQuest { get; set; }

        [JsonPropertyName("popularJars")]
        public List<Jar> PopularJars { get; set; } = new();

        [JsonPropertyName("recommendedJars")]
        public List<Jar> RecommendedJars { get; set; } = new();
    }
}
