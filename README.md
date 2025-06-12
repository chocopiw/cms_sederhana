# CMS Sederhana - Framework MVC

Sebuah Content Management System (CMS) yang dibangun menggunakan Framework MVC custom dengan PHP.

## 🚀 Fitur

- **Framework MVC Custom**: Struktur yang terorganisir dan mudah dikembangkan
- **Authentication System**: Login/logout dengan session management
- **Dashboard Modern**: Interface yang responsif dan user-friendly
- **Post Management**: CRUD untuk artikel/posts
- **Category Management**: Pengelolaan kategori
- **User Management**: Sistem user dengan role
- **Visitor Tracking**: Statistik pengunjung website
- **Responsive Design**: Menggunakan Bootstrap 5
- **Security Features**: SQL injection protection, XSS protection

## 📁 Struktur Folder

```
cms_sederhana/
├── app/
│   ├── config/
│   │   ├── config.php          # Konfigurasi aplikasi
│   │   └── database.php        # Setup database
│   ├── Controllers/
│   │   ├── AuthController.php  # Authentication
│   │   ├── DashboardController.php
│   │   ├── PostController.php
│   │   ├── CategoryController.php
│   │   └── UserController.php
│   ├── Core/
│   │   ├── Controller.php      # Base controller
│   │   ├── Database.php        # Database wrapper
│   │   └── Router.php          # URL routing
│   └── views/
│       ├── auth/
│       │   └── login.php       # Login page
│       └── dashboard/
│           └── index.php       # Dashboard
├── public/
│   └── uploads/                # File uploads
├── .htaccess                   # URL rewriting
├── index.php                   # Entry point
└── README.md                   # Dokumentasi
```

## 🛠️ Instalasi

### 1. Persyaratan Sistem
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Apache/Nginx dengan mod_rewrite enabled
- XAMPP/WAMP/LAMP

### 2. Setup Database
1. Buat database baru di MySQL:
```sql
CREATE DATABASE cms_sederhana;
```

2. Import struktur database (akan dibuat otomatis saat pertama kali diakses)

### 3. Konfigurasi
1. Edit file `app/config/config.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'cms_sederhana');
define('DB_USER', 'root');
define('DB_PASS', '');
```

2. Pastikan mod_rewrite Apache aktif

### 4. Akses Website
- URL: `http://localhost/cms_sederhana/`
- Login: `http://localhost/cms_sederhana/login`
- Default credentials: `admin` / `admin123`

## 🎯 Penggunaan

### Login
1. Buka `http://localhost/cms_sederhana/login`
2. Masukkan username: `admin` dan password: `admin123`
3. Klik "Login"

### Dashboard
Setelah login, Anda akan diarahkan ke dashboard yang menampilkan:
- Statistik posts, categories, users, dan visitors
- Recent posts
- Visitor statistics
- Menu navigasi

### Menambah Post
1. Klik menu "Posts" di sidebar
2. Klik "Create New Post"
3. Isi form dan submit

### Mengelola Categories
1. Klik menu "Categories" di sidebar
2. Tambah, edit, atau hapus kategori

## 🔧 Framework MVC

### Router
```php
$router->get('/dashboard', 'DashboardController@index');
$router->post('/login', 'AuthController@login');
```

### Controller
```php
class DashboardController extends Controller
{
    public function index()
    {
        $data = ['title' => 'Dashboard'];
        $this->view('dashboard/index', $data);
    }
}
```

### Database
```php
$db = Database::getInstance();
$users = $db->fetchAll("SELECT * FROM users");
$db->insert('users', ['username' => 'john', 'email' => 'john@example.com']);
```

### View
```php
<!-- app/views/dashboard/index.php -->
<h1><?php echo $title; ?></h1>
```

## 🛡️ Security Features

- **SQL Injection Protection**: Menggunakan prepared statements
- **XSS Protection**: Output escaping
- **CSRF Protection**: Token validation
- **Session Security**: Secure session handling
- **File Upload Security**: Validasi file type dan size

## 🎨 Customization

### Mengubah Tema
Edit CSS di file view atau tambahkan file CSS custom di `public/css/`

### Menambah Fitur
1. Buat controller baru di `app/Controllers/`
2. Tambah route di `index.php`
3. Buat view di `app/views/`

### Database Schema
Edit file `app/config/database.php` untuk menambah tabel atau kolom baru

## 📝 API Endpoints

### Authentication
- `GET /login` - Login form
- `POST /login` - Process login
- `GET /logout` - Logout

### Dashboard
- `GET /dashboard` - Main dashboard

### Posts
- `GET /dashboard/posts` - List posts
- `GET /dashboard/posts/create` - Create form
- `POST /dashboard/posts/store` - Store post
- `GET /dashboard/posts/edit/{id}` - Edit form
- `POST /dashboard/posts/update/{id}` - Update post
- `GET /dashboard/posts/delete/{id}` - Delete post

### Categories
- `GET /dashboard/categories` - List categories
- `GET /dashboard/categories/create` - Create form
- `POST /dashboard/categories/store` - Store category
- `GET /dashboard/categories/edit/{id}` - Edit form
- `POST /dashboard/categories/update/{id}` - Update category
- `GET /dashboard/categories/delete/{id}` - Delete category

## 🐛 Troubleshooting

### Error 404
- Pastikan mod_rewrite aktif
- Cek file `.htaccess`
- Restart Apache

### Database Error
- Cek konfigurasi database di `app/config/config.php`
- Pastikan database sudah dibuat
- Cek koneksi MySQL

### Permission Error
- Pastikan folder `public/uploads/` writable
- Cek permission file dan folder

## 📄 License

MIT License - bebas digunakan untuk keperluan komersial dan non-komersial.

## 🤝 Contributing

1. Fork repository
2. Buat branch baru
3. Commit changes
4. Push ke branch
5. Buat Pull Request

## 📞 Support

Jika ada pertanyaan atau masalah, silakan buat issue di repository atau hubungi developer.

---

**Dibuat dengan ❤️ menggunakan Framework MVC Custom**