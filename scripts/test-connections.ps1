#!/bin/bash

echo "🧪 Testing database connections..."

# Test MySQL if running
if docker ps | grep -q "internal-tools-mysql"; then
    echo "📡 Testing MySQL connection..."
    if docker exec internal-tools-mysql mysql -u${MYSQL_USER:-dev} -p${MYSQL_PASSWORD:-dev123} -e "SELECT 'MySQL OK' as status;" ${MYSQL_DATABASE:-internal_tools} 2>/dev/null; then
        echo "✅ MySQL connection successful"
        echo "🔗 phpMyAdmin: http://localhost:${PHPMYADMIN_PORT:-8080}"
    else
        echo "❌ MySQL connection failed"
    fi
else
    echo "⚫ MySQL not running"
fi

echo ""


