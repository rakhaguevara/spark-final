@echo off
REM Windows version of config check (for local testing)

echo ==========================================
echo   SPARK Configuration Check
echo ==========================================
echo.

REM 1. Check .env file
echo 1. Checking .env file...
if exist .env (
    echo [OK] .env file exists
    echo.
    echo --- .env Contents ---
    type .env
    echo.
) else (
    echo [ERROR] .env file NOT FOUND!
    echo    Please create .env from .env.example
    echo.
)

REM 2. Check Docker containers
echo ==========================================
echo 2. Checking Docker containers...
docker-compose ps
echo.

REM 3. Check database connection
echo ==========================================
echo 3. Testing database connection...
docker exec spark-db-1 mysql -u root -prootpassword -e "SELECT 'Database connection OK' as status;"
echo.

REM 4. Check database and tables
echo ==========================================
echo 4. Checking database and tables...
docker exec spark-db-1 mysql -u root -prootpassword -e "USE spark; SHOW TABLES;"
echo.

REM 5. Check qr_session table
echo ==========================================
echo 5. Checking qr_session table structure...
docker exec spark-db-1 mysql -u root -prootpassword -e "USE spark; DESCRIBE qr_session;"
echo.

REM 6. Check qr_session data
echo ==========================================
echo 6. Checking qr_session data...
docker exec spark-db-1 mysql -u root -prootpassword -e "USE spark; SELECT * FROM qr_session;"
echo.

REM 7. Check booking_parkir
echo ==========================================
echo 7. Checking booking_parkir...
docker exec spark-db-1 mysql -u root -prootpassword -e "USE spark; SELECT id_booking, id_pengguna, status_booking, qr_secret FROM booking_parkir WHERE status_booking IN ('confirmed', 'ongoing') LIMIT 5;"
echo.

echo ==========================================
echo   Configuration Check Complete
echo ==========================================
pause
