<?php
// KONEKSI DATABASE
// Digunakan untuk menghubungkan PHP ke database MySQL triftdauz_db.
$host     = "localhost";
$user     = "root";
$password = "";
$nama_db  = "triftdauz_db";

$koneksi = mysqli_connect($host, $user, $password, $nama_db);

if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8mb4");
