<?php
// PENGECEKAN LOGIN
// Digunakan untuk memastikan hanya admin yang sudah login yang bisa mengedit produk.
require "includes/auth.php";
require "config/koneksi.php";
$koneksi = $GLOBALS["koneksi"] ?? null;

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

// PENGAMBILAN DATA PRODUK LAMA
// Digunakan untuk mengambil data produk berdasarkan id dari $_GET["id"].
// Jika id tidak ada atau data tidak ditemukan, alihkan ke produk.php.
if (!isset($_GET["id"])) {
    header("Location: produk.php");
    exit;
}

$id = (int) $_GET["id"];

$query_ambil = mysqli_prepare($koneksi, "SELECT * FROM produk WHERE id = ?");
mysqli_stmt_bind_param($query_ambil, "i", $id);
mysqli_stmt_execute($query_ambil);
$hasil_ambil = mysqli_stmt_get_result($query_ambil);
$produk_lama = mysqli_fetch_assoc($hasil_ambil);
mysqli_stmt_close($query_ambil);

if (!$produk_lama) {
    header("Location: produk.php");
    exit;
}

// DATA PILIHAN KATEGORI
// Digunakan untuk menyimpan pilihan kategori tetap yang tampil di form.
$daftar_kategori = array("Sepatu", "Baju", "Jaket", "Celana", "Aksesori");

// Form terisi data lama
$nama_produk  = $produk_lama["nama_produk"];
$kategori     = $produk_lama["kategori"];
$harga        = $produk_lama["harga"];
$ukuran       = $produk_lama["ukuran"];
$kondisi      = $produk_lama["kondisi"];
$deskripsi    = $produk_lama["deskripsi"];
$gambar_lama  = $produk_lama["gambar"];
$daftar_error = array();

// PROSES EDIT PRODUK
// Digunakan untuk memvalidasi form, mengganti foto jika ada foto baru, lalu memperbarui data produk di database.
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

    // Proses upload foto baru (foto boleh tidak diganti)
    $nama_gambar_baru = "";
    if (count($daftar_error) == 0 && isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] != 4) {
        $hasil_upload = upload_gambar($_FILES["gambar"]);

        if ($hasil_upload["error"] != "") {
            $daftar_error[] = $hasil_upload["error"];
        } else {
            $nama_gambar_baru = $hasil_upload["nama_file"];
        }
    }

    // Jika tidak ada error, perbarui data di database
    if (count($daftar_error) == 0) {
        $harga_angka = (int) $harga;

        // Jika foto tidak diganti, pakai nama foto lama
        if ($nama_gambar_baru != "") {
            $nama_gambar_simpan = $nama_gambar_baru;
        } else {
            $nama_gambar_simpan = $gambar_lama;
        }

        $query_ubah = mysqli_prepare($koneksi, "UPDATE produk SET nama_produk = ?, kategori = ?, harga = ?, ukuran = ?, kondisi = ?, deskripsi = ?, gambar = ? WHERE id = ?");
        mysqli_stmt_bind_param($query_ubah, "ssissssi", $nama_produk, $kategori, $harga_angka, $ukuran, $kondisi, $deskripsi, $nama_gambar_simpan, $id);

        if (mysqli_stmt_execute($query_ubah)) {
            mysqli_stmt_close($query_ubah);

            // Hapus foto lama hanya jika foto diganti, kolom gambar lama tidak kosong, dan file ada
            if ($nama_gambar_baru != "" && $gambar_lama != "" && file_exists("uploads/" . $gambar_lama)) {
                unlink("uploads/" . $gambar_lama);
            }

            $_SESSION["pesan"]       = "Produk berhasil diubah.";
            $_SESSION["jenis_pesan"] = "success";
            header("Location: produk.php");
            exit;
        } else {
            $daftar_error[] = "Produk gagal diubah di database.";
            // Hapus foto baru yang sudah terlanjur diunggah
            if ($nama_gambar_baru != "" && file_exists("uploads/" . $nama_gambar_baru)) {
                unlink("uploads/" . $nama_gambar_baru);
            }
        }
        mysqli_stmt_close($query_ubah);
    }
}

$judul_halaman = "Edit Produk";
require "includes/header.php";
?>

<div class="card card-warning">
  <div class="card-header">
    <h3 class="card-title">Form Edit Produk</h3>
  </div>

  <form action="edit_produk.php?id=<?= $id ?>" method="post" enctype="multipart/form-data">
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
        <input type="text" class="form-control" id="nama_produk" name="nama_produk" value="<?= htmlspecialchars($nama_produk) ?>">
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
        <input type="number" class="form-control" id="harga" name="harga" value="<?= htmlspecialchars($harga) ?>">
      </div>

      <div class="form-group">
        <label for="ukuran">Ukuran</label>
        <input type="text" class="form-control" id="ukuran" name="ukuran" value="<?= htmlspecialchars($ukuran) ?>">
      </div>

      <div class="form-group">
        <label for="kondisi">Kondisi</label>
        <input type="text" class="form-control" id="kondisi" name="kondisi" value="<?= htmlspecialchars($kondisi) ?>">
      </div>

      <div class="form-group">
        <label for="deskripsi">Deskripsi</label>
        <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"><?= htmlspecialchars($deskripsi) ?></textarea>
      </div>

      <div class="form-group">
        <label>Foto Saat Ini</label><br>
        <?php if ($gambar_lama != ""): ?>
          <img src="uploads/<?= htmlspecialchars($gambar_lama) ?>" width="100" height="100" alt="<?= htmlspecialchars($produk_lama["nama_produk"]) ?>">
        <?php else: ?>
          Tidak ada foto
        <?php endif; ?>
      </div>

      <div class="form-group">
        <label for="gambar">Ganti Foto (kosongkan jika tidak diganti; jpg, jpeg, png, maks. 2 MB)</label>
        <input type="file" class="form-control-file" id="gambar" name="gambar">
      </div>
    </div>

    <div class="card-footer">
      <button type="submit" name="simpan" class="btn btn-warning">Simpan Perubahan</button>
      <a href="produk.php" class="btn btn-default">Batal</a>
    </div>
  </form>
</div>

<?php require "includes/footer.php"; ?>