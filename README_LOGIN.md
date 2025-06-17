# CMS Sederhana - Login & Register System

## Fitur yang Tersedia

### 1. Halaman Login Baru (`login_new.php`)
- Desain modern dengan gradient background
- Form login yang bersih dan responsif
- Toggle password visibility
- Validasi form
- Redirect ke dashboard setelah login berhasil

### 2. Halaman Register Baru (`register_new.php`)
- Form pendaftaran dengan validasi lengkap
- Validasi username (minimal 3 karakter, hanya huruf, angka, underscore)
- Validasi email format
- Validasi password (minimal 6 karakter)
- Konfirmasi password
- Cek duplikasi username dan email
- Toggle password visibility untuk kedua field password

### 3. Halaman Index Baru (`index_new.php`)
- Redirect otomatis ke halaman login baru
- Cek session untuk user yang sudah login

## Cara Penggunaan

### 1. Setup Database
Jalankan file `update_database.php` untuk menambahkan kolom email ke tabel users:
```
http://localhost/cms_sederhana/update_database.php
```

### 2. Akses Halaman
- **Login**: `http://localhost/cms_sederhana/login_new.php`
- **Register**: `http://localhost/cms_sederhana/register_new.php`
- **Index**: `http://localhost/cms_sederhana/index_new.php`

### 3. Default Admin Account
- Username: `admin`
- Password: `admin123`
- Email: `admin@example.com`

## Fitur Keamanan

1. **Password Hashing**: Menggunakan `password_hash()` untuk enkripsi password
2. **SQL Injection Protection**: Menggunakan prepared statements
3. **XSS Protection**: Menggunakan `htmlspecialchars()` untuk output
4. **Session Management**: Menggunakan PHP sessions untuk autentikasi
5. **Input Validation**: Validasi lengkap untuk semua input user

## Desain

- **Responsive Design**: Bekerja dengan baik di desktop dan mobile
- **Modern UI**: Gradient background, card design, smooth animations
- **User Friendly**: Label yang jelas, placeholder text, error messages
- **Accessibility**: Proper form labels dan keyboard navigation

## File yang Dibuat

1. `login_new.php` - Halaman login dengan desain baru
2. `register_new.php` - Halaman register dengan desain baru
3. `index_new.php` - Halaman index yang redirect ke login
4. `update_database.php` - Script untuk update database
5. `update_database.sql` - SQL script untuk update database

## Struktur Database

Tabel `users`:
- `id` (INT, AUTO_INCREMENT, PRIMARY KEY)
- `username` (VARCHAR(50), UNIQUE)
- `email` (VARCHAR(100), UNIQUE)
- `password` (VARCHAR(255))
- `created_at` (TIMESTAMP)

## Troubleshooting

### Jika ada error database:
1. Pastikan database `cms_sederhana` sudah dibuat
2. Jalankan `update_database.php` untuk menambahkan kolom email
3. Pastikan koneksi database di `config/database.php` sudah benar

### Jika login tidak berfungsi:
1. Pastikan tabel `users` sudah ada
2. Cek apakah user admin sudah terdaftar
3. Pastikan password di database sudah di-hash

### Jika register tidak berfungsi:
1. Pastikan kolom email sudah ditambahkan ke tabel users
2. Cek apakah ada constraint UNIQUE yang konflik
3. Pastikan semua validasi berjalan dengan benar 