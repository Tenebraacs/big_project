using SideQuestJar.Models;
using System.Collections.Generic;

namespace SideQuestJar.DTOs
{
    public class UserProfileResponse
    {
        public User? User { get; set; }
        public List<Jar>? MyJars { get; set; }
        public List<Jar>? JoinedJars { get; set; }
    }
}
