# Project Setup Guide

This guide outlines the steps required to set up and run this project on a new PC after cloning it from the Git repository.

## PC Configuration (Windows / XAMPP)

Assuming you already have XAMPP, Composer, Node.js, and Git installed, you need to configure your PC before setting up the project:

### 1. Add PHP to System PATH
To run PHP commands (like `php artisan`) in your terminal, add PHP to your Windows PATH:
1. Search for "Environment Variables" in your Windows Start menu and open "Edit the system environment variables".
2. Click "Environment Variables...".
3. Under "System variables", find and select the `Path` variable, then click "Edit".
4. Click "New" and add the path to your XAMPP PHP directory (usually `C:\xampp\php`).
5. Click OK on all windows to save.

### 2. Enable Required PHP Extensions
You need to enable specific PHP extensions in XAMPP for the project to function correctly:
1. Open your XAMPP Control Panel.
2. Click the "Config" button next to Apache and select `php.ini`.
3. Find and uncomment (remove the `;` at the beginning of the line) the following extensions if they are commented out:
   - `extension=gd` *(Required for the image cropper functionality)*
   - `extension=fileinfo` *(Required for file uploads)*
   - `extension=pdo_sqlite` and `extension=sqlite3` *(Required for the default SQLite database)*
   - `extension=zip` *(Required by some Composer packages)*
4. Save the `php.ini` file.

*(Note: After making these changes, you may need to restart your terminal or PC for the PATH changes to take effect, and restart Apache in XAMPP for the extension changes to apply.)*

---

## Project Setup Instructions

Follow these steps in your terminal, starting from the root directory of the cloned project:

### 1. Install PHP Dependencies
Run Composer to install all the backend packages required by Laravel.
```bash
composer install
```

### 2. Install Node.js Dependencies
Run npm to install the frontend packages.
```bash
npm install
```

### 3. Set Up Environment Configuration
Copy the example environment file to create your local `.env` configuration file.
*(For Windows PowerShell / Command Prompt)*
```cmd
copy .env.example .env
```
*(For Linux / macOS / Git Bash)*
```bash
cp .env.example .env
```

### 4. Generate Application Key
Generate a unique application key. This key is used for encrypting user sessions and other sensitive data.
```bash
php artisan key:generate
```

### 5. Database Setup & Migrations
This project defaults to using an SQLite database. 
Run the database migrations to create the necessary tables. When prompted to create the database file (e.g., `database/database.sqlite`), select "yes".
```bash
php artisan migrate
```
*(Optional)* If the project has initial dummy data you want to load, you can run the seeders:
```bash
php artisan migrate:fresh --seed
```

### 6. Create Storage Link
Create a symbolic link from `public/storage` to `storage/app/public` so that uploaded files are publicly accessible.
```bash
php artisan storage:link
```

### 7. Compile Frontend Assets
Build your Vite assets for development. This command will watch for changes and automatically reload the browser.
```bash
npm run dev
```

### 8. Start the Local Development Server
Open a **new terminal tab/window** (leaving the `npm run dev` running) and start the Laravel backend server.
```bash
php artisan serve
```

---

## Accessing the Application
Once both the Vite and Laravel servers are running, you can access your application in your web browser at:
**http://127.0.0.1:8000**
