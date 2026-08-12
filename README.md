# Daily Expense Tracker Pro (PHP MVC)

A professional, responsive, and dynamic Expense Tracker built from scratch using a Custom PHP MVC framework, MySQL, Bootstrap 5, and Alpine.js. This MVP is designed to be highly presentable for a portfolio, showcasing a deep understanding of core programming concepts, design patterns, and modern frontend interactivity.

## 🌟 Key Features

1. **Custom MVC Architecture**: Hand-coded Model-View-Controller framework without relying on heavy libraries like Laravel or CodeIgniter.
2. **User Authentication**: Secure login and registration with password hashing (Bcrypt) and session management.
3. **Interactive Dashboard**: Overview of Total Income, Total Expenses, and Net Balance with dynamic color coding and recent transaction history.
4. **Category Management**: Create and manage custom categories (e.g., Food, Rent, Salary) assigning custom color codes and types.
5. **Transaction Management (CRUD)**: Log new incomes and expenses linked to specific categories.
6. **Alpine.js Interactivity**: Modern, fast UI with instant pop-up modals for adding categories and transactions without page reloads.
7. **Pro Export Feature**: Export your transactions into a clean `.csv` format instantly.
8. **Dynamic Server Compatibility**: Built-in routing works seamlessly across Apache, Nginx, and the PHP Built-in Server.

## 🛠️ Tech Stack

- **Backend**: Custom PHP MVC (Object-Oriented PHP)
- **Database**: MySQL (PDO for secure, parameterized database interactions)
- **Frontend Styling**: Bootstrap 5 (CSS Framework for responsive UI)
- **Frontend Logic**: Alpine.js (Lightweight JavaScript for modals and reactive UI)

## 🚀 How to Run Locally

### Prerequisites
- PHP 8.0+
- MySQL Server (e.g., XAMPP, WAMP, Laragon, or standalone)

### Installation & Setup

1. **Clone the repository**:
   ```bash
   git clone https://github.com/yourusername/daily-expense-tracker-system.git
   cd daily-expense-tracker-system
   ```

2. **Database Setup**:
   - Create a new MySQL database named `expense_tracker`.
   - Import the provided `database.sql` file into your MySQL database to create the `users`, `categories`, and `transactions` tables.

3. **Configuration (Optional)**:
   - The application automatically configures the Base URL. 
   - If your database password is not `root`, update the database credentials in `app/config/config.php`.

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
