using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Models
{
    public class Achievements
    {
        public int Id { get; set; }
        public required string Name { get; set; }
        public string? Description { get; set; }
        public required string Picture { get; set; }
        public int UserCompletion { get; set; }
        public int Xp { get; set; }
    }
}
