using System.ComponentModel.DataAnnotations;

namespace SideQuestJar.Models
{
    // Local SQLite cache for a friend relationship belonging to the logged-in user.
    // Mirrors the Laravel `friends` pivot table but flattened for simple offline queries.
    public class FriendEntry
    {
        [Key]
        public int Id { get; set; }                  // remote User.id of the friend/requester

        public int LocalUserId { get; set; }         // FK → Users.Id (the device owner)
        public string Username { get; set; } = string.Empty;
        public string ProfilePicture { get; set; } = string.Empty;

        /// <summary>
        /// "accepted" | "pending_sent" | "pending_received"
        /// </summary>
        public string Status { get; set; } = string.Empty;
    }
}
