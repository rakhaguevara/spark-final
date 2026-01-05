# ✅ SPARK Database Schema - Complete

## 🎉 All Tables Created Successfully!

Database initialization is now **100% complete** with all required tables.

---

## 📊 Complete Table List (17 Tables)

### **Core Tables:**
1. ✅ `role_pengguna` - User roles (user, admin, owner)
2. ✅ `data_pengguna` - User accounts
3. ✅ `jenis_kendaraan` - Vehicle types (Motor, Mobil)

### **Owner & Parking:**
4. ✅ `owner_parkir` - Owner profiles
5. ✅ `tempat_parkir` - Parking locations
6. ✅ `slot_parkir` - Parking slots
7. ✅ `harga_parkir` - Pricing per location/vehicle type
8. ✅ `parking_photos` - Multiple photos per location ⭐ **NEW**

### **User & Booking:**
9. ✅ `kendaraan_pengguna` - User vehicles
10. ✅ `booking_parkir` - Parking bookings
11. ✅ `qr_session` - QR code sessions
12. ✅ `scan_history` - QR scan logs

### **Payment & Wallet:**
13. ✅ `pembayaran_booking` - Payment transactions
14. ✅ `wallet_methods` - User payment methods ⭐ **NEW**

### **Reviews & Communication:**
15. ✅ `ulasan_tempat` - Location reviews ⭐ **NEW**
16. ✅ `notifikasi_pengguna` - User notifications
17. ✅ `contacts` - Contact form submissions

---

## 🆕 Recently Added Tables

### **1. ulasan_tempat (Reviews)**
```sql
CREATE TABLE ulasan_tempat (
  id_ulasan INT AUTO_INCREMENT PRIMARY KEY,
  id_tempat INT NOT NULL,
  id_pengguna INT NOT NULL,
  rating INT(1) CHECK (rating >= 1 AND rating <= 5),
  komentar TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (id_tempat) REFERENCES tempat_parkir(id_tempat),
  FOREIGN KEY (id_pengguna) REFERENCES data_pengguna(id_pengguna)
);
```

### **2. wallet_methods (Payment Methods)**
```sql
CREATE TABLE wallet_methods (
  id_wallet INT AUTO_INCREMENT PRIMARY KEY,
  id_pengguna INT NOT NULL,
  type ENUM('bank','ewallet','paypal'),
  provider_name VARCHAR(50),
  account_identifier VARCHAR(255),
  is_default TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP,
  FOREIGN KEY (id_pengguna) REFERENCES data_pengguna(id_pengguna)
);
```

### **3. parking_photos (Multiple Photos)**
```sql
CREATE TABLE parking_photos (
  id_foto INT AUTO_INCREMENT PRIMARY KEY,
  id_tempat INT NOT NULL,
  foto_path VARCHAR(255),
  urutan INT DEFAULT 1,
  created_at TIMESTAMP,
  FOREIGN KEY (id_tempat) REFERENCES tempat_parkir(id_tempat)
);
```

---

## ✅ Verification

### **Check All Tables:**
```bash
docker exec spark-db mysql -uroot -prootpassword spark -e "SHOW TABLES;"
```

**Expected Output:** 17 tables

### **Check Data:**
```bash
# Users
docker exec spark-db mysql -uroot -prootpassword spark -e "SELECT COUNT(*) FROM data_pengguna;"
# Expected: 10 (2 admin + 3 owner + 5 user)

# Locations
docker exec spark-db mysql -uroot -prootpassword spark -e "SELECT COUNT(*) FROM tempat_parkir;"
# Expected: 3 (minimal data)

# Slots
docker exec spark-db mysql -uroot -prootpassword spark -e "SELECT COUNT(*) FROM slot_parkir;"
# Expected: 15 (5 per location)
```

---

## 🔄 Auto-Reset on Restart

All tables are now included in:
- ✅ `00-init-complete.sql` - Creates all tables
- ✅ `01-production-data.sql` - Loads minimal data + TRUNCATE to prevent duplicates

**No more missing table errors!** 🎉

---

## 📝 Summary of Fixes

### **Issues Resolved:**
1. ❌ **Before:** Missing `ulasan_tempat` → Dashboard error
2. ❌ **Before:** Missing `wallet_methods` → Wallet page error
3. ❌ **Before:** Missing `parking_photos` → Photo upload error

### **After:**
1. ✅ All 17 tables created automatically
2. ✅ No missing table errors
3. ✅ Safe to restart without data duplication
4. ✅ Complete schema for all features

---

## 🎯 Next Steps

Database is now **production-ready**! You can:

1. ✅ **Login as admin** - http://localhost:8080/admin/login.php
2. ✅ **Login as owner** - http://localhost:8080/owner/login.php
3. ✅ **Login as user** - http://localhost:8080/pages/login.php
4. ✅ **Add payment methods** - Wallet page works
5. ✅ **Add reviews** - Review system works
6. ✅ **Upload photos** - Multiple photos per location works

---

**Last Updated:** 2026-01-06  
**Total Tables:** 17  
**Status:** ✅ Complete & Production Ready
