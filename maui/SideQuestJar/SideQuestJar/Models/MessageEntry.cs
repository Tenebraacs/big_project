using System;
using System.ComponentModel.DataAnnotations;

namespace SideQuestJar.Models
{
    // Local SQLite cache for a message or invitation snapshot.
    public class MessageEntry
    {
        [Key]
        public int LocalId { get; set; } // Auto-increment primary key for local DB

        public int RemoteId { get; set; } // The ID from Laravel (Message ID or Invitation ID)
        
        public int LocalUserId { get; set; } // The current logged-in user (owner of this cache)
        
        public int FriendId { get; set; } // The other person in the conversation
        
        public int SenderId { get; set; } // Who actually sent it
        
        public string Content { get; set; } = string.Empty;
        
        public string Type { get; set; } = "chat"; // "chat" | "invite"
        
        public DateTime CreatedAt { get; set; }
        
        public DateTime? ReadAt { get; set; }

        // Invitation specific fields (cached snapshots)
        public string? JarName { get; set; }
        public string? JarThumb { get; set; }
        public string? InviteStatus { get; set; }
    }
}
