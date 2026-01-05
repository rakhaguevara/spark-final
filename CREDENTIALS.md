# 🔑 SPARK Production Credentials

## ✅ CORRECT PASSWORDS (Updated)

All passwords have been fixed with correct bcrypt hashes.

---

## 👨‍💼 ADMIN ACCOUNTS

### Admin 1: Super Admin
```
Email:    admin@spark.com
Password: admin123
URL:      http://localhost:8080/admin/login.php
```

### Admin 2: System Manager
```
Email:    manager@spark.com
Password: admin123
URL:      http://localhost:8080/admin/login.php
```

---

## 🏢 PARKING OWNER ACCOUNTS

### Owner 1: Jakarta Parking Group
```
Email:    owner.jakarta@spark.com
Password: owner123
URL:      http://localhost:8080/owner/login.php
Locations: 4 (Grand Indonesia, Plaza Senayan, Kuningan City, Thamrin City)
```

### Owner 2: Bandung Parking Solutions
```
Email:    owner.bandung@spark.com
Password: owner123
URL:      http://localhost:8080/owner/login.php
Locations: 3 (Paris Van Java, Braga City Walk, Cihampelas Walk)
```

### Owner 3: Surabaya Parking Network
```
Email:    owner.surabaya@spark.com
Password: owner123
URL:      http://localhost:8080/owner/login.php
Locations: 3 (Tunjungan Plaza, Galaxy Mall, Pakuwon Mall)
```

---

## 👤 REGULAR USER ACCOUNTS

### User 1: Budi Santoso
```
Email:    budi.santoso@email.com
Password: user123
Vehicle:  Motor (B 123)
URL:      http://localhost:8080/pages/login.php
```

### User 2: Siti Nurhaliza
```
Email:    siti.nurhaliza@email.com
Password: user123
Vehicle:  Mobil (B 567)
URL:      http://localhost:8080/pages/login.php
```

### User 3: Ahmad Wijaya
```
Email:    ahmad.wijaya@email.com
Password: user123
Vehicle:  Motor (D 901)
URL:      http://localhost:8080/pages/login.php
```

### User 4: Dewi Lestari
```
Email:    dewi.lestari@email.com
Password: user123
Vehicle:  Mobil (D 345)
URL:      http://localhost:8080/pages/login.php
```

### User 5: Rudi Hermawan
```
Email:    rudi.hermawan@email.com
Password: user123
Vehicle:  Motor (L 789)
URL:      http://localhost:8080/pages/login.php
```

---

## 🔐 Password Hashes (For Reference)

```
admin123 → $2y$10$h6ig7eYcremrVSNcBENfIeOfLhPQeS4ZxuAI7A2e/77GdqLhFwkZ2
owner123 → $2y$10$WqN6GSZ1dBXojEvf8qvPLOok/RVZ.Ah/T2V7gp/qyZr/7CTe6CtHa
user123  → $2y$10$4IR0CM9YH3rIsMZYWTOlN.JC0e76lOF5V7hbKO6ADr/YXHrl8Yfde
```

---

## 🔄 Reset Database with Fixed Passwords

```bash
docker-compose down
docker volume rm spark_db_data
docker-compose up -d
```

Wait 30 seconds, then try logging in with the credentials above!

---

**Last Updated:** 2026-01-06  
**Status:** ✅ All passwords verified and working
