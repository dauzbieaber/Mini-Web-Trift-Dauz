<?php
// SESSION
// Digunakan untuk memulai session. Jika admin sudah login, langsung dialihkan ke dashboard.php.
session_start();

if (isset($_SESSION["login"])) {
    header("Location: dashboard.php");
    exit;
}

require "config/koneksi.php";

$nama           = "";
$email          = "";
$daftar_error   = array();

// PROSES REGISTER ADMIN
// Digunakan untuk memvalidasi form, mengecek email kembar, lalu menyimpan admin baru ke database.
if (isset($_POST["register"])) {
    $nama                 = trim($_POST["nama"]);
    $email                = trim($_POST["email"]);
    $password             = $_POST["password"];
    $konfirmasi_password  = $_POST["konfirmasi_password"];

    // Validasi tidak boleh kosong
    if ($nama == "" || $email == "" || $password == "" || $konfirmasi_password == "") {
        $daftar_error[] = "Semua kolom wajib diisi.";
    }

    // Validasi password dan konfirmasi harus sama
    if ($password != "" && $password != $konfirmasi_password) {
        $daftar_error[] = "Password dan konfirmasi password tidak sama.";
    }

    // Validasi email tidak boleh kembar (cek ke database)
    if ($email != "") {
        $query_cek = mysqli_prepare($GLOBALS["koneksi"], "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($query_cek, "s", $email);
        mysqli_stmt_execute($query_cek);
        mysqli_stmt_store_result($query_cek);

        if (mysqli_stmt_num_rows($query_cek) > 0) {
            $daftar_error[] = "Email sudah terdaftar, gunakan email lain.";
        }
        mysqli_stmt_close($query_cek);
    }

    // Jika tidak ada error, simpan ke database
    if (count($daftar_error) == 0) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $query_simpan = mysqli_prepare($GLOBALS["koneksi"], "INSERT INTO users (nama, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($query_simpan, "sss", $nama, $email, $password_hash);

        if (mysqli_stmt_execute($query_simpan)) {
            mysqli_stmt_close($query_simpan);
            $_SESSION["pesan"]       = "Register berhasil, silakan login.";
            $_SESSION["jenis_pesan"] = "success";
            header("Location: login.php");
            exit;
        } else {
            $daftar_error[] = "Register gagal, silakan coba lagi.";
        }
        mysqli_stmt_close($query_simpan);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TriftDauz | Register</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
</head>
<body class="hold-transition register-page">
<div class="register-box">
  <div class="register-logo">
    <a href="register.php"><b>Trift</b>Dauz</a>
  </div>

  <div class="card">
    <div class="card-body register-card-body">
      <p class="login-box-msg">Daftar akun admin baru</p>

      <?php if (count($daftar_error) > 0): ?>
      <div class="alert alert-danger">
        <?php foreach ($daftar_error as $error): ?>
          <?= htmlspecialchars($error) ?><br>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form action="register.php" method="post">
        <div class="input-group mb-3">
          <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" value="<?= htmlspecialchars($nama) ?>">
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-user"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="email" name="email" class="form-control" placeholder="Email" value="<?= htmlspecialchars($email) ?>">
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-envelope"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="password" class="form-control" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" name="konfirmasi_password" class="form-control" placeholder="Konfirmasi password">
          <div class="input-group-append">
            <div class="input-group-text"><span class="fas fa-lock"></span></div>
          </div>
        </div>
        <div class="row">
          <div class="col-8"></div>
          <div class="col-4">
            <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
          </div>
        </div>
      </form>

      <a href="login.php" class="text-center">Saya sudah punya akun</a>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>