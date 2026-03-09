@echo off
echo Starting WhatsApp Status Bridge...
cd wa-bridge
start cmd /k "node server.js"
timeout /t 5
echo Exposing bridge to public URL...
npx localtunnel --port 3000
pause
