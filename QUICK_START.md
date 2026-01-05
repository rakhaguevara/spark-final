# 🚀 SPARK - Quick Clone & Setup Guide

Clone project ini ke laptop orang lain dengan mudah dalam 3 opsi:

---

## ⭐ OPSI 1: Docker (PALING MUDAH) ⭐

**Prasyarat:**
- Docker Desktop (https://docker.com/products/docker-desktop)
- Git (https://git-scm.com)

**Langkah:**
```bash
# 1. Clone repo
git clone <repo-url> spark
cd spark

# 2. Jalankan Docker
docker-compose up -d

# 3. Tunggu 30 detik untuk database initialize

# 4. Buka browser
# App: http://localhost:8080
# Admin: http://localhost:8080/admin/login.php
# phpMyAdmin: http://localhost:8081
```

**Keuntungan:**
✅ Semua service sudah pre-configured  
✅ Database auto-import  
✅ Tidak perlu install PHP, MySQL, Apache  
✅ Sama persis di semua device  
✅ Cukup `docker-compose up -d` untuk start  

**Stop:**
```bash
docker-compose down
```

---

## 🔧 OPSI 2: Manual Setup (Local)

**Prasyarat:**
- PHP 8.0+
- MySQL/MariaDB
- Apache/Nginx

**Langkah:**
```bash
# 1. Clone
git clone <repo-url> spark
cd spark

# 2. Buat database
mysql -u root -p
> CREATE DATABASE spark;
> exit;

# 3. Import data
mysql -u root -p spark < database/000-complete-setup.sql

# 4. Update config/database.php
# Ubah DB_HOST, DB_USER, DB_PASS sesuai setup lokal

# 5. Set permissions (Linux/Mac)
chmod -R 777 uploads/

# 6. Akses
# http://localhost/spark/public
```

**Keuntungan:**
✅ Kontrol penuh atas konfigurasi  
✅ Tidak perlu Docker  
✅ Lebih cepat jika sudah install PHP/MySQL  

---

## 🪟 OPSI 3: Auto Script Setup

**Hanya untuk Linux/Mac:**
```bash
git clone <repo-url> spark
cd spark
bash setup.sh
```

**Untuk Windows:**
```batch
git clone <repo-url> spark
cd spark
setup.bat
```

---

## ✅ Test Aplikasi

**Login Admin:**
```
URL: http://localhost:8080/admin/login.php
Email: admin@spark.local
Password: (cek di database/000-complete-setup.sql)
```

**Fitur yang harus berfungsi:**
- ✅ Home page dengan 10 parking locations
- ✅ Gambar parking location muncul
- ✅ Interactive map dengan markers
- ✅ Admin dashboard + transactions page
- ✅ Login/Register user
- ✅ Booking form

---

## 🐛 Troubleshooting

### Docker Container Error
```bash
# Cek status
docker-compose ps

# Lihat logs
docker-compose logs db

# Restart
docker-compose restart
```

### Database Error
```bash
# Cek database ada atau tidak
mysql -u root -p -e "SHOW DATABASES;"

# Reimport jika perlu
mysql -u root -p spark < database/000-complete-setup.sql
```

### Image Not Showing
```
Check: assets/img/parking-area/01.jpg - 10.jpg ada atau tidak
Clear browser cache: Ctrl+Shift+Delete
```

### Port Already in Use
```bash
# Ubah port di docker-compose.yml
# Atau kill process yang pakai port
lsof -i :8080  # Mac/Linux
netstat -ano | findstr :8080  # Windows
```

---

## 📋 Database Credentials (Default Docker)

```
Host: localhost (atau db jika dari Docker)
Port: 3308 (Docker) atau 3306 (lokal)
User: root
Password: rootpassword
Database: spark
```

---

## 🌐 Access URLs (Docker)

| Service | URL |
|---------|-----|
| Main App | http://localhost:8080 |
| Admin Panel | http://localhost:8080/admin/login.php |
| phpMyAdmin | http://localhost:8081 |
| API Base | http://localhost:8080/api/ |

---

## 📝 Struktur Database Otomatis

Setiap kali dijalankan, database akan include:

✅ 18 Tables (users, parking, booking, payment, dll)  
✅ Sample Data (3 users + 10 parking locations)  
✅ Foreign Keys & Relationships  
✅ Performance Indexes  
✅ Initial Data (roles, vehicle types, payment methods)  

Tidak perlu manual setup kolom!

---

## 🎯 Next Steps

1. **Develop:**
   - Edit files di local
   - Docker auto-sync changes
   - Refresh browser untuk lihat update

2. **Commit:**
   ```bash
   git add .
   git commit -m "Fitur baru: ..."
   git push origin main
   ```

3. **Deploy:**
   ```bash
   git clone <repo-url> /path/to/server
   cd /path/to/server
   docker-compose up -d
   ```

---

## 💡 Tips

- Jangan push `.env` atau `config/database.php` ke Git
- Gunakan `.env` untuk development config
- Database schema otomatis ter-setup saat first run
- Backup data: `docker exec spark-db mysqldump -u root -prootpassword spark > backup.sql`
- Restore: `mysql -u root -p spark < backup.sql`

---

**Butuh bantuan?** Lihat [SETUP_GUIDE.md](SETUP_GUIDE.md) untuk detail lengkap!
