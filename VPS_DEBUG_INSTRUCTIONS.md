# 🔍 VPS Debugging Instructions

## ✅ **Latest Changes Pushed**

Commit: `04b7743 - Add detailed logging to QR generation APIs`

**Perubahan:**
- ✅ Enhanced logging di `generate-qr-image.php`
- ✅ Enhanced logging di `fix-missing-qr-tokens.php`  
- ✅ Verification setelah INSERT/UPDATE
- ✅ QR token expiry: 10s → 1 hour (lebih stabil)

---

## 🚀 **STEP 1: Pull & Restart di VPS**

```bash
ssh user@72.62.125.127
cd /path/to/spark
git pull origin main
docker-compose restart web
```

---

## 🧪 **STEP 2: Test & Collect Logs**

### A. Buka Halaman My Tickets
```
http://72.62.125.127:8080/pages/my-ticket.php
```

### B. Klik Tombol "Regenerate QR Code"

Tunggu sampai muncul alert "QR Code generated successfully!"

### C. Cek Server Logs

```bash
# Lihat logs real-time
docker logs spark-web-1 --tail 100 -f

# Atau save ke file
docker logs spark-web-1 --tail 200 > qr-debug.log
```

**Cari log entries seperti:**
```
=== Fix Missing QR Tokens ===
Booking ID: 1
User ID: 2
Booking found. Status: confirmed
Has qr_secret: Yes
Creating new QR session
Token: abc123def4...
QR session created. Insert ID: 1
Verification: Record exists = Yes (ID: 1)
```

**Dan juga:**
```
=== QR Image Generation Request ===
Booking ID: 1
Database connection established
Query executed. Result: Found
QR Token: abc123def4...
Generating QR code for content length: 69
QR code generated successfully
```

---

## 📊 **STEP 3: Manual Database Check**

```bash
docker exec -it spark-db-1 mysql -u root -p
```

```sql
USE spark;

-- Cek apakah QR session ada
SELECT * FROM qr_session WHERE id_booking = 1;

-- Cek booking
SELECT id_booking, status_booking, qr_secret FROM booking_parkir WHERE id_booking = 1;

-- Cek join
SELECT 
    b.id_booking,
    b.status_booking,
    q.id_qr,
    q.qr_token,
    q.expires_at
FROM booking_parkir b
LEFT JOIN qr_session q ON b.id_booking = q.id_booking
WHERE b.id_booking = 1;
```

---

## 🎯 **Expected Results**

### ✅ **Jika Berhasil:**

**Logs akan menunjukkan:**
```
QR session created. Insert ID: 1
Verification: Record exists = Yes (ID: 1)
```

**Database query:**
```
+------------+----------+------------------+
| id_booking | id_qr    | qr_token         |
+------------+----------+------------------+
| 1          | 1        | abc123def456...  |
+------------+----------+------------------+
```

**QR Code akan muncul** di halaman My Tickets! ✅

---

### ❌ **Jika Masih Gagal:**

**Scenario 1: Insert berhasil tapi SELECT gagal**
```
Logs: QR session created. Insert ID: 1
Logs: Verification: Record exists = No
```
→ **Masalah:** Database transaction atau connection issue

**Scenario 2: Insert gagal**
```
Logs: EXCEPTION in fix-missing-qr-tokens: ...
```
→ **Masalah:** Database constraint atau permission

**Scenario 3: QR image generation tidak menemukan token**
```
Logs: Query executed. Result: Not found
Logs: ERROR: No QR token found for booking 1
```
→ **Masalah:** Data tidak persist atau database tidak sync

---

## 🆘 **Kirim Info Ini**

Jika masih error, kirim:

1. **Server logs** (dari `docker logs spark-web-1`)
2. **Database query result** (dari SQL di atas)
3. **Screenshot** error di browser
4. **Browser console** (F12 → Console tab)

Dengan informasi ini saya bisa identifikasi **root cause** yang sebenarnya!

---

**Created:** 2026-01-06 11:06
