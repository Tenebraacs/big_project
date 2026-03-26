using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Models
{
    // Szintén figyelni kell a 'naming conventions'-re mint a Jar-nál
    public enum LikeType
    {
        Jar,
        Quest,
        Completion
    }
    public class Like
    {
        public int Id { get; set; }
        public int UserId { get; set; }
        public LikeType Type { get; set; }
        public int? JarId { get; set; }
        public int? QuestId { get; set; }
        public int? CompletionId { get; set; }
    }
}
