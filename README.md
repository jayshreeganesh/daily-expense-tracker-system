# 💰 Daily Expense Tracker Pro (SaaS Edition)

[![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-Database-4479A1?style=flat-square&logo=mysql&logoColor=white)](https://mysql.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-Reactive-8BC0D0?style=flat-square&logo=alpine.js&logoColor=white)](https://alpinejs.dev)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5-7952B3?style=flat-square&logo=bootstrap&logoColor=white)](https://getbootstrap.com)

A fully-featured, multi-tenant capable SaaS Expense Tracker engineered entirely from scratch. Built using a Custom PHP 8 MVC framework, this platform prioritizes zero-dependency architecture, iron-clad PDO security, and dynamic white-label configuration.

---

### 🌐 Live Demo
Experience the platform live: **[Launch Live Demo](https://daily-expense-tracker-system.iceiy.com/)**

**Demo Accounts (Auto-seeded & sandboxed):**
- **Admin Portal:** `demoadmin@example.com` / `demoadmin123`
- **Standard User:** `demouser@example.com` / `demouser123`

---

## ✨ Core Engineering Features

- **🚀 Zero-Config Installer:** Bypasses manual `.sql` imports or `.env` configurations. Simply run `public/setup.php` to dynamically generate routing configurations, define the database schema, and seed demo accounts regardless of strict shared-hosting constraints.
- **🎨 White-Label Branding Engine:** The installer seamlessly injects global CSS variables into the DOM to completely override Bootstrap themes based on a selected brand color. Timezones and currency symbols (`$`, `€`, `₹`) globally synchronize across all views.
- **📊 Premium Reporting Engine:** Instead of heavy backend libraries, the platform utilizes advanced `@media print` CSS directives and browser-native capabilities to generate pixel-perfect, invoice-style **PDF Reports** and fast **CSV Exports**.
- **🛡️ Custom MVC & Security:** Operates on a bespoke Model-View-Controller framework. Features strict Role-Based Access Control (RBAC), immutable Audit Logs, Bcrypt password hashing, and total SQL Injection prevention via strict PDO Prepared Statements.

## 🛠️ Technology Stack
- **Backend Architecture:** Core PHP 8 (Custom MVC, Zero Composer Dependencies)
- **Database:** MySQL / MariaDB (Relational Architecture)
- **Frontend Interactivity:** Alpine.js (Reactive states)
- **UI Framework:** Bootstrap 5 (Responsive layouts)
- **Data Visualization:** Chart.js

## 🚀 Quick Start (Installation)
1. **Upload** the repository to your web server (e.g., Apache `public_html` or XAMPP `htdocs`).
2. **Navigate** your browser to the wizard: `http://yourdomain.com/public/setup.php`
3. **Configure** your database credentials, SaaS Brand Color, and Default Categories via the UI.
4. **Click Install** — The system will automatically build your configuration file, seed the database, lock the installer, and redirect you to your newly deployed SaaS platform!

## 📚 Technical Documentation
To dive deep into the architectural choices, security model, and database schema, please visit the official [Project Wiki](https://github.com/jayshreeganesh/daily-expense-tracker-system/wiki).
