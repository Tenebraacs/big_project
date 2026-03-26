using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Models
{
    public class User
    {
        // Ez a class tárolja az aktuálisan bejelentkezett felhasználót
        public int Id { get; set; }
        public required string Username { get; set; }
        public string? Nickname { get; set; }
        public required string Fullname { get; set; }
        public required string Email { get; set; }
        public string? ProfilePicture { get; set; }
        public DateTime BirthDate { get; set; }
        public DateTime JoinedDate { get; set; }
        public int UserXp { get; set; }
        public bool IsCustomized { get; set; }
        public bool IsAdmin { get; set; }
    }
}
