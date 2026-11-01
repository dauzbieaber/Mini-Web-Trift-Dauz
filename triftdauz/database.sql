-- PEMBUATAN DATABASE
-- Digunakan untuk membuat database triftdauz_db dan memilihnya.
CREATE DATABASE IF NOT EXISTS triftdauz_db;
USE triftdauz_db;

-- TABEL USERS
-- Digunakan untuk menyimpan data admin yang register dan login.
CREATE TABLE IF NOT EXISTS users (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY email (email)
);

-- TABEL PRODUK
-- Digunakan untuk menyimpan data produk thrift/preloved.
CREATE TABLE IF NOT EXISTS produk (
    id INT(11) NOT NULL AUTO_INCREMENT,
    nama_produk VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) NOT NULL,
    harga INT(11) NOT NULL,
    ukuran VARCHAR(20) NOT NULL,
    kondisi VARCHAR(50) NOT NULL,
    deskripsi TEXT NOT NULL,
    gambar VARCHAR(255) NOT NULL DEFAULT '',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- DATA AWAL PRODUK
-- Digunakan untuk mengisi 6 produk contoh agar pagination langsung bisa dicoba.
-- Kolom gambar dikosongkan karena file foto belum ada.
INSERT INTO produk (nama_produk, kategori, harga, ukuran, kondisi, deskripsi, gambar) VALUES
('Sepatu Converse Chuck Taylor Hitam', 'Sepatu', 250000, '42', 'Bekas Baik', 'Sepatu converse high top warna hitam, sol masih tebal, tidak ada robek.', ''),
('Kemeja Flanel Kotak Merah', 'Baju', 85000, 'L', 'Bekas Baik', 'Kemeja flanel lengan panjang motif kotak merah, bahan tebal dan nyaman.', ''),
('Jaket Denim Levis Vintage', 'Jaket', 320000, 'M', 'Bekas Sangat Baik', 'Jaket jeans vintage warna biru pudar, kancing lengkap, tidak ada noda.', ''),
('Celana Cargo Army Hijau', 'Celana', 120000, '32', 'Bekas Baik', 'Celana cargo hijau army dengan banyak kantong, jahitan masih kuat.', ''),
('Topi Baseball Nike Putih', 'Aksesori', 65000, 'All Size', 'Bekas Sangat Baik', 'Topi baseball putih logo Nike, ukuran bisa diatur di bagian belakang.', ''),
('Hoodie Champion Abu-abu', 'Jaket', 175000, 'XL', 'Bekas Baik', 'Hoodie champion warna abu-abu, bahan fleece tebal, sedikit pudar di lengan.', '');
