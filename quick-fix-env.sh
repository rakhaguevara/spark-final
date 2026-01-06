#!/bin/bash

echo "=========================================="
echo "  Quick Fix: Update .env Configuration"
echo "=========================================="
echo ""

# Backup current .env
echo "Creating backup of current .env..."
cp .env .env.backup.$(date +%Y%m%d_%H%M%S)
echo "✅ Backup created"
echo ""

# Update .env
echo "Updating .env configuration..."

# Fix DB_NAME
sed -i 's/^DB_NAME=.*/DB_NAME=spark/' .env
echo "✅ DB_NAME set to 'spark'"

# Fix DB_USER  
sed -i 's/^DB_USER=.*/DB_USER=root/' .env
echo "✅ DB_USER set to 'root'"

# Fix DB_PASS
sed -i 's/^DB_PASS=.*/DB_PASS=rootpassword/' .env
echo "✅ DB_PASS set to 'rootpassword'"

echo ""
echo "New configuration:"
echo "---"
grep -E "^DB_" .env
echo "---"
echo ""

# Restart containers
echo "Restarting Docker containers..."
docker-compose down
docker-compose up -d

echo ""
echo "✅ Configuration updated and containers restarted!"
echo ""
echo "Next steps:"
echo "1. Test: http://your-vps-ip/pages/my-ticket.php"
echo "2. Check logs: docker logs spark-web-1 --tail 50"
echo ""
