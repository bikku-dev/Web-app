@echo off
title Bhumi Technocare System - Web Application
color 0B
echo =====================================================================
echo         BHUMI TECHNOCARE SYSTEM - FIELD SERVICE MANAGEMENT
echo =====================================================================
echo.
echo [1/3] Checking MySQL Server...
netstat -ano | findstr :3306 >nul
if %errorlevel% neq 0 (
    echo Starting local MySQL Server daemon on port 3306...
    start /b "" "C:\Program Files\MySQL\MySQL Server 9.7\bin\mysqld.exe" --datadir="d:\Client 1\mysql_data" --port=3306 --console
    timeout /t 3 /nobreak >nul
) else (
    echo MySQL Server is already running on port 3306.
)

echo.
echo [2/3] Starting Laravel Application Server on http://127.0.0.1:8000 ...
echo (No Vite / No NPM required - Tailwind CDN & Blade PHP active)
echo.
echo =====================================================================
echo   ADMIN LOGIN:      9999999999  /  password123
echo   ENGINEER LOGIN:   9876543210  /  password123
echo =====================================================================
echo.

start http://127.0.0.1:8000/login
php artisan serve --host=127.0.0.1 --port=8000

pause
