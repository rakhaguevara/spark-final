# ✅ Setup Checklist - SPARK Project

Gunakan checklist ini untuk memastikan project siap di-clone ke device lain.

---

## 📋 Pre-Clone Requirements

Pastikan ini sudah ada di repository:

- [ ] `docker-compose.yml` (configured dengan ports & env)
- [ ] `Dockerfile` (PHP 8.2 + Apache + extensions)
- [ ] `database/000-complete-setup.sql` (lengkap semua tables)
- [ ] `database/spark (2).sql` (or included in 000-complete-setup.sql)
- [ ] `.env.example` (template untuk environment vars)
- [ ] `.gitignore` (mencegah .env dan config credentials ter-push)
- [ ] `README.md` (main documentation)
- [ ] `SETUP_GUIDE.md` (detailed setup instructions)
- [ ] `QUICK_START.md` (quick reference)
- [ ] `setup.sh` (Linux/Mac auto-setup)
- [ ] `setup.bat` (Windows auto-setup)
- [ ] `config/database.php` (with proper defaults)
- [ ] `config/app.php` (with BASEURL definition)

---

## 🔧 Clone & Setup Process

### Step 1: Clone Repo
```bash
git clone <repo-url> spark
cd spark
```
✅ **Check:**
- [ ] Semua files ada
- [ ] Hidden files (`.*`) ada (tidak hidden di view)

### Step 2: Choose Setup Method

#### Method A: Docker (Recommended)
```bash
docker-compose up -d
# Wait 30 seconds
```
✅ **Check:**
- [ ] 3 containers running: `docker-compose ps`
- [ ] Database initialized: `docker exec spark-db mysql -u root -prootpassword spark -e "SHOW TABLES;"`
- [ ] App accessible: http://localhost:8080

#### Method B: Manual
```bash
mysql -u root -p
> CREATE DATABASE spark;
> exit;

mysql -u root -p spark < database/000-complete-setup.sql
```
✅ **Check:**
- [ ] Database created
- [ ] 18 tables created
- [ ] Sample data inserted (3 users, parking locations)

#### Method C: Auto Script
```bash
bash setup.sh  # Linux/Mac
```
or
```batch
setup.bat  # Windows
```

### Step 3: Configuration

```bash
# Copy .env template (if needed)
cp .env.example .env

# Edit .env with local values (if manual setup)
nano .env  # or open in editor
```

✅ **Check:**
- [ ] DB_HOST, DB_USER, DB_PASS correct
- [ ] BASEURL set correctly
- [ ] uploads/ folder writable (chmod 777 uploads/)

### Step 4: Verify Setup

Open in browser:
- [ ] http://localhost:8080 → Main app loads
- [ ] http://localhost:8080/admin/login.php → Admin login page
- [ ] http://localhost:8081 → phpMyAdmin (if Docker)

Test login:
- [ ] Email: `admin@spark.local`
- [ ] Admin dashboard loads without errors

Test functionality:
- [ ] Home page shows 10 parking locations
- [ ] Images display correctly
- [ ] Map renders with markers
- [ ] User can browse parking details
- [ ] Booking form works

---

## 🐳 Docker Specific Checks

After `docker-compose up -d`:

```bash
# Check all containers running
docker-compose ps
# Output: 3 containers with status "Up"

# Check database logs
docker-compose logs db
# Should see: "mysqld is ready for connections"

# Check PHP app logs
docker-compose logs web
# Should be clean (no errors)

# Access MySQL from host
mysql -h 127.0.0.1 -P 3308 -u root -prootpassword spark -e "SELECT COUNT(*) as 'Total Parking Locations' FROM tempat_parkir;"
# Should return: 10-15 locations

# Check file permissions in container
docker exec spark-app ls -la /var/www/html/uploads/
# Should be writable

# Stop containers
docker-compose down
```

---

## 🔍 Database Schema Verification

After setup, verify all tables exist:

```sql
-- Check table count (should be 18)
SELECT COUNT(*) as 'Total Tables' FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'spark';

-- Check key tables
SHOW TABLES IN spark;

-- Should see:
-- booking_parkir
-- data_pengguna
-- harga_parkir
-- history_transaksi
-- jenis_kendaraan
-- kendaraan_pengguna
-- metode_pembayaran
-- pembayaran_booking
-- preferensi_pengguna
-- qr_session
-- role_pengguna
-- scan_history
-- slot_parkir
-- tempat_parkir
-- tiket_digital
-- ulasan_tempat
-- wallet_methods
-- dompet_pengguna
```

---

## 📋 Sample Data Verification

After setup, should have:

```sql
-- 3 users
SELECT COUNT(*) as 'Total Users' FROM data_pengguna;
-- Result: 3 (user, admin, owner)

-- Multiple parking locations
SELECT COUNT(*) as 'Total Locations' FROM tempat_parkir;
-- Result: 10-15

-- Parking slots
SELECT COUNT(*) as 'Total Slots' FROM slot_parkir;
-- Result: 100+

-- Sample user vehicle
SELECT * FROM kendaraan_pengguna WHERE id_pengguna = 1;
-- Result: Honda PCX (B-1234-XYZ)
```

---

## 🚨 Common Issues & Fixes

### Issue: Database not initialized
```bash
# Docker
docker-compose restart db
docker-compose logs db

# Manual
mysql -u root -p spark < database/000-complete-setup.sql
```

### Issue: Port already in use
```bash
# Change ports in docker-compose.yml
# OR kill existing process
# Linux/Mac
lsof -i :8080
kill -9 <PID>

# Windows
netstat -ano | findstr :8080
taskkill /PID <PID> /F
```

### Issue: Images not showing
```
1. Check assets/img/parking-area/ has files 01.jpg - 10.jpg
2. Clear browser cache (Ctrl+Shift+Delete)
3. Check browser console for 404 errors
4. Verify BASEURL in config/app.php
```

### Issue: Permission denied on uploads/
```bash
# Docker (auto-handled)
docker exec spark-app chmod 777 /var/www/html/uploads

# Manual
chmod 777 uploads/
chmod 777 uploads/profile/
```

### Issue: PDOException or Column not found
```bash
# Docker
docker-compose down
docker-compose up -d  # Will re-import database
sleep 30

# Manual
mysql -u root -p spark < database/000-complete-setup.sql
```

---

## ✨ Post-Setup Recommendations

After successful setup:

- [ ] Update .gitignore to prevent .env push
- [ ] Create backup: `mysqldump -u root -p spark > backup.sql`
- [ ] Document any custom configurations
- [ ] Test all user flows (register → login → booking)
- [ ] Check admin dashboard functionality
- [ ] Verify email/notification systems (if configured)
- [ ] Test QR code generation (if implemented)
- [ ] Load test with multiple users

---

## 📝 Final Verification

Run this command to generate setup report:

```bash
# Create setup verification script
php database/setup.php

# Should output:
# ✓ Database connection successful
# ✓ All tables exist (18/18)
# ✓ All required columns present
# ✓ Sample data loaded
```

---

**✅ If all checks pass, setup is complete and project is ready to use!**

For detailed troubleshooting: See [SETUP_GUIDE.md](SETUP_GUIDE.md)
