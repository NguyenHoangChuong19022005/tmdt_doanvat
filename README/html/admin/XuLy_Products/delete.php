<?php
$conn = new mysqli("localhost", "root", "", "showroom_gach");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Lấy ID sản phẩm cần xóa
$sanpham_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin hình ảnh để kiểm tra và xóa file (nếu là ảnh đã upload)
$sql = "SELECT image FROM sanpham WHERE sanpham_id = $sanpham_id";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image_url = $row['image'];
    
    // Nếu ảnh lưu trong thư mục uploads thì xóa (tránh xóa URL ngoài)
    if (!empty($image_url) && !filter_var($image_url, FILTER_VALIDATE_URL)) {
        $image_path = "../uploads/" . basename($image_url);
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    // Thực hiện xóa sản phẩm khỏi bảng `sanpham`
    $sql_delete = "DELETE FROM sanpham WHERE sanpham_id = $sanpham_id";
    if ($conn->query($sql_delete) === TRUE) {
        header("Location: ../products.php");
        exit();
    } else {
        echo "Lỗi khi xóa sản phẩm: " . $conn->error;
    }
} else {
    echo "Không tìm thấy sản phẩm!";
}

$conn->close();
?>