<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "doan_vat"; // doan_vat là tên database đã import file doan_vat.sql

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
