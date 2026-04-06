-- ============================================
-- APLIKASI BLOG DINAMIS - DATABASE SCHEMA
-- ============================================

CREATE DATABASE IF NOT EXISTS blog_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blog_db;

-- Tabel Users (Admin & Author)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'author') DEFAULT 'author',
    avatar VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Tag
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Artikel
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    excerpt TEXT DEFAULT NULL,
    content LONGTEXT NOT NULL,
    thumbnail VARCHAR(255) DEFAULT NULL,
    category_id INT NOT NULL,
    author_id INT NOT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    views INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Artikel-Tag (many to many)
CREATE TABLE article_tags (
    article_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (article_id, tag_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

-- Tabel Komentar
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    parent_id INT DEFAULT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    status ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES comments(id) ON DELETE CASCADE
);

-- ============================================
-- DATA AWAL (Seed Data)
-- ============================================

-- Admin user (password: admin123)
INSERT INTO users (username, email, password, full_name, role, bio) VALUES
('admin', 'admin@blog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin', 'Pengelola utama platform blog ini.'),
('johndoe', 'john@blog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', 'author', 'Penulis teknologi dan sains.');

-- Kategori
INSERT INTO categories (name, slug, description) VALUES
('Teknologi', 'teknologi', 'Artikel seputar dunia teknologi terkini'),
('Lifestyle', 'lifestyle', 'Tips dan gaya hidup modern'),
('Bisnis', 'bisnis', 'Dunia bisnis dan entrepreneurship'),
('Kesehatan', 'kesehatan', 'Informasi kesehatan dan wellness'),
('Pendidikan', 'pendidikan', 'Artikel seputar pendidikan dan pengembangan diri');

-- Tag
INSERT INTO tags (name, slug) VALUES
('PHP', 'php'), ('MySQL', 'mysql'), ('Web Development', 'web-development'),
('JavaScript', 'javascript'), ('Tips', 'tips'), ('Tutorial', 'tutorial'),
('Produktivitas', 'produktivitas'), ('Startup', 'startup');

-- Artikel contoh
INSERT INTO articles (title, slug, excerpt, content, category_id, author_id, status, views) VALUES
('Panduan Lengkap Belajar PHP untuk Pemula', 'panduan-lengkap-belajar-php',
'PHP adalah bahasa pemrograman server-side yang sangat populer untuk membangun website dinamis.',
'<p>PHP (Hypertext Preprocessor) adalah bahasa scripting server-side yang banyak digunakan dalam pengembangan web. PHP dapat diintegrasikan dengan HTML dan berjalan di server.</p><h2>Mengapa Belajar PHP?</h2><p>PHP digunakan oleh lebih dari 79% website di seluruh dunia, termasuk Facebook, Wikipedia, dan WordPress. Dengan mempelajari PHP, Anda membuka peluang karir yang sangat luas.</p><h2>Langkah Pertama</h2><p>Mulailah dengan menginstall XAMPP atau WAMP di komputer Anda. Ini akan menyediakan lingkungan PHP, MySQL, dan Apache yang siap digunakan.</p><p>Pelajari sintaks dasar seperti variabel, kondisi, perulangan, dan fungsi. Kemudian lanjutkan ke topik yang lebih advanced seperti OOP, PDO, dan framework seperti Laravel.</p>',
1, 1, 'published', 245),

('10 Tips Meningkatkan Produktivitas Kerja dari Rumah', '10-tips-produktivitas-wfh',
'Bekerja dari rumah memiliki tantangan tersendiri. Berikut 10 tips ampuh untuk tetap produktif.',
'<p>Work from home (WFH) kini menjadi gaya kerja yang umum. Namun, tanpa disiplin yang tepat, produktivitas bisa menurun drastis.</p><h2>1. Buat Jadwal Rutin</h2><p>Tetapkan jam kerja yang konsisten setiap harinya. Mulai dan selesai pada waktu yang sama seperti di kantor.</p><h2>2. Siapkan Ruang Kerja Khusus</h2><p>Pisahkan area kerja dari area istirahat. Ini membantu otak untuk "masuk mode kerja" saat berada di area tersebut.</p><h2>3. Gunakan Teknik Pomodoro</h2><p>Kerja 25 menit, istirahat 5 menit. Teknik ini terbukti meningkatkan fokus dan mengurangi kelelahan mental.</p><h2>4. Hindari Distraksi Digital</h2><p>Matikan notifikasi media sosial selama jam kerja. Gunakan aplikasi seperti Forest atau Focus@Will.</p>',
2, 2, 'published', 182),

('Cara Memulai Bisnis Online di Era Digital', 'cara-memulai-bisnis-online',
'Era digital membuka peluang bisnis yang tak terbatas. Pelajari langkah-langkah memulai bisnis online.',
'<p>Bisnis online semakin diminati karena modal yang relatif kecil namun potensi keuntungan yang besar. Berikut panduan lengkap untuk memulai.</p><h2>Riset Pasar</h2><p>Sebelum memulai, lakukan riset mendalam tentang target pasar Anda. Gunakan Google Trends, survei online, dan analisis kompetitor.</p><h2>Pilih Model Bisnis</h2><p>Ada berbagai model bisnis online: dropshipping, affiliate marketing, produk digital, jasa, atau marketplace. Pilih yang sesuai dengan keahlian dan sumber daya Anda.</p><h2>Bangun Presence Online</h2><p>Buat website profesional, aktifkan media sosial yang relevan, dan optimalkan SEO untuk meningkatkan visibilitas bisnis Anda.</p>',
3, 1, 'published', 320);

-- Tag untuk artikel
INSERT INTO article_tags VALUES (1,1),(1,2),(1,3),(1,6),(2,5),(2,7),(3,8),(3,5);

-- Komentar
INSERT INTO comments (article_id, name, email, content, status) VALUES
(1, 'Budi Santoso', 'budi@email.com', 'Artikel yang sangat bermanfaat! Saya sudah mencoba tutorial ini dan berhasil.', 'approved'),
(1, 'Sari Dewi', 'sari@email.com', 'Terima kasih tutorialnya, sangat jelas dan mudah dipahami untuk pemula seperti saya.', 'approved'),
(2, 'Ahmad Fauzi', 'ahmad@email.com', 'Tips yang sangat berguna! Saya sudah menerapkan teknik Pomodoro dan terasa jauh lebih produktif.', 'approved'),
(3, 'Rina Kusuma', 'rina@email.com', 'Artikel yang inspiratif! Saya sedang mempertimbangkan untuk memulai bisnis dropshipping.', 'approved');

-- ============================================
-- MIGRATION: Tambah kolom status ke tabel users
-- ============================================
ALTER TABLE users 
  ADD COLUMN status ENUM('pending','active','suspended') NOT NULL DEFAULT 'active' AFTER role,
  ADD COLUMN avatar VARCHAR(255) DEFAULT NULL AFTER bio,
  ADD COLUMN avatar_path VARCHAR(255) DEFAULT NULL AFTER avatar;

-- Update user lama agar tetap active
UPDATE users SET status = 'active';
