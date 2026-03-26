using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Models
{
    public class JarUser
    {
        public int Id { get; set; }
        public int JarId { get; set; }
        public int UserId { get; set; }
        public int Streak { get; set; }
    }
}
