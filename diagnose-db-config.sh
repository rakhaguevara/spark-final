#!/bin/bash

echo "=========================================="
echo "  SPARK Database Configuration Fix"
echo "=========================================="
echo ""

# Check current .env configuration
echo "Current .env configuration:"
echo "---"
grep -E "^DB_" .env
echo "---"
echo ""

# Check which databases exist
echo "Checking existing databases..."
DATABASES=$(docker exec spark-db-1 mysql -u root -prootpassword -e "SHOW DATABASES;" 2>&1 | grep -E "spark")
echo "$DATABASES"
echo ""

# Check if 'spark' database has data
echo "Checking 'spark' database..."
SPARK_DATA=$(docker exec spark-db-1 mysql -u root -prootpassword -e "
USE spark;
SELECT 
    (SELECT COUNT(*) FROM booking_parkir) as bookings,
    (SELECT COUNT(*) FROM data_pengguna) as users,
    (SELECT COUNT(*) FROM qr_session) as qr_sessions;
" 2>&1)

if echo "$SPARK_DATA" | grep -q "ERROR"; then
    echo "❌ Database 'spark' does not exist or has errors"
else
    echo "✅ Database 'spark' exists:"
    echo "$SPARK_DATA"
fi
echo ""

# Check if 'spark_production' database has data
echo "Checking 'spark_production' database..."
SPARK_PROD_DATA=$(docker exec spark-db-1 mysql -u root -prootpassword -e "
USE spark_production;
SELECT 
    (SELECT COUNT(*) FROM booking_parkir) as bookings,
    (SELECT COUNT(*) FROM data_pengguna) as users,
    (SELECT COUNT(*) FROM qr_session) as qr_sessions;
" 2>&1)

if echo "$SPARK_PROD_DATA" | grep -q "ERROR"; then
    echo "❌ Database 'spark_production' does not exist or has errors"
else
    echo "✅ Database 'spark_production' exists:"
    echo "$SPARK_PROD_DATA"
fi
echo ""

# Recommendation
echo "=========================================="
echo "  RECOMMENDATION"
echo "=========================================="
echo ""

if echo "$SPARK_DATA" | grep -q "ERROR" && ! echo "$SPARK_PROD_DATA" | grep -q "ERROR"; then
    echo "📋 Database 'spark_production' has data, but 'spark' doesn't exist."
    echo ""
    echo "OPTION 1: Rename database (RECOMMENDED)"
    echo "---"
    echo "docker exec spark-db-1 mysql -u root -prootpassword -e \\"
    echo "  \"CREATE DATABASE spark; \\"
    echo "  USE spark_production; \\"
    echo "  SHOW TABLES;\" | tail -n +2 | xargs -I {} \\"
    echo "  docker exec spark-db-1 mysql -u root -prootpassword -e \\"
    echo "  \"CREATE TABLE spark.{} LIKE spark_production.{}; \\"
    echo "  INSERT INTO spark.{} SELECT * FROM spark_production.{};\""
    echo ""
    echo "OPTION 2: Update .env to use spark_production"
    echo "---"
    echo "sed -i 's/DB_NAME=spark/DB_NAME=spark_production/' .env"
    echo "docker-compose restart web"
    
elif ! echo "$SPARK_DATA" | grep -q "ERROR" && echo "$SPARK_PROD_DATA" | grep -q "ERROR"; then
    echo "✅ Database 'spark' has data. This is correct!"
    echo ""
    echo "FIX: Update .env to use 'spark' database"
    echo "---"
    echo "sed -i 's/DB_NAME=spark_production/DB_NAME=spark/' .env"
    echo "sed -i 's/DB_USER=spark/DB_USER=root/' .env"
    echo "sed -i 's/DB_PASS=spark123!/DB_PASS=rootpassword/' .env"
    echo "docker-compose restart web"
    
else
    echo "⚠️  Both databases exist or neither exists. Manual investigation needed."
fi

echo ""
echo "=========================================="
