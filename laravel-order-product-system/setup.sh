#!/bin/bash

echo "==================================="
echo "Setting up MySQL for Laravel..."
echo "==================================="
echo ""

echo "1/5 Installing MySQL..."
sudo apt update
sudo apt install -y mysql-server mysql-client php-mysql

echo ""
echo "2/5 Starting MySQL..."
sudo service mysql start

echo ""
echo "3/5 Creating database..."
sudo mysql -u root -p -e "CREATE DATABASE laravel_db;"

echo ""
echo "4/5 Running migrations..."
cd /home/michael_rafaillo/.openclaw/workspace/laravel-order-product-system
php artisan migrate --seed

echo ""
echo "==================================="
echo "✅ Setup Complete!"
echo "==================================="
echo ""
echo "Now start the servers:"
echo ""
echo "Terminal 1 (API):"
echo "  php artisan serve"
echo ""
echo "Terminal 2 (Frontend):"
echo "  cd frontend && npm run dev"
