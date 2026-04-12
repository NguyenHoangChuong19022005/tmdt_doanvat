<?php
$conn = new mysqli("localhost", "root", "", "showroom_gach");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string($_POST['category_name']);

    // Thêm vào bảng loai_sanpham (loai_id tự tăng)
    $sql = "INSERT INTO loai_sanpham (loai_name) VALUES ('$name')";
    if ($conn->query($sql) === TRUE) {
        header("Location: ../products_type.php");
        exit();
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

$conn->close();
?>