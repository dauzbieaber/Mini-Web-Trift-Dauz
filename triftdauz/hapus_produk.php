<?php
// PENGECEKAN LOGIN
// Digunakan untuk memastikan hanya admin yang sudah login yang bisa menghapus produk.
require "includes/auth.php";
require "config/koneksi.php";
global $koneksi;

// PENGAMBILAN ID PRODUK
// Digunakan untuk mengambil id produk. Saat konfirmasi dikirim (POST), id diambil dari $_POST["id"],
// saat halaman pertama dibuka, id diambil dari $_GET["id"].
// Jika id tidak ada, alihkan ke produk.php.
if (isset($_POST["hapus"]) && isset($_POST["id"])) {
    $id = (int) $_POST["id"];
} elseif (isset($_GET["id"])) {
    $id = (int) $_GET["id"];
} else {
    header("Location: produk.php");
    exit;
}

// PENGAMBILAN DATA PRODUK
// Digunakan untuk mengambil data produk yang akan dihapus. Jika data tidak ditemukan, alihkan ke produk.php.
$query_ambil = mysqli_prepare($koneksi, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($query_ambil, "i", $id);
mysqli_stmt_execute($query_ambil);
$hasil_ambil = mysqli_stmt_get_result($query_ambil);
$produk      = mysqli_fetch_assoc($hasil_ambil);
mysqli_stmt_close($query_ambil);

if (!$produk) {
    header("Location: produk.php");
    exit;
}

// PROSES HAPUS PRODUK
// Digunakan untuk menghapus data produk dari database setelah form konfirmasi dikirim lewat POST,
// lalu menghapus file foto dengan unlink hanya jika kolom gambar tidak kosong dan file ada.
if (isset($_POST["hapus"])) {
  global $koneksi;
    $query_hapus = mysqli_prepare($koneksi, "DELETE FROM produk WHERE id = ?");
    mysqli_stmt_bind_param($query_hapus, "i", $id);

    if (mysqli_stmt_execute($query_hapus)) {
        mysqli_stmt_close($query_hapus);

        if ($produk["gambar"] != "" && file_exists("uploads/" . $produk["gambar"])) {
            unlink("uploads/" . $produk["gambar"]);
        }

        $_SESSION["pesan"]       = "Produk berhasil dihapus.";
        $_SESSION["jenis_pesan"] = "success";
    } else {
        mysqli_stmt_close($query_hapus);
        $_SESSION["pesan"]       = "Produk gagal dihapus.";
        $_SESSION["jenis_pesan"] = "danger";
    }

    header("Location: produk.php");
    exit;
}

$judul_halaman = "Hapus Produk";
require "includes/header.php";
?>

<div class="card card-danger">
  <div class="card-header">
    <h3 class="card-title">Konfirmasi Hapus Produk</h3>
  </div>

  <form action="hapus_produk.php" method="post">
    <div class="card-body">
      <p>Apakah kamu yakin ingin menghapus produk berikut?</p>

      <table class="table table-bordered">
        <tr>
          <th style="width: 150px;">Foto</th>
          <td>
            <?php if ($produk["gambar"] != ""): ?>
              <img src="uploads/<?= htmlspecialchars($produk["gambar"]) ?>" width="100" height="100" alt="<?= htmlspecialchars($produk["nama_produk"]) ?>">
            <?php else: ?>
              Tidak ada foto
            <?php endif; ?>
          </td>
        </tr>
        <tr>
          <th>Nama Produk</th>
          <td><?= htmlspecialchars($produk["nama_produk"]) ?></td>
        </tr>
        <tr>
          <th>Kategori</th>
          <td><?= htmlspecialchars($produk["kategori"]) ?></td>
        </tr>
        <tr>
          <th>Harga</th>
          <td>Rp <?= number_format($produk["harga"], 0, ",", ".") ?></td>
        </tr>
        <tr>
          <th>Ukuran</th>
          <td><?= htmlspecialchars($produk["ukuran"]) ?></td>
        </tr>
        <tr>
          <th>Kondisi</th>
          <td><?= htmlspecialchars($produk["kondisi"]) ?></td>
        </tr>
      </table>

      <input type="hidden" name="id" value="<?= $produk["id"] ?>">
    </div>

    <div class="card-footer">
      <button type="submit" name="hapus" class="btn btn-danger">Ya, Hapus</button>
      <a href="produk.php" class="btn btn-default">Batal</a>
    </div>
  </form>
</div>

<?php require "includes/footer.php"; ?>