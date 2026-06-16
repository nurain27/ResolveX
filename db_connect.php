<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "resolvex_db";

// 1. Sambungan DUA DALAM SATU (Untuk fail dashboard.php)
$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn)
{
    die("Connection Failed: " . mysqli_connect_error());
}

// 2. Sambungan PDO (Untuk fail complaints.php)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    // Set error mode ke exception supaya senang debug kalau ada salah SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("PDO Connection Failed: " . $e->getMessage());
}
?>