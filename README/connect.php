<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "showroom_gach"; // showroom_gach là tên database đã import file showroom_gach.sql

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}
?>
