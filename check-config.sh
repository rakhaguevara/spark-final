#!/bin/bash

echo "=========================================="
echo "  SPARK Configuration Check"
echo "=========================================="
echo ""

# 1. Check .env file
echo "1. Checking .env file..."
if [ -f .env ]; then
    echo "✅ .env file exists"
    echo ""
    echo "--- .env Contents ---"
    cat .env
    echo ""
else
    echo "❌ .env file NOT FOUND!"
    echo "   Please create .env from .env.example"
    echo ""
fi

# 2. Check Docker containers
echo "=========================================="
echo "2. Checking Docker containers..."
docker-compose ps
echo ""

# 3. Check database connection
echo "=========================================="
echo "3. Testing database connection..."
docker exec spark-db-1 mysql -u root -p"${DB_PASS:-rootpassword}" -e "SELECT 'Database connection OK' as status;" 2>&1
echo ""

# 4. Check database and tables
echo "=========================================="
echo "4. Checking database and tables..."
docker exec spark-db-1 mysql -u root -p"${DB_PASS:-rootpassword}" -e "
USE spark;
SHOW TABLES;
" 2>&1
echo ""

# 5. Check qr_session table structure
echo "=========================================="
echo "5. Checking qr_session table structure..."
docker exec spark-db-1 mysql -u root -p"${DB_PASS:-rootpassword}" -e "
USE spark;
DESCRIBE qr_session;
" 2>&1
echo ""

# 6. Check qr_session data
echo "=========================================="
echo "6. Checking qr_session data..."
docker exec spark-db-1 mysql -u root -p"${DB_PASS:-rootpassword}" -e "
USE spark;
SELECT * FROM qr_session;
" 2>&1
echo ""

# 7. Check booking_parkir data
echo "=========================================="
echo "7. Checking booking_parkir (confirmed/ongoing)..."
docker exec spark-db-1 mysql -u root -p"${DB_PASS:-rootpassword}" -e "
USE spark;
SELECT id_booking, id_pengguna, status_booking, qr_secret 
FROM booking_parkir 
WHERE status_booking IN ('confirmed', 'ongoing')
LIMIT 5;
" 2>&1
echo ""

# 8. Check PHP config
echo "=========================================="
echo "8. Checking PHP configuration..."
docker exec spark-web-1 php -v
echo ""

# 9. Check file permissions
echo "=========================================="
echo "9. Checking file permissions..."
docker exec spark-web-1 ls -la /var/www/html/api/ | grep -E "(fix-missing|generate-qr|qr-diagnostic)"
echo ""

# 10. Check logs
echo "=========================================="
echo "10. Recent error logs (last 20 lines)..."
docker logs spark-web-1 --tail 20 2>&1
echo ""

echo "=========================================="
echo "  Configuration Check Complete"
echo "=========================================="
