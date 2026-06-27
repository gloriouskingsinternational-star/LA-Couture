@echo off
title L.A. Couture — Backend Server
cd /d "%~dp0backend"
echo Starting L.A. Couture API server...
echo Access at: http://127.0.0.1:8000
echo Press Ctrl+C to stop.
echo.
php artisan serve --host=127.0.0.1 --port=8000
pause
