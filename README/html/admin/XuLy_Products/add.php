<?php
$conn = new mysqli("localhost", "root", "", "showroom_gach");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $ten_sanpham = $conn->real_escape_string($_POST['ten_sanpham']);
    $ma_sp = $conn->real_escape_string($_POST['ma_sp']);
    $bemat = $conn->real_escape_string($_POST['bemat']);
    $chatlieu = $conn->real_escape_string($_POST['chatlieu']);
    $congnang = $conn->real_escape_string($_POST['congnang']);
    $image = trim($conn->real_escape_string($_POST['image']));
    $gia = floatval($_POST['gia']);
    $loai_id = intval($_POST['loai_id']);
    $chungloai_id = intval($_POST['chungloai_id']);

    // Kiểm tra dữ liệu bắt buộc
    if (
        empty($ten_sanpham) || empty($ma_sp) || empty($bemat) ||
        empty($chatlieu) || empty($congnang) || empty($image) ||
        empty($gia) || empty($loai_id) || empty($chungloai_id)
    ) {
        echo "Vui lòng điền đầy đủ thông tin sản phẩm.";
        exit();
    }

    // Kiểm tra định dạng URL ảnh (có thể bỏ qua nếu không cần kiểm tra)
    if (!preg_match('/^(https?:\/\/)?([\w\-]+\.)+[\w\-]+(\/[\w\-\.\/]*)*\.(jpg|jpeg|png|gif)$/i', $image)) {
        echo "URL hình ảnh không hợp lệ. Vui lòng nhập liên kết hình ảnh kết thúc bằng jpg, png, v.v.";
        exit();
    }

    // Thêm vào bảng `sanpham`
    $sql = "INSERT INTO sanpham (ten_sanpham, ma_sp, bemat, chatlieu, congnang, image, gia, loai_id, chungloai_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssdii", $ten_sanpham, $ma_sp, $bemat, $chatlieu, $congnang, $image, $gia, $loai_id, $chungloai_id);

    if ($stmt->execute()) {
        session_start();
        $_SESSION['success'] = "Thêm sản phẩm thành công!";
        header("Location: ../products.php");
        exit();
    } else {
        echo "Lỗi khi thêm sản phẩm: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>