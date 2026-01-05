# 🚀 SPARK - Setup Guide

## ⚡ Setup Cepat dengan Docker (Recommended)

Ini adalah cara termudah untuk setup project di laptop orang lain atau device baru.

### Prerequisites (Install dulu)
- **Docker Desktop** → https://www.docker.com/products/docker-desktop
- **Git** → https://git-scm.com/

### Langkah Setup:

#### 1. Clone Repository
```bash
git clone <repo-url> spark
cd spark
```

#### 2. Start Docker
```bash
# Build dan run semua containers
docker-compose up -d

# Tunggu ~30 detik sampai database selesai initialize
```

#### 3. Verify Containers Running
```bash
docker-compose ps
```

Harus ada 3 containers:
- `spark-app` (PHP/Apache) → Port 8080
- `spark-db` (MariaDB) → Port 3308
- `spark-pma` (phpMyAdmin) → Port 8081

#### 4. Database Auto-Import
Database `spark` akan **otomatis** di-import dari file `spark (2).sql` saat container pertama kali running.

Tunggu ~30 detik untuk proses selesai.

#### 5. Access Aplikasi
- **Main App:** http://localhost:8080
- **Admin Panel:** http://localhost:8080/admin/login.php
- **phpMyAdmin:** http://localhost:8081

#### 6. Test Login (Optional)
Masuk ke admin panel dengan credentials:
```
Email: admin@spark.local
Password: (lihat di database/000-complete-setup.sql)
```

### ✅ Setup Selesai!
Project siap digunakan. Semua data sudah terkoneksi dengan database.

---

## 🔧 Setup Manual (Tanpa Docker)

Jika tidak ingin pakai Docker, ikuti langkah berikut:

### Prerequisites
- PHP 8.2+
- MySQL/MariaDB 10.4+
- Apache/Nginx
- Composer (optional)

### Langkah Setup:

#### 1. Clone Repository
```bash
git clone <repo-url> spark
cd spark
```

#### 2. Create Database
```bash
# Via MySQL CLI
mysql -u root -p
> CREATE DATABASE spark;
> EXIT;

# Atau buat database kosong, akan di-import di step 3
```

#### 3. Import Database
```bash
# Import script setup lengkap
mysql -u root -p spark < database/000-complete-setup.sql

# Atau import database lama (jika ada)
mysql -u root -p spark < "spark (2).sql"
```

#### 4. Update Config
Edit `config/database.php` sesuai credentials Anda:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your-password');
define('DB_NAME', 'spark');
define('BASEURL', 'http://localhost/spark/public/');
```

#### 5. Set Folder Permissions
```bash
# Linux/Mac
chmod -R 777 uploads/
chmod -R 777 uploads/profile/
```

#### 6. Access Aplikasi
- **Main App:** http://localhost/spark/public/
- **Admin Panel:** http://localhost/spark/public/admin/login.php
- **phpMyAdmin:** http://localhost/phpmyadmin

#### 7. Test Login
```
Email: admin@spark.local
Password: (check database/000-complete-setup.sql for hash)
```

---

## 🐛 Troubleshooting

### ❌ Error: PDOException or "Column not found"
**Solution:**
1. Run: `php database/setup.php`
2. Or import fresh database: `mysql -u root -p spark < database/000-complete-setup.sql`
3. Check `config/database.php` is configured correctly

### ❌ Error: Images not showing
**Solution:**
1. Check image path in browser console (F12)
2. Verify images exist: `ls assets/img/parking-area/`
3. Check BASEURL in `config/app.php` has trailing slash
4. Clear browser cache (Ctrl+Shift+Delete)

### ❌ Docker port already in use
**Solution:**
```bash
# Change ports in docker-compose.yml
# Or stop conflicting container
docker-compose down
docker ps  # check other running containers
```

### ❌ Database not initialized in Docker
**Solution:**
```bash
# Restart database container
docker-compose restart db
# Wait 30 seconds
# Check if database exists
docker exec spark-db mysql -u root -prootpassword -e "SHOW DATABASES;"
```

### ❌ Permission denied on uploads
**Solution (Docker):**
```bash
# Already handled in Dockerfile
# Check if /var/www/html/uploads is writable
docker exec spark-app ls -la /var/www/html/uploads
```

**Solution (Manual):**
```bash
chmod 777 uploads/
chmod 777 uploads/profile/
chown www-data:www-data uploads/  # If using Apache
```

---

## 📋 Database Schema Checklist

Semua tabel & kolom ini harus ada:

### Core Tables
- ✅ `role_pengguna` - Roles (user, owner, admin)
- ✅ `data_pengguna` - Users with profile_image
- ✅ `jenis_kendaraan` - Vehicle types
- ✅ `tempat_parkir` - Parking locations with foto_tempat, is_plat_required
- ✅ `slot_parkir` - Parking slots
- ✅ `kendaraan_pengguna` - User vehicles with plat_hash
- ✅ `booking_parkir` - Bookings with qr_secret
- ✅ `pembayaran_booking` - Payments (metode, status)
- ✅ `harga_parkir` - Pricing
- ✅ `ulasan_tempat` - Reviews/ratings
- ✅ `scan_history` - Check-in/out logs
- ✅ `tiket_digital` - Digital tickets
- ✅ `dompet_pengguna` - User wallets
- ✅ `metode_pembayaran` - Payment methods
- ✅ `history_transaksi` - Transaction history
- ✅ `qr_session` - QR session tracking
- ✅ `wallet_methods` - Wallet payment methods
- ✅ `preferensi_pengguna` - User preferences

---

## 📁 Project Structure

```
spark/
├── config/               # Configuration files
│   ├── app.php
│   ├── config.php
│   └── database.php
├── database/             # Database scripts
│   ├── setup.php
│   └── 000-complete-setup.sql (MAIN SETUP)
├── public/               # Web root
│   ├── index.php
│   └── admin/
├── includes/             # Reusable components
│   └── bookpark.php
├── functions/            # Helper functions
├── assets/               # CSS, JS, images
│   ├── css/
│   ├── js/
│   └── img/parking-area/ (parking images)
├── docker-compose.yml    # Docker configuration
├── Dockerfile            # Docker build config
└── .gitignore

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
