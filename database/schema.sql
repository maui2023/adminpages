CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('superadmin','admin','user') NOT NULL DEFAULT 'user',
  level_id INT DEFAULT NULL
);

CREATE TABLE pages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE modules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE page_modules (
  id INT AUTO_INCREMENT PRIMARY KEY,
  page_id INT NOT NULL,
  module_id INT NOT NULL,
  UNIQUE KEY uniq_page_module (page_id, module_id)
);

CREATE TABLE permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  page VARCHAR(50) NOT NULL,
  module VARCHAR(50) NOT NULL,
  can_view TINYINT(1) DEFAULT 0,
  can_edit TINYINT(1) DEFAULT 0,
  UNIQUE KEY uniq_perm (user_id, page, module)
);

CREATE TABLE user_levels (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE user_level_permissions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  level_id INT NOT NULL,
  page VARCHAR(50) NOT NULL,
  module VARCHAR(50) NOT NULL,
  can_view TINYINT(1) DEFAULT 0,
  can_edit TINYINT(1) DEFAULT 0,
  UNIQUE KEY uniq_ul_perm (level_id, page, module)
);

INSERT INTO pages (name) VALUES ('page_a'), ('page_b'), ('page_c');
INSERT INTO modules (name) VALUES ('dashboard'), ('application'), ('letter');
