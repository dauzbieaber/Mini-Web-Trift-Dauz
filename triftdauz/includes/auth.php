<?php
// PENGECEKAN SESSION LOGIN
// Digunakan untuk memulai session dan memastikan hanya admin yang sudah login yang bisa membuka halaman.
// Jika belum login, pengguna dialihkan ke login.php.
session_start();

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit;
}