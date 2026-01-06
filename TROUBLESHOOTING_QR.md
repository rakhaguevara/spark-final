# 🔧 QR Code Issue - Step-by-Step Troubleshooting

## ✅ **Perubahan Terbaru Sudah Di-Push**

Commit: `d169fbc - Enhanced QR fix with debugging tools`

File yang diupdate:
- ✅ `pages/my-ticket.php` - Enhanced error handling & logging
- ✅ `api/qr-diagnostic.php` - NEW: Diagnostic tool
- ✅ `api/bulk-fix-qr-tokens.php` - NEW: Bulk fix tool

---

## 🚀 **LANGKAH 1: Pull di VPS**

```bash
# SSH ke VPS
ssh user@72.62.125.127

# Masuk ke folder project
cd /path/to/spark

# Pull perubahan terbaru
git pull origin main

# Restart Docker
docker-compose restart web
```

---

## 🔍 **LANGKAH 2: Diagnostic - Cek Status QR Token**

Buka di browser VPS:
```
http://72.62.125.127:8080/api/qr-diagnostic.php
```

**Expected Output:**
```json
{
  "success": true,
  "total_bookings": 1,
  "bookings": [
    {
      "booking_id": "1",
      "user_id": "2",
      "status": "confirmed",
      "parking": "SPARK Grand Indonesia",
      "has_qr_secret": true,
      "has_qr_session": false,  // ❌ Ini masalahnya!
      "has_qr_token": false,    // ❌ Ini masalahnya!
      "issues": [
        "Missing qr_token (no qr_session entry)"
      ],
      "needs_fix": true
    }
  ]
}
```

**Jika `needs_fix: true`** → Lanjut ke Langkah 3

---

## 🔧 **LANGKAH 3: Bulk Fix - Generate Semua QR Token**

Buka di browser VPS:
```
http://72.62.125.127:8080/api/bulk-fix-qr-tokens.php
```

**Expected Output:**
```json
{
  "success": true,
  "total_bookings": 1,
  "fixed": 1,
  "errors": []
}
```

**Jika `fixed > 0`** → QR tokens berhasil di-generate! ✅

---

## 🧪 **LANGKAH 4: Test di Browser**

1. **Buka halaman My Tickets:**
   ```
   http://72.62.125.127:8080/pages/my-ticket.php
   ```

2. **Buka Browser Console** (F12 → Console tab)

3. **Cek Debug Logs:**
   ```
   === QR System Configuration ===
   BASEURL: http://72.62.125.127:8080
   BOOKING_ID: 1
   HAS_QR_TOKEN: true  // ✅ Harus true sekarang!
   REFRESH_INTERVAL: 10000
   ==============================
   ```

4. **QR Code harus muncul!** ✅

---

## ❌ **Jika Masih Error - Troubleshooting Lanjutan**

### **A. Cek Browser Console Error**

Buka Console (F12), cari error seperti:
```
fixMissingQR: Fetch error: Failed to fetch
```

**Solusi:**
- Cek BASEURL di `.env` file
- Pastikan `BASEURL=http://72.62.125.127:8080` (tanpa trailing slash)

---

### **B. Cek API Endpoint Accessibility**

Test manual dengan curl:
```bash
curl -X POST "http://72.62.125.127:8080/api/fix-missing-qr-tokens.php?booking_id=1" \
     -H "Cookie: PHPSESSID=your-session-id" \
     -v
```

**Expected:** HTTP 200 dengan JSON response

**Jika 404:** File tidak ter-upload, ulangi git pull

**Jika 500:** Ada error PHP, cek logs:
```bash
docker logs spark-web-1 --tail 50
```

---

### **C. Cek Database Connection**

```bash
# Masuk ke MySQL container
docker exec -it spark-db-1 mysql -u root -p

# Cek tabel qr_session
USE spark;
SELECT * FROM qr_session;
```

**Jika kosong:** Jalankan bulk-fix-qr-tokens.php lagi

**Jika tabel tidak ada:**
```sql
-- Create table manually
CREATE TABLE IF NOT EXISTS `qr_session` (
  `id_qr` int(11) NOT NULL AUTO_INCREMENT,
  `id_booking` int(11) NOT NULL,
  `qr_token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_qr`),
  KEY `id_booking` (`id_booking`),
  CONSTRAINT `qr_session_ibfk_1` FOREIGN KEY (`id_booking`) 
    REFERENCES `booking_parkir` (`id_booking`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

### **D. Cek File Permissions**

```bash
# Set correct permissions
chmod 644 api/fix-missing-qr-tokens.php
chmod 644 api/qr-diagnostic.php
chmod 644 api/bulk-fix-qr-tokens.php
chmod 644 pages/my-ticket.php

# If using Apache
chown www-data:www-data api/*.php
chown www-data:www-data pages/*.php
```

---

### **E. Cek .env Configuration**

```bash
cat .env | grep BASEURL
```

**Expected:**
```
BASEURL=http://72.62.125.127:8080
```

**Jika salah:**
```bash
nano .env
# Update BASEURL
# Save: Ctrl+O, Enter, Ctrl+X

# Restart
docker-compose restart web
```

---

## 📊 **Diagnostic Checklist**

Jalankan checklist ini untuk memastikan semua OK:

- [ ] Git pull berhasil (no conflicts)
- [ ] Docker restart berhasil
- [ ] `qr-diagnostic.php` bisa diakses
- [ ] `bulk-fix-qr-tokens.php` berhasil fix tokens
- [ ] Browser console tidak ada error
- [ ] BASEURL di console log benar
- [ ] HAS_QR_TOKEN = true
- [ ] QR code image muncul
- [ ] Auto-refresh berjalan (countdown 10s)

---

## 🆘 **Jika Semua Gagal**

Coba **manual fix** via database:

```bash
docker exec -it spark-db-1 mysql -u root -p
```

```sql
USE spark;

-- Generate QR token untuk booking ID 1
SET @booking_id = 1;
SET @qr_secret = SHA2(CONCAT('spark', @booking_id, UNIX_TIMESTAMP()), 256);
SET @qr_token = SHA2(CONCAT(@qr_secret, @booking_id, UNIX_TIMESTAMP()), 256);

-- Update booking dengan qr_secret
UPDATE booking_parkir 
SET qr_secret = @qr_secret 
WHERE id_booking = @booking_id;

-- Insert QR session
INSERT INTO qr_session (id_booking, qr_token, expires_at)
VALUES (@booking_id, @qr_token, DATE_ADD(NOW(), INTERVAL 1 HOUR))
ON DUPLICATE KEY UPDATE 
    qr_token = @qr_token,
    expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR);

-- Verify
SELECT * FROM qr_session WHERE id_booking = @booking_id;
```

Kemudian refresh halaman My Tickets.

---

## 📞 **Kirim Info Ini Jika Masih Bermasalah**

Jika masih error, kirim screenshot dari:

1. **Browser Console** (F12 → Console)
2. **Output dari:** `http://your-vps/api/qr-diagnostic.php`
3. **Docker logs:** `docker logs spark-web-1 --tail 50`
4. **Database query:** `SELECT * FROM qr_session;`

---

**Last Updated:** 2026-01-06 11:02
