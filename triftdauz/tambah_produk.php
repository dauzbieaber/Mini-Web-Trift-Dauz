<?php
// PENGECEKAN LOGIN
// Digunakan untuk memastikan hanya admin yang sudah login yang bisa menambah produk.
require "includes/auth.php";
require "config/koneksi.php";

// FUNGSI UPLOAD GAMBAR
// Digunakan untuk memvalidasi file foto (ekstensi, MIME type, ukuran maksimal 2 MB),
// mengganti nama file dengan uniqid(), lalu memindahkannya ke folder uploads/.
function upload_gambar($file) {
    $hasil = array("nama_file" => "", "error" => "");

    if ($file["error"] != 0) {
        $hasil["error"] = "Upload foto gagal, silakan coba lagi.";
        return $hasil;
    }

    $ekstensi_boleh = array("jpg", "jpeg", "png");
    $mime_boleh     = array("image/jpeg", "image/png");

    $nama_asli = $file["name"];
    $ekstensi  = strtolower(pathinfo($nama_asli, PATHINFO_EXTENSION));
    $mime_file = mime_content_type($file["tmp_name"]);

    if (!in_array($ekstensi, $ekstensi_boleh) || !in_array($mime_file, $mime_boleh)) {
        $hasil["error"] = "Foto harus berformat jpg, jpeg, atau png.";
        return $hasil;
    }

    if ($file["size"] > 2 * 1024 * 1024) {
        $hasil["error"] = "Ukuran foto maksimal 2 MB.";
        return $hasil;
    }

    $nama_baru = uniqid() . "." . $ekstensi;

    if (move_uploaded_file($file["tmp_name"], "uploads/" . $nama_baru)) {
        $hasil["nama_file"] = $nama_baru;
    } else {
        $hasil["error"] = "Foto gagal dipindahkan ke folder uploads. Pastikan folder uploads bisa ditulis.";
    }

    return $hasil;
}

// DATA PILIHAN KATEGORI
// Digunakan untuk menyimpan pilihan kategori tetap yang tampil di form.
$daftar_kategori = array("Sepatu", "Baju", "Jaket", "Celana", "Aksesori");

$nama_produk  = "";
$kategori     = "";
$harga        = "";
$ukuran       = "";
$kondisi      = "";
$deskripsi    = "";
$daftar_error = array();

// PROSES TAMBAH PRODUK
// Digunakan untuk memvalidasi form, mengunggah foto, lalu menyimpan data produk baru ke database.
if (isset($_POST["simpan"])) {
    $nama_produk = trim($_POST["nama_produk"]);
    $kategori    = $_POST["kategori"];
    $harga       = trim($_POST["harga"]);
    $ukuran      = trim($_POST["ukuran"]);
    $kondisi     = trim($_POST["kondisi"]);
    $deskripsi   = trim($_POST["deskripsi"]);

    // Validasi tidak boleh kosong
    if ($nama_produk == "" || $kategori == "" || $harga == "" || $ukuran == "" || $kondisi == "" || $deskripsi == "") {
        $daftar_error[] = "Semua kolom wajib diisi.";
    }

    // Validasi kategori harus sesuai pilihan
    if ($kategori != "" && !in_array($kategori, $daftar_kategori)) {
        $daftar_error[] = "Kategori tidak valid.";
    }

    // Validasi harga harus angka lebih dari 0
    if ($harga != "" && (!is_numeric($harga) || $harga <= 0)) {
        $daftar_error[] = "Harga harus berupa angka lebih dari 0.";
    }

    // Proses upload foto (foto boleh tidak diisi)
    $nama_gambar = "";
    if (count($daftar_error) == 0 && isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] != 4) {
        $hasil_upload = upload_gambar($_FILES["gambar"]);

        if ($hasil_upload["error"] != "") {
            $daftar_error[] = $hasil_upload["error"];
        } else {
            $nama_gambar = $hasil_upload["nama_file"];
        }
    }

    // Jika tidak ada error, simpan ke database
    if (count($daftar_error) == 0) {
        $harga_angka = (int) $harga;

        $query_simpan = mysqli_prepare($GLOBALS["koneksi"], "INSERT INTO produk (nama_produk, kategori, harga, ukuran, kondisi, deskripsi, gambar) VALUES (?, ?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($query_simpan, "ssissss", $nama_produk, $kategori, $harga_angka, $ukuran, $kondisi, $deskripsi, $nama_gambar);

        if (mysqli_stmt_execute($query_simpan)) {
            mysqli_stmt_close($query_simpan);
            $_SESSION["pesan"]       = "Produk berhasil ditambahkan.";
            $_SESSION["jenis_pesan"] = "success";
            header("Location: produk.php");
            exit;
        } else {
            $daftar_error[] = "Produk gagal disimpan ke database.";
            // Hapus foto yang sudah terlanjur diunggah
            if ($nama_gambar != "" && file_exists("uploads/" . $nama_gambar)) {
                unlink("uploads/" . $nama_gambar);
            }
        }
        mysqli_stmt_close($query_simpan);
    }
}

$judul_halaman = "Tambah Produk";
require "includes/header.php";
?>

<div class="card card-primary">
  <div class="card-header">
    <h3 class="card-title">Form Tambah Produk</h3>
  </div>

  <form action="tambah_produk.php" method="post" enctype="multipart/form-data">
    <div class="card-body">

      <?php if (count($daftar_error) > 0): ?>
      <div class="alert alert-danger">
        <?php foreach ($daftar_error as $error): ?>
          <?= htmlspecialchars($error) ?><br>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <div class="form-group">
        <label for="nama_produk">Nama Produk</label>
        <input type="text" class="form-control" id="nama_produk" name="nama_produk" placeholder="Masukkan nama produk" value="<?= htmlspecialchars($nama_produk) ?>">
      </div>

      <div class="form-group">
        <label for="kategori">Kategori</label>
        <select class="form-control" id="kategori" name="kategori">
          <option value="">-- Pilih Kategori --</option>
          <?php foreach ($daftar_kategori as $pilihan): ?>
            <option value="<?= $pilihan ?>" <?= $kategori == $pilihan ? "selected" : "" ?>><?= $pilihan ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="harga">Harga (Rp)</label>
        <input type="number" class="form-control" id="harga" name="harga" placeholder="Contoh: 150000" value="<?= htmlspecialchars($harga) ?>">
      </div>

      <div class="form-group">
        <label for="ukuran">Ukuran</label>
        <input type="text" class="form-control" id="ukuran" name="ukuran" placeholder="Contoh: M, L, 42" value="<?= htmlspecialchars($ukuran) ?>">
      </div>

      <div class="form-group">
        <label for="kondisi">Kondisi</label>
        <input type="text" class="form-control" id="kondisi" name="kondisi" placeholder="Contoh: Bekas Baik" value="<?= htmlspecialchars($kondisi) ?>">
      </div>

      <div class="form-group">
        <label for="deskripsi">Deskripsi</label>
        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Masukkan deskripsi produk"><?= htmlspecialchars($deskripsi) ?></textarea>
      </div>

      <div class="form-group">
        <label for="gambar">Foto Produk (jpg, jpeg, png, maks. 2 MB)</label>
        <input type="file" class="form-control-file" id="gambar" name="gambar">
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" name="simpan" class="btn btn-primary">Simpan</button>
      <a href="produk.php" class="btn btn-default">Batal</a>
    </div>
  </form>
</div>

<?php require "includes/footer.php"; ?>