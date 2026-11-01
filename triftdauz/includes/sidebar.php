<?php
// MENU SIDEBAR
// Digunakan untuk menampilkan menu navigasi admin dan menandai menu yang sedang aktif.
$nama_file = basename($_SERVER["PHP_SELF"]);
$halaman_produk = array("produk.php", "edit_produk.php", "hapus_produk.php");
?>
<!-- Main Sidebar -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
  <a href="dashboard.php" class="brand-link">
    <span class="brand-text font-weight-light">TriftDauz</span>
  </a>

  <div class="sidebar">
    <div class="user-panel mt-3 pb-3 mb-3 d-flex">
      <div class="info">
        <a href="dashboard.php" class="d-block"><?= htmlspecialchars($_SESSION["nama_admin"]) ?></a>
      </div>
    </div>

    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="dashboard.php" class="nav-link <?= $nama_file == "dashboard.php" ? "active" : "" ?>">
            <i class="nav-icon fas fa-tachometer-alt"></i>
            <p>Dashboard</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="produk.php" class="nav-link <?= in_array($nama_file, $halaman_produk) ? "active" : "" ?>">
            <i class="nav-icon fas fa-tshirt"></i>
            <p>Data Produk</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="tambah_produk.php" class="nav-link <?= $nama_file == "tambah_produk.php" ? "active" : "" ?>">
            <i class="nav-icon fas fa-plus"></i>
            <p>Tambah Produk</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="logout.php" class="nav-link">
            <i class="nav-icon fas fa-sign-out-alt"></i>
            <p>Logout</p>
          </a>
        </li>
      </ul>
    </nav>
  </div>
</aside>