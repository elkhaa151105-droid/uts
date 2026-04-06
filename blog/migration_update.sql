-- ============================================
-- JALANKAN INI jika database sudah ada sebelumnya
-- ============================================
USE blog_db;

ALTER TABLE users 
  ADD COLUMN IF NOT EXISTS status ENUM('pending','active','suspended') NOT NULL DEFAULT 'active' AFTER role,
  ADD COLUMN IF NOT EXISTS avatar_path VARCHAR(255) DEFAULT NULL AFTER bio;

-- Pastikan user lama tetap active
UPDATE users SET status = 'active' WHERE status IS NULL OR status = '';
