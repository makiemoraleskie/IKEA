# IKEA Cakes and Snacks Commissary Inventory and Purchaser Transaction Tracking System

This repository contains a PHP (OOP, MVC) web application for centralized inventory and purchaser transaction tracking for IKEA Cakes and Snacks Commissary (Ormoc City). It replaces manual paper-based processes with secure, auditable digital workflows.

## Stack
- PHP 8+, Apache (XAMPP)
- MySQL 8+
- Tailwind CSS (CDN)
- JavaScript
- Chart.js (for reports)

## Quick Start (Local - XAMPP)
1. Clone into `C:/xampp/htdocs/IKEA` (Windows).
2. Ensure Apache and MySQL are running.
3. Create a MySQL database (e.g. `ikea_commissary`).
4. Import `database.sql` (once available) or create tables manually.
5. Configure DB credentials in `config/db.php`.
6. Visit `http://localhost/IKEA/` in your browser.

## Structure
```
root/
 ├── index.php                # Front controller + router bootstrap
 ├── .htaccess                # Pretty URLs
 ├── config/
 │    └── db.php              # PDO connection (prepared statements)
 ├── public/
 │    ├── css/
 │    ├── js/
 │    │    └── app.js
 │    └── uploads/            # Receipt uploads (secured by checks)
 ├── src/
 │    ├── controllers/
 │    │    ├── BaseController.php
 │    │    ├── AuthController.php
 │    │    └── DashboardController.php
 │    ├── models/
 │    │    ├── BaseModel.php
 │    │    ├── User.php
 │    │    └── AuditLog.php
 │    ├── utils/
 │    │    ├── Csrf.php
 │    │    └── Auth.php
 │    └── views/
 │         ├── auth/login.php
 │         └── dashboard/index.php
 ├── includes/
 │    ├── header.php
 │    └── footer.php
 ├── routes/
 │    └── web.php
 ├── assets/
 │    ├── README.md
 │    └── .gitkeep
 └── README.md
```

## Security
- Passwords hashed with `password_hash`/`password_verify` (bcrypt/argon2i as available)
- All queries via PDO prepared statements
- CSRF tokens on all forms (session-backed)
- Basic input validation/sanitization
- Role-based access checks

## Next Steps
- Add full schema (`database.sql`) with FKs and indexes
- Implement feature modules (Requests, Inventory, Purchases with uploads, Deliveries, Reports)
- Add Chart.js dashboards and Dompdf for PDF export

## License
Proprietary - internal use for IKEA Cakes and Snacks Commissary.