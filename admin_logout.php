<?php
// 1. Mula mengesan session yang aktif
session_start();

// 2. Kosongkan semua pembolehubah di dalam session admin
$_SESSION = array();

// 3. Musnahkan session tersebut sepenuhnya dari server
session_destroy();

// 4. Hantar admin balik ke halaman login admin yang berwarna biru tadi
header("Location: admin_login.php"); 
exit();
?>