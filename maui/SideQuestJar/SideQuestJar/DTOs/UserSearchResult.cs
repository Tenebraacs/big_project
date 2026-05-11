namespace SideQuestJar.DTOs
{
    // Represents a single user returned by the /api/users/search endpoint
    public class UserSearchResult
    {
        public int Id { get; set; }
        public string Username { get; set; } = string.Empty;
        public string? ProfilePicture { get; set; }
        public string? ProfilePictureUrl { get; set; }
    }
}
