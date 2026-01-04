# 📦 Installation Guide - SPARK

Complete guide to install SPARK on your local or production environment.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Quick Installation](#quick-installation)
- [Manual Installation](#manual-installation)
- [Docker Installation](#docker-installation)
- [Verification](#verification)

---

## Prerequisites

### Required Software
- **PHP** >= 8.0
  - Extensions: `pdo`, `pdo_mysql`, `mbstring`, `openssl`, `gd`
- **MySQL** >= 5.7 or **MariaDB** >= 10.2
- **Web Server**: Apache, Nginx, or PHP built-in server
- **Composer** (optional, for dependency management)
- **Git** (for cloning repository)

### Optional
- **Node.js** & npm (if you plan to modify frontend assets)
- **Docker** & Docker Compose (for containerized setup)

### Check Your Environment

```bash
# Check PHP version
php -v

# Check PHP extensions
php -m | grep -E "pdo|mysql|mbstring|openssl|gd"

# Check MySQL/MariaDB
mysql --version

# Check Git
git --version
```

---

## Quick Installation

### For Linux/Mac Users

```bash
# 1. Clone repository
git clone https://github.com/yourusername/spark.git
cd spark

# 2. Run setup script
bash setup.sh
```

### For Windows Users

```batch
REM 1. Clone repository
git clone https://github.com/yourusername/spark.git
cd spark

REM 2. Run setup script
setup.bat
```

The setup script will:
- ✅ Import database
- ✅ Check and fix schema
- ✅ Create necessary directories
- ✅ Set proper permissions

---

## Manual Installation

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/spark.git
cd spark
```

### Step 2: Create Database

```bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE spark CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
EXIT;
```

### Step 3: Import Database Schema

```bash
# Import main database
mysql -u root -p spark < "spark (2).sql"
```

### Step 4: Configure Database Connection

Edit `config/database.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');           // Your MySQL username
define('DB_PASS', '');               // Your MySQL password
define('DB_NAME', 'spark');          // Database name
define('DB_PORT', '3306');           // MySQL port (usually 3306)
define('DB_CHARSET', 'utf8mb4');
```

### Step 5: Run Database Setup

This step is **CRITICAL** - it ensures all tables and columns are present:

**Option A: Via Browser**
```
http://localhost:8000/database/setup.php
```

**Option B: Via Command Line**
```bash
php database/setup.php
```

Expected output:
```
✓ Database connection successful
✓ foto_tempat column exists
✓ is_plat_required column exists
✓ plat_hash column exists
✓ qr_session table exists
✓ tickets table exists
=== Database Setup Complete ===
```

### Step 6: Set Permissions

**Linux/Mac:**
```bash
chmod -R 755 .
chmod -R 777 uploads/
```

**Windows:**
- Right-click on `uploads` folder
- Properties → Security → Edit
- Give "Full Control" to your user

### Step 7: Configure Application

Edit `config/config.php` if needed:

```php
// Base URL (usually auto-detected)
define('BASEURL', 'http://localhost:8000');

// Security salt (change this!)
define('SECRET_SALT', 'your-random-secret-salt-here-change-me');
```

### Step 8: Start Server

**Option A: PHP Built-in Server (Development)**
```bash
php -S localhost:8000
```

**Option B: Apache/Nginx**
- Copy project to web root (e.g., `/var/www/html/spark` or `C:\xampp\htdocs\spark`)
- Access via: `http://localhost/spark`

**Option C: XAMPP/MAMP/WAMP**
- Copy to htdocs folder
- Start Apache and MySQL
- Access via: `http://localhost/spark`

---

## Docker Installation

### Prerequisites
- Docker Desktop installed
- Docker Compose installed

### Quick Start

```bash
# 1. Clone repository
git clone https://github.com/yourusername/spark.git
cd spark

# 2. Start containers
docker-compose up -d

# 3. Import database
docker exec -it spark-db mysql -u root -pspark spark < "spark (2).sql"

# 4. Run setup
docker exec -it spark-web php database/setup.php
```

### Docker Configuration

The `docker-compose.yml` includes:
- **Web Server**: PHP 8.0 with Apache
- **Database**: MySQL 8.0
- **phpMyAdmin**: Database management interface

Access points:
- Application: http://localhost:8080
- phpMyAdmin: http://localhost:8081

### Stop Containers

```bash
docker-compose down
```

---

## Verification

### 1. Check Database Connection

Visit:
```
http://localhost:8000/test-db.php
```

Expected: "Database connection successful"

### 2. Check Homepage

Visit:
```
http://localhost:8000
```

Should see SPARK homepage with parking locations

### 3. Test User Registration

```
http://localhost:8000/pages/register.php
```

Create a test account and try logging in

### 4. Test Admin Panel

```
http://localhost:8000/admin/login.php
```

Default admin credentials (check database):
- Query: `SELECT * FROM data_pengguna WHERE role_pengguna = 2`

---

## Post-Installation

### Create Admin Account

If no admin exists:

```sql
-- Login to MySQL
mysql -u root -p spark

-- Create admin user
INSERT INTO data_pengguna (
    role_pengguna, 
    nama_pengguna, 
    email_pengguna, 
    password_pengguna
) VALUES (
    2,                                              -- role 2 = admin
    'Admin',                                        -- name
    'admin@spark.com',                              -- email
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'  -- password: "password"
);
```

### Update Security Settings

1. Change `SECRET_SALT` in `config/config.php`
2. Use strong admin password
3. Set appropriate file permissions

### Optional: Install Composer Dependencies

```bash
composer install
```

---

## Troubleshooting

### Error: "Column not found"
```bash
php database/setup.php
```

### Error: "Database connection failed"
- Check credentials in `config/database.php`
- Ensure MySQL is running: `sudo systemctl status mysql`

### Error: "Permission denied" for uploads
```bash
chmod -R 777 uploads/
```

### Error: 404 on all pages
- Check `.htaccess` file exists
- Enable Apache `mod_rewrite`:
  ```bash
  sudo a2enmod rewrite
  sudo systemctl restart apache2
  ```

### Port 8000 already in use
```bash
# Use different port
php -S localhost:8080

# Or find and kill process
lsof -ti:8000 | xargs kill -9
```

---

## Next Steps

- Read [Configuration Guide](CONFIGURATION.md)
- Understand [Project Structure](STRUCTURE.md)
- Check [Troubleshooting Guide](TROUBLESHOOTING.md)
- Review [API Documentation](API.md)

---

## Support

If you encounter issues:
1. Check [TROUBLESHOOTING.md](TROUBLESHOOTING.md)
2. Run `php database/setup.php`
3. Check PHP error logs
4. Open an issue on GitHub

---

**Installation Time**: ~10 minutes  
**Difficulty**: Beginner-friendly  
**Support**: Active community
