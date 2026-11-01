<?php
// PENGECEKAN LOGIN
// Digunakan untuk memastikan hanya admin yang sudah login yang bisa membuka dashboard.
require "includes/auth.php";
require "config/koneksi.php";

/** @var mysqli $koneksi */
// PENGAMBILAN DATA TOTAL PRODUK
// Digunakan untuk menghitung jumlah seluruh produk di database.
$query_total = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$data_total  = mysqli_fetch_assoc($query_total);
$total_produk = $data_total["total"];

// PENGAMBILAN DATA PRODUK TERBARU
// Digunakan untuk mengambil nama produk yang terakhir ditambahkan.
$query_terbaru = mysqli_query($koneksi, "SELECT nama_produk FROM produk ORDER BY id DESC LIMIT 1");
$data_terbaru  = mysqli_fetch_assoc($query_terbaru);

if ($data_terbaru) {
    $produk_terbaru = $data_terbaru["nama_produk"];
} else {
    $produk_terbaru = "Belum ada produk";
}

$judul_halaman = "Dashboard";
require "includes/header.php";
?>

<div class="row">
  <!-- Total produk -->
  <div class="col-lg-4 col-12">
    <div class="small-box bg-info">
      <div class="inner">
        <h3><?= $total_produk ?></h3>
        <p>Total Produk</p>
      </div>
      <div class="icon">
        <i class="fas fa-tshirt"></i>
      </div>
      <a href="produk.php" class="small-box-footer">Lihat data produk <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <!-- Produk terbaru -->
  <div class="col-lg-4 col-12">
    <div class="small-box bg-success">
      <div class="inner">
        <h4><?= htmlspecialchars($produk_terbaru) ?></h4>
        <p>Produk Terbaru</p>
      </div>
      <div class="icon">
        <i class="fas fa-star"></i>
      </div>
      <a href="tambah_produk.php" class="small-box-footer">Tambah produk <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>

  <!-- Admin yang login -->
  <div class="col-lg-4 col-12">
    <div class="small-box bg-warning">
      <div class="inner">
        <h4><?= htmlspecialchars($_SESSION["nama_admin"]) ?></h4>
        <p>Admin yang Login</p>
      </div>
      <div class="icon">
        <i class="fas fa-user"></i>
      </div>
      <a href="logout.php" class="small-box-footer">Logout <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
</div>

<?php require "includes/footer.php"; ?>