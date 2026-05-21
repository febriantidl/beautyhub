# 🌸 BeautyHub — Panduan Instalasi

Platform pemesanan MUA (Make-Up Artist) lokal berbasis Laravel 12.

## Prasyarat

- PHP 8.2+
- Composer
- Node.js 18+ & NPM

## Langkah Instalasi

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```

### 3. Database

**SQLite (mudah, direkomendasikan untuk lokal):**
```bash
touch database/database.sqlite
php artisan migrate:fresh --seed
```

**MySQL:**
Buat database `beautyhub`, edit `.env` (ubah `DB_CONNECTION=mysql` dan isi kredensial), lalu:
```bash
php artisan migrate:fresh --seed
```

### 4. Storage Link

```bash
php artisan storage:link
```

### 5. Jalankan

```bash
php artisan serve
npm run dev   # di terminal terpisah (opsional, untuk Tailwind)
```

Buka http://127.0.0.1:8000

---

## Akun Default

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@beautyhub.com | password |
| MUA | iah@beautyhub.com | password |
| MUA | siti@beautyhub.com | password |
| Customer | rina@gmail.com | password |

---

## Struktur Fitur

### Web Panel (MUA/Admin)
| URL | Deskripsi |
|-----|-----------|
| `/mua/login` | Login MUA |
| `/mua/dashboard` | Dashboard statistik |
| `/mua/bookings` | Kelola semua booking |
| `/mua/portfolio` | Upload & kelola portfolio |
| `/mua/services` | Kelola layanan & harga |
| `/mua/verification` | Verifikasi QR kedatangan |
| `/mua/profile` | Edit profil & password |

### REST API (Mobile App / Customer)
Base URL: `/api`

| Method | Endpoint | Auth | Deskripsi |
|--------|----------|------|-----------|
| POST | `/register` | ❌ | Daftar customer |
| POST | `/login` | ❌ | Login (JWT) |
| POST | `/logout` | ✅ | Logout |
| GET | `/me` | ✅ | Info user login |
| GET | `/muas` | ✅ | List MUA + filter |
| GET | `/muas/{id}` | ✅ | Detail MUA |
| GET | `/muas/{id}/portfolio` | ✅ | Portfolio MUA |
| GET | `/muas/{id}/reviews` | ✅ | Ulasan MUA |
| POST | `/bookings` | ✅ | Buat booking |
| GET | `/bookings/my` | ✅ | Booking saya |
| PUT | `/bookings/{id}/cancel` | ✅ | Batalkan booking |
| POST | `/reviews` | ✅ | Beri ulasan |
| POST | `/chatbot/message` | ✅ | Chat AI |
| POST | `/search/by-image` | ✅ | Cari MUA by gambar |

**Auth Header:** `Authorization: Bearer {token}`
