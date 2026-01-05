# ⚠️ SPARK - Prevent Duplicate Data on Restart

## 🔍 Problem

Setiap kali `docker-compose restart` atau re-run init scripts, data menjadi duplikat karena INSERT statement dijalankan lagi.

## ✅ Solution

Script `01-production-data.sql` sudah diperbaiki dengan menambahkan **TRUNCATE** statements di awal untuk clear data lama sebelum insert data baru.

---

## 🛠️ How It Works

### **Before (Duplikat):**
```sql
SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO tempat_parkir (...) VALUES (...);  -- Akan duplikat!
```

### **After (No Duplikat):**
```sql
SET FOREIGN_KEY_CHECKS = 0;

-- Clear existing data first
TRUNCATE TABLE kendaraan_pengguna;
TRUNCATE TABLE harga_parkir;
TRUNCATE TABLE slot_parkir;
TRUNCATE TABLE tempat_parkir;
TRUNCATE TABLE owner_parkir;
DELETE FROM data_pengguna WHERE id_pengguna > 0;

-- Now insert fresh data
INSERT INTO tempat_parkir (...) VALUES (...);  -- No duplicates!
```

---

## 📝 Tables Cleared (In Order)

Urutan penting karena foreign key dependencies:

1. ✅ `kendaraan_pengguna` - User vehicles
2. ✅ `harga_parkir` - Pricing
3. ✅ `slot_parkir` - Parking slots
4. ✅ `tempat_parkir` - Parking locations
5. ✅ `owner_parkir` - Owner profiles
6. ✅ `data_pengguna` - All users (admin, owner, user)

---

## 🔄 Safe Restart Commands

### **Full Reset (Recommended):**
```bash
docker-compose down
docker volume rm spark_db_data
docker-compose up -d
```
→ Fresh database, no duplicates guaranteed

### **Restart Without Reset:**
```bash
docker-compose restart
```
→ **Now safe!** Script will clear old data before inserting

### **Reload Data Only:**
```bash
docker exec -i spark-db mysql -uroot -prootpassword spark < database/01-production-data.sql
```
→ Can run multiple times, no duplicates

---

## ⚠️ Important Notes

### **Why TRUNCATE Instead of DELETE?**
- ✅ Faster (no transaction log)
- ✅ Resets AUTO_INCREMENT
- ✅ Cleaner for bulk operations

### **Why DELETE for data_pengguna?**
- ⚠️ TRUNCATE doesn't work with foreign keys
- ✅ DELETE with WHERE clause is safer
- ✅ Preserves table structure

### **Foreign Key Order Matters!**
Tables must be cleared in **reverse dependency order**:
```
kendaraan_pengguna → depends on data_pengguna
harga_parkir → depends on tempat_parkir
slot_parkir → depends on tempat_parkir
tempat_parkir → depends on data_pengguna
owner_parkir → depends on data_pengguna
data_pengguna → base table
```

---

## 🧪 Test Duplicate Prevention

### **Test 1: Run Script Twice**
```bash
# First run
docker exec -i spark-db mysql -uroot -prootpassword spark < database/01-production-data.sql

# Second run (should not duplicate)
docker exec -i spark-db mysql -uroot -prootpassword spark < database/01-production-data.sql

# Check count
docker exec spark-db mysql -uroot -prootpassword spark -e "SELECT COUNT(*) FROM tempat_parkir;"
```
→ Should show **10** locations, not 20!

### **Test 2: Restart Container**
```bash
# Restart
docker-compose restart

# Check count
docker exec spark-db mysql -uroot -prootpassword spark -e "SELECT COUNT(*) FROM tempat_parkir;"
```
→ Should still show **10** locations!

---

## 📊 Expected Data Counts

After running script (should be consistent):

| Table | Count | Description |
|-------|-------|-------------|
| `data_pengguna` | 10 | 2 admin + 3 owner + 5 user |
| `owner_parkir` | 3 | 3 owner profiles |
| `tempat_parkir` | 10 | 10 parking locations |
| `slot_parkir` | 50 | 50 slots (for location 1 only) |
| `harga_parkir` | 20 | 10 locations × 2 vehicle types |
| `kendaraan_pengguna` | 5 | 5 user vehicles |

---

## 🔧 Troubleshooting

### **Still Getting Duplicates?**

**Check if script is being run multiple times:**
```bash
docker-compose logs db | grep "01-production-data"
```

**Manually clear and reload:**
```bash
docker exec spark-db mysql -uroot -prootpassword spark -e "
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE kendaraan_pengguna;
TRUNCATE TABLE harga_parkir;
TRUNCATE TABLE slot_parkir;
TRUNCATE TABLE tempat_parkir;
TRUNCATE TABLE owner_parkir;
DELETE FROM data_pengguna WHERE id_pengguna > 0;
SET FOREIGN_KEY_CHECKS = 1;
"

docker exec -i spark-db mysql -uroot -prootpassword spark < database/01-production-data.sql
```

### **Foreign Key Error?**

Make sure `FOREIGN_KEY_CHECKS` is disabled:
```sql
SET FOREIGN_KEY_CHECKS = 0;
-- ... TRUNCATE statements ...
SET FOREIGN_KEY_CHECKS = 1;
```

---

## ✅ Summary

- ✅ Script now **clears old data** before inserting
- ✅ Safe to run **multiple times**
- ✅ Safe to **restart containers**
- ✅ No more **duplicate parking locations**
- ✅ Consistent **data counts**

---

**Last Updated:** 2026-01-06  
**Status:** ✅ Duplicate Prevention Implemented
