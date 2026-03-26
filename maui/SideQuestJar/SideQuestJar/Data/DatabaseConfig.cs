using System;
using System.Collections.Generic;
using System.Text;

namespace SideQuestJar.Data
{
    public class DatabaseConfig
    {
        public const string DatabaseFilename = "SideQuestJarLocal.db3";

        public static string DatabasePath
        {
            get
            {
                // This ensures the database is saved in the correct secure local app data folder for Android/iOS/Windows
                var basePath = Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData);
                return Path.Combine(basePath, DatabaseFilename);
            }
        }
    }
}
