<?php
// SESSION
// Digunakan untuk memulai session. Jika admin sudah login, langsung dialihkan ke dashboard.php.
session_start();

if (isset($_SESSION["login"])) {
    header("Location: dashboard.php");
    exit;
}

require "config/koneksi.php";

// Ambil koneksi dari konfigurasi, termasuk jika variabelnya bernama $conn.
$koneksi = $GLOBALS["koneksi"] ?? $GLOBALS["conn"] ?? null;

$email        = "";
$error_login  = "";

// PENGAMBILAN PESAN SESSION
// Digunakan untuk menampilkan pesan dari $_SESSION["pesan"] (misalnya setelah register), lalu menghapusnya.
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

// PROSES LOGIN
// Digunakan untuk mengecek email di database, memverifikasi password, lalu mengisi session login.
if (isset($_POST["login"])) {
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    if ($email == "" || $password == "") {
        $error_login = "Email dan password wajib diisi.";
    } elseif (!($koneksi instanceof mysqli)) {
      $error_login = "Koneksi database tidak tersedia.";
    } else {
        $query_login = mysqli_prepare($koneksi, "SELECT id, nama, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($query_login, "s", $email);
        mysqli_stmt_execute($query_login);
        $hasil_login = mysqli_stmt_get_result($query_login);
        $data_admin  = mysqli_fetch_assoc($hasil_login);
        mysqli_stmt_close($query_login);

        if ($data_admin && password_verify($password, $data_admin["password"])) {
            session_regenerate_id(true);
            $_SESSION["login"]      = true;
            $_SESSION["nama_admin"] = $data_admin["nama"];
            header("Location: dashboard.php");
            exit;
        } else {
            $error_login = "Email atau password salah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TriftDauz | Login</title>

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/plugins/fontawesome-free/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/css/adminlte.min.css">
</head>
<body class="hold-transition login-page">
<div class="login-box">
  <div class="login-logo">
    <a href="login.php"><b>Trift</b>Dauz</a>
  </div>

  <div class="card">
    <div class="card-body login-card-body">
      <p class="login-box-msg">Login untuk masuk ke halaman admin</p>

      <?php if ($pesan != ""): ?>
      <div class="alert alert-<?= $jenis_pesan ?>">
        <?= htmlspecialchars($pesan) ?>
      </div>
      <?php endif; ?>

      <?php if ($error_login != ""): ?>
      <div class="alert alert-danger">
        <?= htmlspecialchars($error_login) ?>
      </div>
      <?php endif; ?>

      <form action="login.php" method="post">
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
        <div class="row">
          <div class="col-8"></div>
          <div class="col-4">
            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>
          </div>
        </div>
      </form>

      <p class="mb-0 mt-3">
        <a href="register.php" class="text-center">Daftar akun admin baru</a>
      </p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2.0/dist/js/adminlte.min.js"></script>
</body>
</html>