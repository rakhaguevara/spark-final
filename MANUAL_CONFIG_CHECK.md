# 🔧 Manual Configuration Check Commands

Jalankan command-command ini **satu per satu** di VPS untuk cek konfigurasi.

---

## 📋 **1. Cek File .env**

```bash
# Lihat isi file .env
cat .env
```

**Yang perlu dicek:**
- `DB_HOST=db` (bukan localhost!)
- `DB_NAME=spark`
- `DB_USER=root`
- `DB_PASS=rootpassword` (atau password yang Anda set)
- `BASEURL=http://72.62.125.127:8080` (sesuai IP VPS Anda)

---

## 🐳 **2. Cek Docker Containers**

```bash
# Lihat status containers
docker-compose ps

# Atau
docker ps
```

**Expected output:**
```
NAME            STATUS          PORTS
spark-web-1     Up 10 minutes   0.0.0.0:8080->80/tcp
spark-db-1      Up 10 minutes   0.0.0.0:3308->3306/tcp
spark-pma-1     Up 10 minutes   0.0.0.0:8081->80/tcp
```

---

## 💾 **3. Test Database Connection**

```bash
# Test koneksi (ganti password jika berbeda)
docker exec spark-db-1 mysql -u root -prootpassword -e "SELECT 'OK' as status;"
```

**Jika error "Access denied"** → Password salah, cek `.env`

---

## 📊 **4. Cek Database & Tables**

```bash
# Lihat semua tables
docker exec spark-db-1 mysql -u root -prootpassword -e "USE spark; SHOW TABLES;"
```

**Harus ada table:**
- `booking_parkir`
- `qr_session` ← **PENTING!**
- `data_pengguna`
- dll.

**Jika `qr_session` tidak ada:**
```bash
# Import database schema
docker exec -i spark-db-1 mysql -u root -prootpassword spark < database/00-init-complete.sql
```

---

## 🔍 **5. Cek Struktur Table qr_session**

```bash
docker exec spark-db-1 mysql -u root -prootpassword -e "USE spark; DESCRIBE qr_session;"
```

**Expected output:**
```
+------------+-----------+------+-----+-------------------+
| Field      | Type      | Null | Key | Default           |
+------------+-----------+------+-----+-------------------+
| id_qr      | int(11)   | NO   | PRI | NULL              |
| id_booking | int(11)   | NO   | MUL | NULL              |
| qr_token   | char(64)  | NO   |     | NULL              |
| expires_at | datetime  | NO   |     | NULL              |
| created_at | timestamp | NO   |     | CURRENT_TIMESTAMP |
+------------+-----------+------+-----+-------------------+
```

---

## 📈 **6. Cek Data di qr_session**

```bash
docker exec spark-db-1 mysql -u root -prootpassword -e "USE spark; SELECT * FROM qr_session;"
```

**Jika kosong** → QR tokens belum di-generate

**Jika ada data** → Cek apakah `id_booking` match dengan booking Anda

---

## 🎫 **7. Cek Booking Data**

```bash
docker exec spark-db-1 mysql -u root -prootpassword -e "
USE spark;
SELECT 
    id_booking, 
    id_pengguna, 
    status_booking, 
    LEFT(qr_secret, 10) as qr_secret_preview
FROM booking_parkir 
WHERE status_booking IN ('confirmed', 'ongoing')
LIMIT 5;
"
```

**Yang perlu dicek:**
- Ada booking dengan status `confirmed` atau `ongoing`?
- `qr_secret` ada isinya (bukan NULL)?

---

## 🔗 **8. Cek Join booking_parkir + qr_session**

```bash
docker exec spark-db-1 mysql -u root -prootpassword -e "
USE spark;
SELECT 
    b.id_booking,
    b.status_booking,
    b.id_pengguna,
    q.id_qr,
    LEFT(q.qr_token, 10) as qr_token_preview,
    q.expires_at
FROM booking_parkir b
LEFT JOIN qr_session q ON b.id_booking = q.id_booking
WHERE b.status_booking IN ('confirmed', 'ongoing');
"
```

**Expected:**
- Jika `id_qr` NULL → QR session belum dibuat
- Jika `id_qr` ada angka → QR session sudah ada

---

## 📝 **9. Cek PHP Error Logs**

```bash
# Lihat 50 baris terakhir
docker logs spark-web-1 --tail 50

# Atau follow real-time
docker logs spark-web-1 -f
```

**Cari error seperti:**
- `Access denied for user`
- `Table 'spark.qr_session' doesn't exist`
- `SQLSTATE[HY000]`

---

## 🌐 **10. Test API Endpoints**

```bash
# Test diagnostic API
curl http://localhost:8080/api/qr-diagnostic.php

# Test bulk fix API
curl -X POST http://localhost:8080/api/bulk-fix-qr-tokens.php
```

**Expected:** JSON response dengan `"success": true`

---

## 🔐 **11. Cek Database User Permissions**

```bash
docker exec spark-db-1 mysql -u root -prootpassword -e "
SELECT user, host FROM mysql.user WHERE user='root';
SHOW GRANTS FOR 'root'@'%';
"
```

---

## 📁 **12. Cek File Permissions**

```bash
# Cek permissions di folder api
docker exec spark-web-1 ls -la /var/www/html/api/ | grep qr
```

**Expected:**
```
-rw-r--r-- fix-missing-qr-tokens.php
-rw-r--r-- generate-qr-image.php
-rw-r--r-- qr-diagnostic.php
```

---

## 🚀 **Quick Check Script**

Atau jalankan script ini untuk cek semua sekaligus:

```bash
# Upload check-config.sh ke VPS
scp check-config.sh user@72.62.125.127:/path/to/spark/

# SSH dan jalankan
ssh user@72.62.125.127
cd /path/to/spark
chmod +x check-config.sh
./check-config.sh
```

---

## 📤 **Kirim Output Ini**

Setelah jalankan command di atas, kirim output dari:

1. ✅ `cat .env` (sensor password jika perlu)
2. ✅ `docker-compose ps`
3. ✅ `DESCRIBE qr_session`
4. ✅ `SELECT * FROM qr_session`
5. ✅ Join query (booking + qr_session)
6. ✅ `docker logs spark-web-1 --tail 50`

Dengan info ini saya bisa **identify exact problem**! 🎯

---

**Created:** 2026-01-06 11:09
