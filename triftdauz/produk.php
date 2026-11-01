<?php
// PENGECEKAN LOGIN
// Digunakan untuk memastikan hanya admin yang sudah login yang bisa membuka halaman produk.
require "includes/auth.php";
require "config/koneksi.php";

// Ambil koneksi dari scope global jika file konfigurasi mendeklarasikannya di sana.
if (!isset($koneksi) && isset($GLOBALS["koneksi"])) {
  $koneksi = $GLOBALS["koneksi"];
}

// PROSES PAGINATION
// Digunakan untuk menghitung jumlah halaman, memvalidasi nomor halaman, dan menentukan OFFSET data.
$data_per_halaman = 5;

$query_hitung   = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$data_hitung    = mysqli_fetch_assoc($query_hitung);
$jumlah_data    = $data_hitung["total"];
$jumlah_halaman = ceil($jumlah_data / $data_per_halaman);

if ($jumlah_halaman < 1) {
    $jumlah_halaman = 1;
}

if (isset($_GET["halaman"])) {
    $halaman_aktif = (int) $_GET["halaman"];
} else {
    $halaman_aktif = 1;
}

if ($halaman_aktif < 1) {
    $halaman_aktif = 1;
}
if ($halaman_aktif > $jumlah_halaman) {
    $halaman_aktif = $jumlah_halaman;
}

$data_awal = ($halaman_aktif - 1) * $data_per_halaman;

// PENGAMBILAN DATA PRODUK
// Digunakan untuk mengambil data produk sesuai halaman aktif memakai LIMIT dan OFFSET.
$query_produk = mysqli_prepare($koneksi, "SELECT * FROM produk ORDER BY id DESC LIMIT ? OFFSET ?");
mysqli_stmt_bind_param($query_produk, "ii", $data_per_halaman, $data_awal);
mysqli_stmt_execute($query_produk);
$hasil_produk = mysqli_stmt_get_result($query_produk);
$daftar_produk = mysqli_fetch_all($hasil_produk, MYSQLI_ASSOC);
mysqli_stmt_close($query_produk);

// Nomor urut tabel lanjut antar halaman (halaman 2 mulai dari 6)
$nomor = $data_awal + 1;

$judul_halaman = "Data Produk";
require "includes/header.php";
?>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">Daftar Produk Thrift</h3>
    <div class="card-tools">
      <a href="tambah_produk.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Tambah Produk</a>
    </div>
  </div>

  <div class="card-body table-responsive p-0">
    <table class="table table-bordered table-hover text-nowrap">
      <thead>
        <tr>
          <th>No</th>
          <th>Foto</th>
          <th>Nama Produk</th>
          <th>Kategori</th>
          <th>Harga</th>
          <th>Ukuran</th>
          <th>Kondisi</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($daftar_produk) == 0): ?>
        <tr>
          <td colspan="8" class="text-center">Belum ada data produk.</td>
        </tr>
        <?php endif; ?>

        <?php foreach ($daftar_produk as $produk): ?>
        <tr>
          <td><?= $nomor ?></td>
          <td>
            <?php if ($produk["gambar"] != ""): ?>
              <img src="uploads/<?= htmlspecialchars($produk["gambar"]) ?>" width="100" height="100" alt="<?= htmlspecialchars($produk["nama_produk"]) ?>">
            <?php else: ?>
              Tidak ada foto
            <?php endif; ?>
          </td>
          <td><?= htmlspecialchars($produk["nama_produk"]) ?></td>
          <td><?= htmlspecialchars($produk["kategori"]) ?></td>
          <td>Rp <?= number_format($produk["harga"], 0, ",", ".") ?></td>
          <td><?= htmlspecialchars($produk["ukuran"]) ?></td>
          <td><?= htmlspecialchars($produk["kondisi"]) ?></td>
          <td>
            <a href="edit_produk.php?id=<?= $produk["id"] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Edit</a>
            <a href="hapus_produk.php?id=<?= $produk["id"] ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Hapus</a>
          </td>
        </tr>
        <?php $nomor++; ?>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <div class="card-footer clearfix">
    <ul class="pagination pagination-sm m-0 float-right">
      <!-- Tombol Previous -->
      <?php if ($halaman_aktif > 1): ?>
        <li class="page-item"><a class="page-link" href="produk.php?halaman=<?= $halaman_aktif - 1 ?>">Previous</a></li>
      <?php else: ?>
        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
      <?php endif; ?>

      <!-- Nomor halaman -->
      <?php for ($i = 1; $i <= $jumlah_halaman; $i++): ?>
        <?php if ($i == $halaman_aktif): ?>
          <li class="page-item active"><a class="page-link" href="produk.php?halaman=<?= $i ?>"><?= $i ?></a></li>
        <?php else: ?>
          <li class="page-item"><a class="page-link" href="produk.php?halaman=<?= $i ?>"><?= $i ?></a></li>
        <?php endif; ?>
      <?php endfor; ?>

      <!-- Tombol Next -->
      <?php if ($halaman_aktif < $jumlah_halaman): ?>
        <li class="page-item"><a class="page-link" href="produk.php?halaman=<?= $halaman_aktif + 1 ?>">Next</a></li>
      <?php else: ?>
        <li class="page-item disabled"><a class="page-link" href="#">Next</a></li>
      <?php endif; ?>
    </ul>
  </div>
</div>

<?php require "includes/footer.php"; ?>