using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Models
{
    // NAMING CONVENTIONS!!!!!!!!!
    public enum PostType
    {
        PublicPost,
        PrivatePost,
        FriendsOnlyPost
    }
    public class QuestCompletion
    {
        public int Id { get; set; }
        public DateTime CompletionDate { get; set; }
        public PostType Type { get; set; }
        public required string Picture { get; set; }
        public string? Description { get; set; }
        public int Likes { get; set; }
        public int UserId { get; set; }
        public int QuestId { get; set; }
    }
}
