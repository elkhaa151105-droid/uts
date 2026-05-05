[readme_blog_web_dinamis_markdown.md](https://github.com/user-attachments/files/26902980/readme_blog_web_dinamis_markdown.md)
# Blog Web Dinamis

Aplikasi blog berbasis PHP Native dan MySQL dengan fitur artikel, kategori, tag, komentar, autentikasi pengguna, dashboard admin, dan manajemen user.

---

## Fitur Utama

### Frontend / Pengunjung
- Menampilkan daftar artikel yang sudah dipublish
- Pagination artikel
- Filter artikel berdasarkan kategori
- Filter artikel berdasarkan tag
- Pencarian artikel
- Halaman detail artikel
- Sidebar kategori dan tag
- Tampilan responsif sederhana

### Autentikasi
- Login menggunakan username atau email
- Register akun baru
- Logout
- Password di-hash menggunakan `password_hash()`
- Validasi akun pending / suspended

### Dashboard Admin / Author
- Statistik artikel, komentar, views, dan user pending
- Daftar artikel terbaru
- Manajemen artikel
  - Tambah artikel
  - Edit artikel
  - Hapus artikel
- Manajemen kategori
- Manajemen komentar
- Manajemen user
- Edit profil pengguna

---

## Struktur Folder

```bash
blog/
│
├── admin/
│   ├── dashboard.php
│   ├── artikel-baru.php
│   ├── artikel-edit.php
│   ├── artikel-hapus.php
│   ├── artikel-list.php
│   ├── kategori.php
│   ├── komentar.php
│   ├── komentar-aksi.php
│   ├── profil.php
│   ├── users.php
│   └── partials/
│       ├── admin-nav.php
│       └── admin-sidebar.php
│
├── assets/
│   └── style.css
│
├── partials/
│   ├── header.php
│   ├── footer.php
│   └── sidebar.php
│
├── config.php
├── database.sql
├── migration_update.sql
├── migrate.php
├── index.php
├── artikel.php
├── login.php
├── register.php
├── logout.php
└── check_upload.php
```

---

## Teknologi yang Digunakan

- PHP Native
- MySQL / MariaDB
- PDO
- HTML5
- CSS3
- Session Authentication

---

## Cara Instalasi

### 1. Clone / Copy Project

Pindahkan folder project ke direktori web server.

Contoh:

```bash
htdocs/blog
```

### 2. Import Database

- Buka phpMyAdmin
- Buat database baru bernama:

```sql
blog_db
```

- Import file:

```bash
database.sql
```

### 3. Konfigurasi Database

Buka file:

```php
config.php
```

Sesuaikan konfigurasi berikut:

```php
$dbHost = 'localhost';
$dbName = 'blog_db';
$dbUser = 'root';
$dbPass = '';
```

### 4. Jalankan Migration Tambahan

Jika ada update struktur tabel, jalankan:

```bash
migration_update.sql
```

Atau buka file:

```bash
migrate.php
```

### 5. Jalankan Project

Akses melalui browser:

```bash
http://localhost/blog
```

---

## Akun Default

Tambahkan akun admin langsung melalui database.

Contoh query:

```sql
INSERT INTO users (
    username,
    email,
    password,
    full_name,
    role,
    status
) VALUES (
    'admin',
    'admin@gmail.com',
    '$2y$10$examplehashpassword',
    'Administrator',
    'admin',
    'active'
);
```

Password dapat dibuat menggunakan:

```php
password_hash('admin123', PASSWORD_DEFAULT)
```

---

## Role User

### Admin
- Mengelola semua artikel
- Mengelola semua user
- Mengelola kategori
- Mengelola komentar
- Melihat seluruh statistik

### Author
- Membuat artikel sendiri
- Mengedit artikel sendiri
- Menghapus artikel sendiri
- Mengelola profil pribadi

---

## Tabel Database Utama

- `users`
- `articles`
- `categories`
- `tags`
- `article_tags`
- `comments`

---

## Keamanan Dasar

- Password disimpan dalam bentuk hash
- Query menggunakan PDO Prepared Statement
- Session digunakan untuk autentikasi
- Validasi input form
- Role-based access sederhana

---

## Pengembangan Selanjutnya

Beberapa fitur yang dapat ditambahkan:

- Upload thumbnail artikel
- Rich text editor
- Dashboard analytics lebih lengkap
- Sistem like artikel
- Notifikasi komentar
- Forgot password
- Email verification
- SEO meta tag
- Sitemap otomatis
- Dark mode

video penjelasan web
https://youtu.be/303kXxuZb4Y?si=hgfrZLPtMB6Gtt8w

---

## Author

Dibuat menggunakan PHP Native dan MySQL untuk kebutuhan website blog dinamis sederhana.

