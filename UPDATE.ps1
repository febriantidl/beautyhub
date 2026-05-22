# ============================================================
#  BeautyHub — Script Update Otomatis (PowerShell)
#  Jalankan dari folder ROOT project:  .\UPDATE.ps1
# ============================================================

$root = Split-Path -Parent $MyInvocation.MyCommand.Path
Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  BeautyHub — Update Otomatis (Final)"   -ForegroundColor Cyan
Write-Host "  Target: $root"                          -ForegroundColor Gray
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

function CopyFile($dst) {
    $src    = Join-Path $root $dst
    $dstFull = Join-Path $root $dst
    $dstDir = Split-Path -Parent $dstFull
    if (!(Test-Path $dstDir)) { New-Item -ItemType Directory -Path $dstDir -Force | Out-Null }
    if (Test-Path $src) {
        Copy-Item -Path $src -Destination $dstFull -Force
        Write-Host "  [OK] $dst" -ForegroundColor Green
    } else {
        Write-Host "  [!!] SKIP (tidak ditemukan): $dst" -ForegroundColor Red
    }
}

Write-Host ">> [1/8] Models..." -ForegroundColor Yellow
foreach ($f in @(
    "app/Models/User.php","app/Models/Mua.php","app/Models/Booking.php",
    "app/Models/Service.php","app/Models/Portfolio.php",
    "app/Models/Review.php","app/Models/ChatbotLog.php"
)) { CopyFile $f }

Write-Host ""
Write-Host ">> [2/8] MUA Controllers..." -ForegroundColor Yellow
foreach ($f in @(
    "app/Http/Controllers/Mua/AuthController.php",
    "app/Http/Controllers/Mua/DashboardController.php",
    "app/Http/Controllers/Mua/BookingController.php",
    "app/Http/Controllers/Mua/PortfolioController.php",
    "app/Http/Controllers/Mua/ServiceController.php",
    "app/Http/Controllers/Mua/ProfileController.php",
    "app/Http/Controllers/Mua/VerificationController.php"
)) { CopyFile $f }

Write-Host ""
Write-Host ">> [3/8] Admin Controllers..." -ForegroundColor Yellow
foreach ($f in @(
    "app/Http/Controllers/Admin/AdminController.php",
    "app/Http/Controllers/Admin/MuaManagementController.php",
    "app/Http/Controllers/Admin/UserManagementController.php"
)) { CopyFile $f }

Write-Host ""
Write-Host ">> [4/8] API Controllers..." -ForegroundColor Yellow
foreach ($f in @(
    "app/Http/Controllers/Api/AuthController.php",
    "app/Http/Controllers/Api/MuaApiController.php",
    "app/Http/Controllers/Api/BookingApiController.php",
    "app/Http/Controllers/Api/ReviewApiController.php",
    "app/Http/Controllers/Api/ChatbotApiController.php",
    "app/Http/Controllers/Api/SearchApiController.php"
)) { CopyFile $f }

Write-Host ""
Write-Host ">> [5/8] Middleware, Routes, Bootstrap, Config..." -ForegroundColor Yellow
foreach ($f in @(
    "app/Http/Middleware/CheckRole.php",
    "app/Providers/AppServiceProvider.php",
    "bootstrap/app.php",
    "routes/web.php","routes/api.php",
    "config/auth.php","config/session.php"
)) { CopyFile $f }

Write-Host ""
Write-Host ">> [6/8] Database..." -ForegroundColor Yellow
foreach ($f in @(
    "database/migrations/2026_04_02_034851_create_permission_tables.php",
    "database/migrations/2026_04_02_132803_create_users_table.php",
    "database/migrations/2026_04_14_035325_create_beautyhub_tables.php",
    "database/seeders/DatabaseSeeder.php"
)) { CopyFile $f }

Write-Host ""
Write-Host ">> [7/8] Views MUA..." -ForegroundColor Yellow
foreach ($f in @(
    "resources/views/layouts/mua.blade.php",
    "resources/views/layouts/admin.blade.php",
    "resources/views/mua/login.blade.php",
    "resources/views/mua/dashboard.blade.php",
    "resources/views/mua/profile.blade.php",
    "resources/views/mua/verification.blade.php",
    "resources/views/mua/bookings/index.blade.php",
    "resources/views/mua/bookings/show.blade.php",
    "resources/views/mua/portfolio/index.blade.php",
    "resources/views/mua/services/index.blade.php",
    "resources/views/mua/services/_form.blade.php"
)) { CopyFile $f }

Write-Host ""
Write-Host ">> [8/8] Views Admin + Error Pages..." -ForegroundColor Yellow
foreach ($f in @(
    "resources/views/admin/dashboard.blade.php",
    "resources/views/admin/muas/index.blade.php",
    "resources/views/admin/muas/show.blade.php",
    "resources/views/admin/users/index.blade.php",
    "resources/views/admin/users/show.blade.php",
    "resources/views/errors/404.blade.php",
    "resources/views/errors/500.blade.php"
)) { CopyFile $f }

# ── Setup .env ────────────────────────────────────────────────
Write-Host ""
if (!(Test-Path (Join-Path $root ".env"))) {
    Copy-Item (Join-Path $root ".env.example") (Join-Path $root ".env")
    Write-Host "  [OK] .env dibuat dari .env.example" -ForegroundColor Green
    Write-Host "  [!!] Edit .env: isi DB_DATABASE=beautyhub_db, DB_USERNAME=root, DB_PASSWORD=" -ForegroundColor Red
} else {
    Write-Host "  [--] .env sudah ada, tidak ditimpa" -ForegroundColor Gray
}

# ── Artisan commands ──────────────────────────────────────────
Write-Host ""
Write-Host ">> Menjalankan artisan commands..." -ForegroundColor Yellow
Set-Location $root

php artisan key:generate --ansi
php artisan jwt:secret --force
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan storage:link

# ── Selesai ───────────────────────────────────────────────────
Write-Host ""
Write-Host "=========================================" -ForegroundColor Green
Write-Host "  SELESAI! Langkah selanjutnya:"          -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Green
Write-Host ""
Write-Host "  1. Pastikan .env:" -ForegroundColor White
Write-Host "     DB_CONNECTION = mysql"      -ForegroundColor Gray
Write-Host "     DB_DATABASE   = beautyhub_db" -ForegroundColor Gray
Write-Host "     DB_USERNAME   = root"       -ForegroundColor Gray
Write-Host "     DB_PASSWORD   =  (kosong)"  -ForegroundColor Gray
Write-Host ""
Write-Host "  2. Karena DB sudah ada data Flutter:" -ForegroundColor White
Write-Host "     php artisan migrate" -ForegroundColor Yellow
Write-Host "     (BUKAN migrate:fresh, data tidak hilang)" -ForegroundColor Red
Write-Host ""
Write-Host "  3. Fix role user mua@gmail.com:" -ForegroundColor White
Write-Host "     php artisan tinker" -ForegroundColor Yellow
Write-Host '     >>> App\Models\User::where("email","mua@gmail.com")->update(["role"=>"admin"]);' -ForegroundColor Yellow
Write-Host "     >>> exit" -ForegroundColor Yellow
Write-Host ""
Write-Host "  4. Jalankan server:" -ForegroundColor White
Write-Host "     php artisan serve" -ForegroundColor Yellow
Write-Host ""
Write-Host "  URL LOGIN   : http://localhost:8000/mua/login" -ForegroundColor Cyan
Write-Host "  MUA Panel   : http://localhost:8000/mua/dashboard" -ForegroundColor Cyan
Write-Host "  Admin Panel : http://localhost:8000/admin/dashboard" -ForegroundColor Cyan
Write-Host ""
Write-Host "  Login pakai email & password sesuai DB kamu." -ForegroundColor Gray
Write-Host ""