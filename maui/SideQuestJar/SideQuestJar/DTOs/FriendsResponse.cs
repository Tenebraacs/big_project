using System.Collections.Generic;

namespace SideQuestJar.DTOs
{
    // BFF: single aggregated API response for the friends page
    // (friends + pending_received + pending_sent in one round-trip)
    public class FriendsResponse
    {
        public List<FriendDto> Friends { get; set; } = new();
        public List<FriendDto> PendingReceived { get; set; } = new();
        public List<FriendDto> PendingSent { get; set; } = new();
    }

    public class FriendDto
    {
        public int Id { get; set; }
        public string Username { get; set; } = string.Empty;
        public string ProfilePicture { get; set; } = string.Empty;
        public string? ProfilePictureUrl { get; set; }
    }
}
