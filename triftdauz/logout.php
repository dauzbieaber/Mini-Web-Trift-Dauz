<?php
// PROSES LOGOUT
// Digunakan untuk menghapus semua data session admin lalu mengalihkan ke halaman login.
session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit;