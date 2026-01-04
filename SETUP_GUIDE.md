# 🚀 SPARK - Setup Guide

## Quick Setup After Cloning

Setiap kali clone project ini ke device baru, ikuti langkah berikut:

### 1. Import Database
```bash
# Import database dasar
mysql -u root -p spark < "spark (2).sql"
```

Atau via phpMyAdmin:
1. Buat database bernama `spark`
2. Import file `spark (2).sql`

### 2. Run Database Setup Script
Ini akan memastikan semua kolom dan tabel yang dibutuhkan ada:

**Via Browser:**
```
http://localhost/spark/database/setup.php
```

**Via CLI:**
```bash
php database/setup.php
```

### 3. Update Config
Edit `config/database.php` sesuai environment Anda:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'spark');
```

### 4. Set Permissions (Linux/Mac)
```bash
chmod 777 uploads/
chmod 777 uploads/profile/
```

## 🔧 Troubleshooting

### Error: Column not found
Jika muncul error "Column not found" (seperti `foto_tempat`):
1. Jalankan `database/setup.php` 
2. Script akan otomatis menambahkan kolom yang hilang

### Error: Table doesn't exist
1. Pastikan database `spark` sudah dibuat
2. Import `spark (2).sql`
3. Jalankan `database/setup.php`

## 📋 Required Tables & Columns

Setup script akan memastikan ini semua ada:

### tempat_parkir
- ✓ `foto_tempat` VARCHAR(255)
- ✓ `is_plat_required` TINYINT(1)

### kendaraan_pengguna  
- ✓ `plat_hash` CHAR(64)
- ✓ `plat_hint` VARCHAR(10)

### booking_parkir
- ✓ `qr_secret` CHAR(64)

### Additional Tables
- ✓ `qr_session`
- ✓ `tickets`
- ✓ `user_preferences`
- ✓ `wallet_payment_methods`

### data_pengguna
- ✓ `profile_image` VARCHAR(255)

## 🐳 Docker Setup (Optional)

Jika menggunakan Docker:
```bash
docker-compose up -d
docker exec -it spark-db mysql -u root -p spark < "spark (2).sql"
docker exec -it spark-web php database/setup.php
```

## 📝 Environment Variables

Buat file `.env` (optional):
```env
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=spark
BASEURL=http://localhost/spark
SECRET_SALT=your-random-secret-salt-here
```

## ⚡ Quick Commands

```bash
# Setup everything at once
php database/setup.php

# Check database status
mysql -u root -p spark -e "SHOW TABLES;"

# Verify columns
mysql -u root -p spark -e "SHOW COLUMNS FROM tempat_parkir;"
```

## 🎯 Login Credentials

**Admin:**
- URL: `http://localhost/spark/admin/login.php`
- Email: (check `data_pengguna` table where `role_pengguna = 2`)

**User:**
- URL: `http://localhost/spark/pages/login.php`
- Register: `http://localhost/spark/pages/register.php`

## 📞 Support

Jika masih ada masalah:
1. Check error log: browser console atau `php error_log`
2. Pastikan PHP >= 8.0
3. Pastikan MySQL/MariaDB running
4. Jalankan `database/setup.php` sekali lagi
