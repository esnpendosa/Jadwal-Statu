@echo off
title Status Scheduler - Master Controller
echo ===================================================
echo   AUTOMATIC STATUS SCHEDULER - STARTING ALL SERVICES
echo ===================================================

:: Menambahkan path PHP Laragon agar 'php' bisa dikenali
SET "PATH=%PATH%;C:\laragon\bin\php\php-8.2.29-Win32-vs16-x64;C:\laragon\bin\nodejs\node-v18.16.0-win-x64"

echo.
echo [1/3] Starting WhatsApp Bridge (Baileys)...
cd wa-bridge
start "WhatsApp Bridge" cmd /k "SET PATH=%PATH%;C:\laragon\bin\nodejs\node-v18.16.0-win-x64 && node server.js"
cd ..

echo.
echo [2/3] Starting Laravel Schedule Worker...
:: Menunggu 5 detik agar bridge siap
timeout /t 5 /nobreak
start "Laravel Scheduler" cmd /k "SET PATH=%PATH%;C:\laragon\bin\php\php-8.2.29-Win32-vs16-x64 && php artisan schedule:work"

echo.
echo [3/3] Starting Local Tunnel (Optional - Only for External Access)...
echo If you want to access the bridge from internet, use this window.
echo.
npx localtunnel --port 3000

echo.
echo ===================================================
echo   ALL SERVICES STARTED! 
echo   1. WhatsApp Bridge is running in another window.
echo   2. Laravel Scheduler is checking posts every minute.
echo   KEEP ALL WINDOWS OPEN FOR AUTOMATION TO WORK.
echo ===================================================
pause
