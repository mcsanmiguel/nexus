# NexusTech PHP + MySQL CRUD

## Features
- Product CRUD: create, read/list, detail view, update and delete
- Search by product name, SKU or category
- Dashboard totals: product count, stock units and inventory value
- Responsive dark NexusTech UI
- PDO prepared statements, output escaping, input validation and CSRF token for POST forms
- Seed products included in `database.sql`

## Run locally with XAMPP
1. Extract `nexustech-php-crud` into `C:\xampp\htdocs\`.
2. Start Apache and MySQL.
3. Open `http://localhost/phpmyadmin`, import `database.sql`.
4. Check database credentials in `config.php` (defaults: database `nexustech_db`, user `root`, blank password).
5. Visit `http://localhost/nexustech-php-crud/index.php`.

## Run on macOS / MAMP
1. Place the folder in MAMP's document root (commonly `/Applications/MAMP/htdocs/`).
2. Start Apache and MySQL and import `database.sql` through phpMyAdmin.
3. Update `config.php` if your database port or credentials differ.
4. Visit the local URL shown by MAMP, for example `http://localhost:8888/nexustech-php-crud/`.

PHP files must be served through PHP/Apache; opening `index.php` directly as a file will not execute PHP.

## Important
This is a classroom starter project. Before public deployment, add login/authentication, role-based authorization, audit logs, backups and production error logging. Configure database credentials securely and do not expose `config.php` in a misconfigured server.
