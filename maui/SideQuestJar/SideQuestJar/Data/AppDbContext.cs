using Microsoft.EntityFrameworkCore;
using SideQuestJar.Models;
using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Data
{
    public class AppDbContext : DbContext
    {
        public DbSet<User> Users { get; set; }
        public DbSet<Jar> Jars { get; set; }
        public DbSet<JarUser> JarUsers { get; set; }
        public DbSet<JarQuest> Quests { get; set; }
        public DbSet<QuestCompletion> QuestCompletions { get; set; }
        public DbSet<Like> Likes { get; set; }
        public DbSet<Library> Libraries { get; set; }
        public DbSet<Achievements> Achievements { get; set; }
        public DbSet<AchievementCompletion> AchievementCompletions { get; set; }

        public AppDbContext()
        {
            Database.EnsureCreated();
        }

        protected override void OnConfiguring(DbContextOptionsBuilder optionsBuilder)
        {
            string dbPath = DatabaseConfig.DatabasePath;
            optionsBuilder.UseSqlite($"Filename={dbPath}");
        }
    }
}
