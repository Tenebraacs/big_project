using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Models
{
    public class Quest
    {
        public int Id { get; set; }
        public required string Name { get; set; }
        public required string Description { get; set; }
        public int Likes { get; set; }
        public int Completions { get; set; }
        public int XpGain { get; set; }
        public int JarId { get; set; }
    }
}
