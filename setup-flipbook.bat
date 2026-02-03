@echo off
echo ========================================
echo Interactive Flipbook System - Setup
echo ========================================
echo.

echo [1/6] Checking Imagick extension...
php -r "if (extension_loaded('imagick')) { echo 'OK - Imagick is installed'; } else { echo 'ERROR - Imagick not found. Please install it first.'; exit(1); }"
echo.
echo.

echo [2/6] Running database migrations...
php artisan migrate --force
echo.

echo [3/6] Creating storage link...
php artisan storage:link
echo.

echo [4/6] Seeding sample data...
php artisan db:seed --class=FlipbookSystemSeeder
echo.

echo [5/6] Clearing cache...
php artisan cache:clear
php artisan config:clear
php artisan view:clear
echo.

echo [6/6] Creating directories...
if not exist "storage\app\public\flipbooks" mkdir storage\app\public\flipbooks
if not exist "storage\app\public\flipbooks\pdfs" mkdir storage\app\public\flipbooks\pdfs
echo.

echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Next steps:
echo 1. Access admin panel: http://localhost/flipbooks
echo 2. Upload a PDF to create your first flipbook
echo 3. Use the hotspot editor to add interactive elements
echo 4. Publish and share your flipbook!
echo.
echo For detailed instructions, see FLIPBOOK_SYSTEM_GUIDE.md
echo.

pause
