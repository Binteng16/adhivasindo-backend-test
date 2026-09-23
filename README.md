# Take Home Test Adhivasindo - REST API

REST API menggunakan Laravel, Laravel Sanctum, dan arsitektur Service-Resource-Trait.

---

## Cara Menjalankan

1. Clone repository dan masuk ke folder proyek: `git clone <URL_REPO>` lalu `cd <FOLDER_PROYEK>`
2. Pasang dependensi: `composer install`
3. Salin environment dan generate key: `cp .env.example .env` lalu `php artisan key:generate`
4. Sesuaikan konfigurasi database pada file `.env`
5. Jalankan migrasi dan seeder: `php artisan migrate --seed`
6. Jalankan server lokal: `php artisan serve`

---

## Daftar Endpoint

Header wajib: `Accept: application/json`  
Endpoint berlabel *(Protected)* wajib menyertakan header: `Authorization: Bearer <token>`

### Authentication
* `POST /api/login` — Login & dapatkan Bearer Token
* `POST /api/logout` — Logout & hapus token aktif *(Protected)*

### CRUD Users *(Protected)*
* `GET /api/users` — Ambil semua user
* `POST /api/users` — Tambah user baru
* `GET /api/users/{id}` — Detail user
* `PUT /api/users/{id}` — Update user
* `DELETE /api/users/{id}` — Hapus user

### External Search *(Protected, Real-Time Data)*
* `GET /api/search/nama?nama=Turner Mia`
* `GET /api/search/nim?nim=9352078461`
* `GET /api/search/ymd?ymd=20230405`

---

## Dokumentasi Postman

File koleksi Postman juga tersedia langsung di root folder:
`Adhivasindo.postman.json`.
