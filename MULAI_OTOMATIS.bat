@echo off
title Status Scheduler - Master Controller
echo ===================================================
echo   AUTOMATIC STATUS SCHEDULER - STARTING ALL SERVICES
echo ===================================================

:: Menambahkan path PHP Laragon agar 'php' bisa dikenali
SET "PATH=%PATH%;C:\laragon\bin\php\php-8.2.29-Win32-vs16-x64;C:\laragon\bin\nodejs\node-v18.16.0-win-x64"

echo.
echo [1/4] Starting WhatsApp Bridge (Baileys)...
cd wa-bridge
start "WhatsApp Bridge" cmd /k "SET PATH=%PATH%;C:\laragon\bin\nodejs\node-v18.16.0-win-x64 && node --experimental-global-webcrypto server.js"
cd ..

echo.
echo [2/4] Starting Laravel Web Server (http://127.0.0.1:8000)...
start "Laravel Server" cmd /k "SET PATH=%PATH%;C:\laragon\bin\php\php-8.2.29-Win32-vs16-x64 && php artisan serve"

echo.
echo [3/4] Starting Laravel Schedule Worker (Auto-Publish every minute)...
:: Menunggu 3 detik agar server siap
timeout /t 3 /nobreak
start "Laravel Scheduler" cmd /k "SET PATH=%PATH%;C:\laragon\bin\php\php-8.2.29-Win32-vs16-x64 && php artisan schedule:work"

echo.
echo [4/4] Starting Queue Worker (for background jobs)...
start "Queue Worker" cmd /k "SET PATH=%PATH%;C:\laragon\bin\php\php-8.2.29-Win32-vs16-x64 && php artisan queue:work --sleep=3 --tries=3"

echo.
echo ===================================================
echo   ALL SERVICES STARTED!
echo   1. WhatsApp Bridge running in another window.
echo   2. Laravel Server: http://127.0.0.1:8000
echo   3. Laravel Scheduler: checking posts every minute.
echo   4. Queue Worker: processing background tasks.
echo   KEEP ALL WINDOWS OPEN FOR AUTOMATION TO WORK.
echo ===================================================
pause
