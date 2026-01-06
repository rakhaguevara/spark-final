# 🚨 MASALAH DITEMUKAN!

## ❌ **2 Masalah Kritis di Konfigurasi .env**

### **Masalah 1: Database Name Mismatch**
```env
# Di .env Anda:
DB_NAME=spark_production  ❌

# Yang diharapkan code:
DB_NAME=spark  ✅
```

**Dampak:** Semua query ke database `spark` akan gagal karena database yang ada bernama `spark_production`!

---

### **Masalah 2: Database User Mismatch**
```env
# Di .env Anda:
DB_USER=spark  ❌
DB_PASS=spark123!

# Yang diharapkan (berdasarkan script check):
DB_USER=root  ✅
DB_PASS=rootpassword
```

**Dampak:** Connection ke database bisa gagal jika user `spark` tidak punya permissions yang cukup!

---

## ✅ **SOLUSI CEPAT**

### **Opsi 1: Update .env (RECOMMENDED)**

Edit file `.env` di VPS:

```bash
nano .env
```

**Ubah baris ini:**
```env
# SEBELUM:
DB_NAME=spark_production
DB_USER=spark
DB_PASS=spark123!

# SESUDAH:
DB_NAME=spark
DB_USER=root
DB_PASS=rootpassword
```

**Simpan dan restart:**
```bash
docker-compose down
docker-compose up -d
```

---

### **Opsi 2: Buat Database Baru + User**

Jika Anda ingin tetap pakai `spark_production` dan user `spark`:

```bash
# Masuk ke MySQL
docker exec -it spark-db-1 mysql -u root -prootpassword

# Buat database
CREATE DATABASE IF NOT EXISTS spark_production;

# Buat user dan grant permissions
CREATE USER IF NOT EXISTS 'spark'@'%' IDENTIFIED BY 'spark123!';
GRANT ALL PRIVILEGES ON spark_production.* TO 'spark'@'%';
FLUSH PRIVILEGES;

# Import schema
USE spark_production;
SOURCE /docker-entrypoint-initdb.d/00-init-complete.sql;
```

---

## 🔍 **Cek Mana yang Aktif Sekarang**

Jalankan command ini untuk cek database mana yang ada:

```bash
docker exec spark-db-1 mysql -u root -prootpassword -e "SHOW DATABASES;"
```

**Expected output:**
```
+--------------------+
| Database           |
+--------------------+
| spark              |  ← Harus ada ini!
| spark_production   |  ← Atau ini (tergantung pilihan)
+--------------------+
```

---

## 🧪 **Test Setelah Fix**

```bash
# Test connection dengan config baru
docker exec spark-db-1 mysql -u root -prootpassword -e "USE spark; SHOW TABLES;"

# Atau jika pakai spark_production:
docker exec spark-db-1 mysql -u spark -pspark123! -e "USE spark_production; SHOW TABLES;"
```

---

## 📋 **Rekomendasi Saya**

**Gunakan Opsi 1** (ubah .env ke `spark` dan `root`) karena:
1. ✅ Lebih simple
2. ✅ Sesuai dengan semua script yang sudah ada
3. ✅ Database `spark` kemungkinan sudah ada dengan data
4. ✅ Tidak perlu import ulang schema

---

## ⚠️ **PENTING: Cek Data Existing**

Sebelum ubah apa-apa, cek dulu database mana yang punya data:

```bash
# Cek database spark
docker exec spark-db-1 mysql -u root -prootpassword -e "
USE spark;
SELECT COUNT(*) as total_bookings FROM booking_parkir;
SELECT COUNT(*) as total_users FROM data_pengguna;
"

# Cek database spark_production (jika ada)
docker exec spark-db-1 mysql -u root -prootpassword -e "
USE spark_production;
SELECT COUNT(*) as total_bookings FROM booking_parkir;
SELECT COUNT(*) as total_users FROM data_pengguna;
"
```

**Pilih database yang punya data!**

---

**Ini kemungkinan besar root cause masalah QR code Anda!** 🎯
