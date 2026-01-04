#!/bin/bash
# SPARK Quick Setup Script
# Run this after cloning: bash setup.sh

echo "🚀 SPARK - Quick Setup Script"
echo "=============================="
echo ""

# Check if database.php exists
if [ ! -f "config/database.php" ]; then
    echo "❌ Error: config/database.php not found"
    echo "   Please copy config/database.php.example to config/database.php"
    exit 1
fi

# Check if spark (2).sql exists
if [ ! -f "spark (2).sql" ]; then
    echo "⚠️  Warning: spark (2).sql not found"
    echo "   Please import your database manually"
else
    echo "📁 Database file found: spark (2).sql"
    
    # Ask for database credentials
    read -p "Import database? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        read -p "MySQL username [root]: " DB_USER
        DB_USER=${DB_USER:-root}
        
        read -p "Database name [spark]: " DB_NAME
        DB_NAME=${DB_NAME:-spark}
        
        echo "Importing database..."
        mysql -u "$DB_USER" -p "$DB_NAME" < "spark (2).sql"
        
        if [ $? -eq 0 ]; then
            echo "✅ Database imported successfully"
        else
            echo "❌ Database import failed"
            exit 1
        fi
    fi
fi

echo ""
echo "🔧 Running database setup script..."
php database/setup.php

if [ $? -eq 0 ]; then
    echo "✅ Database setup complete"
else
    echo "❌ Database setup failed"
    echo "   You can run it manually: php database/setup.php"
fi

echo ""
echo "📁 Setting up directories..."
mkdir -p uploads/profile
mkdir -p uploads/tickets
chmod -R 777 uploads/

echo ""
echo "✨ Setup Complete!"
echo ""
echo "📋 Next Steps:"
echo "   1. Update config/database.php with your credentials"
echo "   2. Visit: http://localhost/spark"
echo "   3. Admin panel: http://localhost/spark/admin/login.php"
echo ""
echo "📖 For more info, read SETUP_GUIDE.md"
