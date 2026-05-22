# ============================================================
#  BeautyHub — Script Update Otomatis (PowerShell)
#  Jalankan dari folder ROOT project: .\UPDATE.ps1
# ============================================================

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  BeautyHub — Update Otomatis" -ForegroundColor Cyan
Write-Host "  Target: $root" -ForegroundColor Gray
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

function CopyFile($src, $dst) {
    $dstDir = Split-Path -Parent (Join-Path $root $dst)
    if (!(Test-Path $dstDir)) { New-Item -ItemType Directory -Path $dstDir -Force | Out-Null }
    Copy-Item -Path (Join-Path $root $src) -Destination (Join-Path $root $dst) -Force
    Write-Host "  [OK] $dst" -ForegroundColor Green
}

Write-Host ">> [1/6] App Models..." -ForegroundColor Yellow
CopyFile "app/Models/User.php"       "app/Models/User.php"
CopyFile "app/Models/Mua.php"        "app/Models/Mua.php"
CopyFile "app/Models/Booking.php"    "app/Models/Booking.php"
CopyFile "app/Models/Service.php"    "app/Models/Service.php"
CopyFile "app/Models/Portfolio.php"  "app/Models/Portfolio.php"
CopyFile "app/Models/Review.php"     "app/Models/Review.php"
CopyFile "app/Models/ChatbotLog.php" "app/Models/ChatbotLog.php"

Write-Host ""
Write-Host ">> [2/6] Controllers..." -ForegroundColor Yellow
CopyFile "app/Http/Controllers/Mua/AuthController.php"         "app/Http/Controllers/Mua/AuthController.php"
CopyFile "app/Http/Controllers/Mua/DashboardController.php"    "app/Http/Controllers/Mua/DashboardController.php"
CopyFile "app/Http/Controllers/Mua/BookingController.php"      "app/Http/Controllers/Mua/BookingController.php"
CopyFile "app/Http/Controllers/Mua/PortfolioController.php"    "app/Http/Controllers/Mua/PortfolioController.php"
CopyFile "app/Http/Controllers/Mua/ServiceController.php"      "app/Http/Controllers/Mua/ServiceController.php"
CopyFile "app/Http/Controllers/Mua/ProfileController.php"      "app/Http/Controllers/Mua/ProfileController.php"
CopyFile "app/Http/Controllers/Mua/VerificationController.php" "app/Http/Controllers/Mua/VerificationController.php"
CopyFile "app/Http/Controllers/Api/AuthController.php"         "app/Http/Controllers/Api/AuthController.php"
CopyFile "app/Http/Controllers/Api/MuaApiController.php"       "app/Http/Controllers/Api/MuaApiController.php"
CopyFile "app/Http/Controllers/Api/BookingApiController.php"   "app/Http/Controllers/Api/BookingApiController.php"
CopyFile "app/Http/Controllers/Api/ReviewApiController.php"    "app/Http/Controllers/Api/ReviewApiController.php"
CopyFile "app/Http/Controllers/Api/ChatbotApiController.php"   "app/Http/Controllers/Api/ChatbotApiController.php"
CopyFile "app/Http/Controllers/Api/SearchApiController.php"    "app/Http/Controllers/Api/SearchApiController.php"

Write-Host ""
Write-Host ">> [3/6] Middleware, Routes, Bootstrap..." -ForegroundColor Yellow
CopyFile "app/Http/Middleware/CheckRole.php" "app/Http/Middleware/CheckRole.php"
CopyFile "app/Providers/AppServiceProvider.php" "app/Providers/AppServiceProvider.php"
CopyFile "bootstrap/app.php"   "bootstrap/app.php"
CopyFile "routes/web.php"      "routes/web.php"
CopyFile "routes/api.php"      "routes/api.php"

Write-Host ""
Write-Host ">> [4/6] Config..." -ForegroundColor Yellow
CopyFile "config/auth.php"    "config/auth.php"
CopyFile "config/session.php" "config/session.php"

Write-Host ""
Write-Host ">> [5/6] Database..." -ForegroundColor Yellow
CopyFile "database/migrations/2026_04_02_034851_create_permission_tables.php" "database/migrations/2026_04_02_034851_create_permission_tables.php"
CopyFile "database/migrations/2026_04_02_132803_create_users_table.php"       "database/migrations/2026_04_02_132803_create_users_table.php"
CopyFile "database/migrations/2026_04_14_035325_create_beautyhub_tables.php"  "database/migrations/2026_04_14_035325_create_beautyhub_tables.php"
CopyFile "database/seeders/DatabaseSeeder.php" "database/seeders/DatabaseSeeder.php"

Write-Host ""
Write-Host ">> [6/6] Views..." -ForegroundColor Yellow
CopyFile "resources/views/layouts/mua.blade.php"          "resources/views/layouts/mua.blade.php"
CopyFile "resources/views/mua/login.blade.php"            "resources/views/mua/login.blade.php"
CopyFile "resources/views/mua/dashboard.blade.php"        "resources/views/mua/dashboard.blade.php"
CopyFile "resources/views/mua/profile.blade.php"          "resources/views/mua/profile.blade.php"
CopyFile "resources/views/mua/verification.blade.php"     "resources/views/mua/verification.blade.php"
CopyFile "resources/views/mua/bookings/index.blade.php"   "resources/views/mua/bookings/index.blade.php"
CopyFile "resources/views/mua/bookings/show.blade.php"    "resources/views/mua/bookings/show.blade.php"
CopyFile "resources/views/mua/portfolio/index.blade.php"  "resources/views/mua/portfolio/index.blade.php"
CopyFile "resources/views/mua/services/index.blade.php"   "resources/views/mua/services/index.blade.php"
CopyFile "resources/views/mua/services/_form.blade.php"   "resources/views/mua/services/_form.blade.php"

# ── Setup .env jika belum ada ─────────────────────────────
Write-Host ""
if (!(Test-Path (Join-Path $root ".env"))) {
    Copy-Item (Join-Path $root ".env.example") (Join-Path $root ".env")
    Write-Host "  [OK] .env dibuat dari .env.example" -ForegroundColor Green
    Write-Host "  [!!] Edit .env: isi DB_DATABASE, DB_USERNAME, DB_PASSWORD" -ForegroundColor Red
} else {
    Write-Host "  [--] .env sudah ada, tidak ditimpa" -ForegroundColor Gray
}

# ── Jalankan artisan commands ─────────────────────────────
Write-Host ""
Write-Host ">> Menjalankan artisan commands..." -ForegroundColor Yellow
Set-Location $root

Write-Host "  php artisan key:generate..." -ForegroundColor Gray
php artisan key:generate

Write-Host "  php artisan jwt:secret..." -ForegroundColor Gray
php artisan jwt:secret

Write-Host "  php artisan config:clear..." -ForegroundColor Gray
php artisan config:clear

Write-Host "  php artisan cache:clear..." -ForegroundColor Gray
php artisan cache:clear

Write-Host "  php artisan storage:link..." -ForegroundColor Gray
php artisan storage:link

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  SELESAI! Langkah selanjutnya:" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "  1. Pastikan .env sudah benar:" -ForegroundColor White
Write-Host "     DB_CONNECTION=mysql" -ForegroundColor Gray
Write-Host "     DB_DATABASE=beautyhub_db" -ForegroundColor Gray
Write-Host "     DB_USERNAME=root" -ForegroundColor Gray
Write-Host "     DB_PASSWORD=   (kosong jika Laragon)" -ForegroundColor Gray
Write-Host ""
Write-Host "  2. Karena DB sudah ada data Flutter, jalankan:" -ForegroundColor White
Write-Host "     php artisan migrate  <-- BUKAN migrate:fresh" -ForegroundColor Yellow
Write-Host ""
Write-Host "  3. Fix role user yang salah (mua@gmail.com jadi admin):" -ForegroundColor White
Write-Host "     php artisan tinker" -ForegroundColor Yellow
Write-Host "     >>> App\Models\User::where('email','mua@gmail.com')->update(['role'=>'admin']);" -ForegroundColor Yellow
Write-Host "     >>> exit" -ForegroundColor Yellow
Write-Host ""
Write-Host "  4. Jalankan server:" -ForegroundColor White
Write-Host "     php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "  Login: http://localhost:8000/mua/login" -ForegroundColor Cyan
Write-Host "  Email: mua@gmail.com  |  Password: (sesuai yg didaftarkan)" -ForegroundColor Gray
Write-Host ""
