using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Models
{
    /*!!!!!!!
     Itt a C# 'naming conventions'-ei miatt PascalCase-ben vannak az enum értékek, 
     de az API-nak később snake_case-ben kell egy JsonConverter-rel megadni.
    !!!!!!!*/
    public enum JarType
    {
        PublicJar,
        PrivateJar,
        FriendsOnlyJar,
        InviteOnlyJar,
        OfficialJar
    }

    public class Jar
    {
        public int Id { get; set; }
        public required string Name { get; set; }
        public string? Description { get; set; }
        public string? Picture { get; set; }
        public int Likes { get; set; }
        public JarType Type { get; set; }
        public int CreatedBy { get; set; }
    }
}
