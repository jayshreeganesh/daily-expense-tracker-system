# Daily Expense Tracker Pro (PHP MVC)

A professional, responsive, and dynamic Expense Tracker built from scratch using a Custom PHP MVC framework, MySQL, Bootstrap 5, and Alpine.js. This MVP is designed to be highly presentable for a portfolio, showcasing a deep understanding of core programming concepts, design patterns, and modern frontend interactivity.

## 🌟 Key Features

1. **Custom MVC Architecture**: Hand-coded Model-View-Controller framework without relying on heavy libraries like Laravel or CodeIgniter.
2. **User Authentication**: Secure login and registration with password hashing (Bcrypt) and session management.
## 🚀 Core Features
- **User Authentication**: Secure registration and login using PHP `password_hash` and `password_verify`.
- **Expense & Income Tracking**: Easily record transactions, categorize them, and specify the date and amount.
- **Dynamic Dashboard**: Visualize your financial flow with interactive **Chart.js** graphs.
- **Pagination & Filtering**: Efficiently browse transaction history with date filters and pagination.
- **Bill Reminders**: Set upcoming bill reminders, track due dates, and mark them as paid.
- **Export Data**: Download your transaction history instantly as a `.csv` file.

## 👑 Enterprise SaaS Features (Super Admin Panel)
- **Role-Based Access Control (RBAC)**: Supports `User`, `Recruiter` (Read-Only Admin), and `Super Admin` roles.
- **Audit Logging System**: The backend silently tracks and logs all critical database interactions for accountability.
- **Bulk CSV Importing**: Admins can download a CSV template and upload it to bulk-insert transactions.
- **System Operations Engine**: 
  - One-click MySQL Database Backup `.sql` downloads.
  - One-click UI button to Seed Demo Data for testing.
  - One-click UI button to export the entire project source code into a clean `.zip`.

## 🛠️ Technology Stack
- **Backend**: PHP 8.x (Custom MVC Architecture)
- **Database**: MySQL (PDO Wrapper)
- **Frontend**: HTML5, CSS3, Bootstrap 5, Alpine.js
- **Visuals**: Chart.js
- **Security**: CSRF Protection Tokens, Prepared Statements (SQLi prevention), XSS sanitization.

## 📥 Installation & Setup
1. **Clone the repository:**
   ```bash
   git clone <your-repo-url>
   ```
2. **Database Setup:**
   - Create a MySQL database named `expense_tracker`.
   - Run the provided `.sql` migrations or trigger the seeding script.
3. **Configure the App:**
   - Open `app/config/config.php` and update `DB_USER` and `DB_PASS` to match your local environment.
   - Ensure `URLROOT` is set correctly (e.g., `http://localhost:8080`).
4. **Run the Server:**
   ```bash
   php -S localhost:8080 -t public
   ```

## 🔒 Demo Credentials
- **Super Admin**: `admin@example.com` / `admin123`
- **Recruiter Demo**: `demo_admin@example.com` / `demoadmin123`
- **Standard User**: `user@example.com` / `user123`

### Starting the Server

**Option A: PHP Built-in Server (Recommended for quick testing)**
Run the included router script from the root of the project:
```bash
php -S localhost:8080 server.php
```
Then navigate to `http://localhost:8080` in your browser.

**Option B: Apache (XAMPP/WAMP)**
Place the project folder in your `htdocs` or `www` directory. Start the Apache and MySQL services, then navigate to `http://localhost/daily-expense-tracker-system/`. The included `.htaccess` files will automatically route traffic.

## 📁 Folder Structure

- `app/` - Contains all core MVC files (Controllers, Models, Views, config, helpers).
- `public/` - The document root containing `index.php` (Front Controller), CSS, JS, and assets.
- `database.sql` - The database schema for easy setup.
- `server.php` - Custom router for the PHP built-in server.
