# adminpages

Simple multi-user PHP login system with Bootstrap 5.3 dark mode.

## Features
- Superadmin can create other users and assign roles.
- Per-page permissions for view and edit access.
- Responsive layout using Bootstrap 5.3 with dark/light toggle.

## Setup
Use a web server with PHP and MySQL. Create tables `admin` and `permissions`:

```sql
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(20) NOT NULL DEFAULT 'user'
);

CREATE TABLE permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  page VARCHAR(50) NOT NULL,
  can_view TINYINT(1) DEFAULT 0,
  can_edit TINYINT(1) DEFAULT 0
);
```

Configure the database connection in `config.php`.
