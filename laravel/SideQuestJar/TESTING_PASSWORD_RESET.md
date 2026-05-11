# Testing Password Reset Locally

This document explains how to test the password reset functionality in your local development environment. 

Since the application is running locally without an actual SMTP email server configured, it won't send real emails to your inbox. Instead, it is configured to write all outgoing emails directly to a log file.

## Step-by-Step Guide

### 1. Go to the Forgot Password Page
1. Ensure your local server is running (`php artisan serve`).
2. Navigate to the application in your browser.
3. Click on the **Login** button.
4. Click on the **"Forgot Your Password?"** link.

### 2. Request a Reset Link
1. Enter the email address of an existing user in your database.
2. Click the **"Send Password Reset Link"** button.
3. You should see a green success message saying the link was sent.

### 3. Find the Reset Link in the Logs
1. Open your code editor and navigate to your project folder.
2. Open the file located at: `storage/logs/laravel.log`
3. Scroll to the **very bottom** of this file. You will see a block of text that looks like a raw email.
4. Look for the action button URL in the text, which will look something like this:

   ```
   http://127.0.0.1:8000/reset-password/a1b2c3d4e5f6g7h8i9j0k...
   ```

### 4. Reset Your Password
1. Copy that URL from the log file.
2. Paste the URL into your browser's address bar and hit Enter.
3. You will be taken to the newly styled **Reset Password** page.
4. Enter your email address again, along with your new password and confirmation password.
5. Click **"Reset Password"**.

Your password has now been successfully changed, and you will be logged into your account!

---

### Note on Production
When deploying the application to a live server, you will need to update your `.env` file with actual SMTP credentials (such as Mailtrap, SendGrid, or an SMTP server) so that real emails are sent to users. 

To do this, you would change `MAIL_MAILER=log` to `MAIL_MAILER=smtp` and fill in the corresponding `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, and `MAIL_PASSWORD` variables.
