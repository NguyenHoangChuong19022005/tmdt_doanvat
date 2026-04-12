<?php
$conn = new mysqli("localhost", "root", "", "showroom_gach"); // kết nối đến cơ sở dữ liệu
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM orders WHERE id = $id";
    if ($conn->query($sql) === TRUE) {
        header("Location: ../orders.php");
    } else {
        echo "Lỗi xóa đơn hàng: " . $conn->error;
    }
} else {
    echo "Thiếu ID đơn hàng!";
}
?>
