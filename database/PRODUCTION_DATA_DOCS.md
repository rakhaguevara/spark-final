# 📊 SPARK Production Data Documentation

## 🎯 Overview

Production data dengan **koneksi yang jelas** antara Admin, Owner, Parking Locations, dan Users.

---

## 👥 User Accounts (10 Total)

### **1. ADMIN ACCOUNTS (2)**

| ID | Name | Email | Password | Role |
|----|------|-------|----------|------|
| 1 | Super Admin | admin@spark.com | admin123 | Admin |
| 2 | System Manager | manager@spark.com | admin123 | Admin |

**Fungsi:**
- Mengelola seluruh sistem
- Melihat semua transaksi
- Manage users, owners, dan parking locations

---

### **2. PARKING OWNERS (3)**

| ID | Name | Email | Password | Locations | Total Slots |
|----|------|-------|----------|-----------|-------------|
| 3 | Jakarta Parking Group | owner.jakarta@spark.com | owner123 | 1 | 5 |
| 4 | Bandung Parking Solutions | owner.bandung@spark.com | owner123 | 1 | 5 |
| 5 | Surabaya Parking Network | owner.surabaya@spark.com | owner123 | 1 | 5 |

**Koneksi:**
- Setiap owner memiliki **1 parking location** dengan **5 slots** (minimal data)
- Owner bisa **menambah locations dan slots** lewat dashboard
- Owner bisa manage pricing, slots, dan scan QR tickets
- Owner bisa lihat revenue dari lokasi mereka

---

### **3. REGULAR USERS (5)**

| ID | Name | Email | Password | Vehicle | Plate Hint |
|----|------|-------|----------|---------|------------|
| 6 | Budi Santoso | budi.santoso@email.com | user123 | Motor | B 123 |
| 7 | Siti Nurhaliza | siti.nurhaliza@email.com | user123 | Mobil | B 567 |
| 8 | Ahmad Wijaya | ahmad.wijaya@email.com | user123 | Motor | D 901 |
| 9 | Dewi Lestari | dewi.lestari@email.com | user123 | Mobil | D 345 |
| 10 | Rudi Hermawan | rudi.hermawan@email.com | user123 | Motor | L 789 |

**Fungsi:**
- Bisa booking parking di semua lokasi
- Punya kendaraan terdaftar
- Bisa lihat history booking

---

## 🏢 Parking Locations (3 Total - Minimal Data)

### **Jakarta Parking Group (Owner ID: 3)**

| ID | Name | Address | Slots | Price (Motor/Mobil) |
|----|------|---------|-------|---------------------|
| 1 | SPARK Grand Indonesia | Jl. MH Thamrin No. 1 | 5 | Rp 5,000 / Rp 10,000 |

**Total:** 5 parking slots (3 motor + 2 mobil)

---

### **Bandung Parking Solutions (Owner ID: 4)**

| ID | Name | Address | Slots | Price (Motor/Mobil) |
|----|------|---------|-------|---------------------|
| 2 | SPARK Paris Van Java | Jl. Sukajadi No. 137-139 | 5 | Rp 4,000 / Rp 8,000 |

**Total:** 5 parking slots (3 motor + 2 mobil)

---

### **Surabaya Parking Network (Owner ID: 5)**

| ID | Name | Address | Slots | Price (Motor/Mobil) |
|----|------|---------|-------|---------------------|
| 3 | SPARK Tunjungan Plaza | Jl. Basuki Rahmat No. 8-12 | 5 | Rp 4,500 / Rp 9,000 |

**Total:** 5 parking slots (3 motor + 2 mobil)

---

> **Note:** Ini adalah data minimal untuk demo. Owner bisa menambah locations dan slots lewat dashboard.

---

## 🔗 Data Connections

### **Hierarchy:**
```
ADMIN (2)
  └─ Manage all system
  
OWNERS (3)
  ├─ Jakarta Parking Group
  │   ├─ SPARK Grand Indonesia (100 spots)
  │   ├─ SPARK Plaza Senayan (150 spots)
  │   ├─ SPARK Kuningan City (120 spots)
  │   └─ SPARK Thamrin City (80 spots)
  │
  ├─ Bandung Parking Solutions
  │   ├─ SPARK Paris Van Java (200 spots)
  │   ├─ SPARK Braga City Walk (90 spots)
  │   └─ SPARK Cihampelas Walk (110 spots)
  │
  └─ Surabaya Parking Network
      ├─ SPARK Tunjungan Plaza (180 spots)
      ├─ SPARK Galaxy Mall (140 spots)
      └─ SPARK Pakuwon Mall (160 spots)

USERS (5)
  ├─ Budi Santoso (Motor) → Can book any location
  ├─ Siti Nurhaliza (Mobil) → Can book any location
  ├─ Ahmad Wijaya (Motor) → Can book any location
  ├─ Dewi Lestari (Mobil) → Can book any location
  └─ Rudi Hermawan (Motor) → Can book any location
```

---

## 📈 Statistics

### **Total Capacity:**
- **Total Parking Spots:** 1,330
- **Motor Slots:** 665 (50%)
- **Mobil Slots:** 665 (50%)

### **Price Range:**
- **Motor:** Rp 3,500 - Rp 6,000 per jam
- **Mobil:** Rp 7,000 - Rp 12,000 per jam

### **Geographic Distribution:**
- **Jakarta:** 4 locations (450 spots)
- **Bandung:** 3 locations (400 spots)
- **Surabaya:** 3 locations (480 spots)

---

## 🧪 Testing Scenarios

### **Scenario 1: User Books Parking**
1. Login sebagai **Budi Santoso** (budi.santoso@email.com / user123)
2. Pilih **SPARK Grand Indonesia**
3. Book parking untuk motor (B 123)
4. Bayar Rp 5,000/jam
5. Dapat QR code ticket

### **Scenario 2: Owner Manages Location**
1. Login sebagai **Jakarta Parking Group** (owner.jakarta@spark.com / owner123)
2. Lihat 4 parking locations
3. Edit pricing atau slots
4. Scan QR code dari user
5. Lihat revenue statistics

### **Scenario 3: Admin Monitors System**
1. Login sebagai **Super Admin** (admin@spark.com / admin123)
2. Lihat semua 10 parking locations
3. Lihat semua 3 owners
4. Lihat semua 5 users
5. Monitor transactions

---

## 🔐 Security Notes

### **Password Policy:**
- All passwords are hashed with bcrypt
- Default passwords for demo:
  - Admin: `admin123`
  - Owner: `owner123`
  - User: `user123`

### **Production Recommendations:**
1. ✅ Change all default passwords
2. ✅ Implement password complexity rules
3. ✅ Add email verification
4. ✅ Enable 2FA for admin accounts
5. ✅ Regular security audits

---

## 📝 Data Integrity

### **Foreign Key Relationships:**
```sql
data_pengguna (users/owners/admins)
  └─ role_pengguna (roles)

owner_parkir
  └─ data_pengguna (owner accounts)

tempat_parkir (parking locations)
  └─ data_pengguna (owner who owns it)

slot_parkir (parking slots)
  └─ tempat_parkir (location)
  └─ jenis_kendaraan (vehicle type)

harga_parkir (pricing)
  └─ tempat_parkir (location)
  └─ jenis_kendaraan (vehicle type)

kendaraan_pengguna (user vehicles)
  └─ data_pengguna (user)
  └─ jenis_kendaraan (vehicle type)

booking_parkir (bookings)
  └─ data_pengguna (user)
  └─ tempat_parkir (location)
  └─ slot_parkir (slot)
  └─ kendaraan_pengguna (vehicle)
```

---

## 🚀 Quick Test Commands

### **Login as Admin:**
```
URL: http://localhost:8080/admin/login.php
Email: admin@spark.com
Password: admin123
```

### **Login as Owner:**
```
URL: http://localhost:8080/owner/login.php
Email: owner.jakarta@spark.com
Password: owner123
```

### **Login as User:**
```
URL: http://localhost:8080/pages/login.php
Email: budi.santoso@email.com
Password: user123
```

---

## 📊 Database Queries for Verification

### **Check Owner-Location Connection:**
```sql
SELECT 
    dp.nama_pengguna AS owner_name,
    COUNT(tp.id_tempat) AS total_locations,
    SUM(tp.total_spot) AS total_spots
FROM data_pengguna dp
LEFT JOIN tempat_parkir tp ON dp.id_pengguna = tp.id_pemilik
WHERE dp.role_pengguna = 3
GROUP BY dp.id_pengguna;
```

### **Check User Vehicles:**
```sql
SELECT 
    dp.nama_pengguna,
    jk.nama_jenis,
    kp.plat_hint
FROM data_pengguna dp
JOIN kendaraan_pengguna kp ON dp.id_pengguna = kp.id_pengguna
JOIN jenis_kendaraan jk ON kp.id_jenis = jk.id_jenis
WHERE dp.role_pengguna = 1;
```

### **Check Pricing:**
```sql
SELECT 
    tp.nama_tempat,
    jk.nama_jenis,
    hp.harga_per_jam
FROM tempat_parkir tp
JOIN harga_parkir hp ON tp.id_tempat = hp.id_tempat
JOIN jenis_kendaraan jk ON hp.id_jenis = jk.id_jenis
ORDER BY tp.id_tempat, jk.id_jenis;
```

---

**Last Updated:** 2026-01-06  
**Data Version:** 1.0 Production  
**Status:** ✅ Ready for Use
