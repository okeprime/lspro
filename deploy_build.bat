@echo off
REM ================================================================
REM  DEPLOY BUILD SCRIPT — LSPro for Hostinger
REM ================================================================
REM  Jalankan script ini di komputer lokal sebelum upload ke Hostinger
REM  
REM  CARA PAKAI:
REM    1. Buka terminal/cmd
REM    2. cd c:\laragon\www\project-lspro
REM    3. deploy_build.bat
REM ================================================================

echo.
echo ============================================
echo   LSPro — Build untuk Hostinger Deployment
echo ============================================
echo.

REM Step 1: Install composer dependencies (production)
echo [1/4] Installing Composer dependencies (production mode)...
call composer install --optimize-autoloader --no-dev
if %ERRORLEVEL% neq 0 (
    echo ❌ Composer install gagal!
    pause
    exit /b 1
)
echo ✅ Composer dependencies OK
echo.

REM Step 2: Install npm dependencies
echo [2/4] Installing NPM dependencies...
call npm install
if %ERRORLEVEL% neq 0 (
    echo ❌ NPM install gagal!
    pause
    exit /b 1
)
echo ✅ NPM dependencies OK
echo.

REM Step 3: Build frontend assets
echo [3/4] Building frontend assets (Vite + TailwindCSS)...
call npm run build
if %ERRORLEVEL% neq 0 (
    echo ❌ Build gagal!
    pause
    exit /b 1
)
echo ✅ Frontend build OK
echo.

REM Step 4: Check build output
echo [4/4] Checking build output...
if exist "public\build" (
    echo ✅ public\build\ folder berhasil dibuat
) else (
    echo ❌ public\build\ folder tidak ditemukan!
    pause
    exit /b 1
)

echo.
echo ============================================
echo   ✅ BUILD SELESAI — Siap Upload ke Hostinger
echo ============================================
echo.
echo  Langkah selanjutnya:
echo  1. Compress seluruh project (tanpa .git dan node_modules)
echo  2. Upload ke Hostinger via File Manager
echo  3. Ikuti panduan di panduan_hosting_hostinger.md
echo.
pause
