#!/bin/bash

echo "==================================="
echo "Laravel E-Commerce Setup Script"
echo "==================================="
echo ""

echo "Step 1: Installing MySQL..."
sudo apt update
sudo apt install -y mysql-server mysql-client php-mysql

echo ""
echo "Step 2: Starting MySQL service..."
sudo service mysql start

echo ""
echo "Step 3: Creating database..."
sudo mysql -u root -p -e "CREATE DATABASE laravel_db;"

echo ""
echo "Step 4: Running migrations and seeders..."
cd /home/michael_rafaillo/.openclaw/workspace/laravel-order-product-system
php artisan migrate --seed

echo ""
echo "==================================="
echo "✅ Setup Complete!"
echo "==================================="
echo ""
echo "To start the servers:"
echo ""
echo "Terminal 1 (Laravel API):"
echo "  cd /home/michael_rafaillo/.openclaw/workspace/laravel-order-product-system"
echo "  php artisan serve"
echo ""
echo "Terminal 2 (Frontend):"
echo "  cd /home/michael_rafaillo/.openclaw/workspace/laravel-order-product-system/frontend"
echo "  npm run dev"
echo ""
echo "Frontend: http://localhost:5173"
echo "API: http://localhost:8000/api"
