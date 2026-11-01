<?php
// PENGAMBILAN PESAN SESSION
// Digunakan untuk mengambil pesan sukses/gagal dari $_SESSION["pesan"], lalu menghapusnya setelah ditampilkan.
$pesan       = "";
$jenis_pesan = "success";

if (isset($_SESSION["pesan"])) {
    $pesan = $_SESSION["pesan"];
    if (isset($_SESSION["jenis_pesan"])) {
        $jenis_pesan = $_SESSION["jenis_pesan"];
    }
    unset($_SESSION["pesan"]);
    unset($_SESSION["jenis_pesan"]);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TriftDauz | <?= isset($judul_halaman) ? htmlspecialchars($judul_halaman) : "Admin" ?></title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
      <li class="nav-item d-none d-sm-inline-block">
        <a href="dashboard.php" class="nav-link">Home</a>
      </li>
    </ul>
    <ul class="navbar-nav ml-auto">
      <li class="nav-item">
        <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </li>
    </ul>
  </nav>

  <?php require "includes/sidebar.php"; ?>

  <!-- Content Wrapper -->
  <div class="content-wrapper">
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?= isset($judul_halaman) ? htmlspecialchars($judul_halaman) : "Admin" ?></h1>
          </div>
        </div>
      </div>
    </div>

    <section class="content">
      <div class="container-fluid">

        <?php if ($pesan != ""): ?>
        <div class="alert alert-<?= $jenis_pesan ?> alert-dismissible">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          <h5>
            <i class="icon fas <?= $jenis_pesan == "success" ? "fa-check" : "fa-ban" ?>"></i>
            <?= $jenis_pesan == "success" ? "Berhasil!" : "Gagal!" ?>
          </h5>
          <?= htmlspecialchars($pesan) ?>
        </div>
        <?php endif; ?>