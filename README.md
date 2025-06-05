# adminpages

Simple web-based CMS with structured roles and granular permissions.

## Features
- Roles: **superadmin**, **admin**, and **user**.
- Module/function permissions (view/edit) configurable per page.
- Superadmin can manage admins and permissions for all users.
- Admins can create regular users and assign permissions.
- Responsive layout using Bootstrap 5.3 with dark/light toggle.

## Setup
Use a web server with PHP and MySQL. Import the SQL schema from `database/schema.sql` then configure the database connection in `config.php`.
