<?php
// 1. Mula mengesan session yang aktif
session_start();

// 2. Kosongkan semua pembolehubah di dalam session USER
$_SESSION = array();

// 3. Musnahkan session tersebut sepenuhnya dari server
session_destroy();

// 4. Hantar USER balik ke halaman login BIASA (User Login)
header("Location: index.php"); // <--- BEZA DI SINI! Hantar ke login biasa
exit();
?>