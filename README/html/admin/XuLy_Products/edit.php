<?php
ob_start();
$conn = new mysqli("localhost", "root", "", "showroom_gach");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$product = null;

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT * FROM sanpham WHERE sanpham_id = $id";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        echo "<h3>Không tìm thấy sản phẩm!</h3>";
        exit();
    }
} else {
    echo "<h3>Thiếu ID sản phẩm!</h3>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
    <style>
body {
    font-family: 'Poppins', sans-serif;
    background-color: #f8f8f8;
    margin: 0;
    padding: 40px 20px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}

form {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    padding: 30px 40px;
    width: 100%;
    max-width: 700px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

h2 {
    grid-column: 1 / span 2;
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    color: #d4af37;
    margin-bottom: 10px;
    text-transform: uppercase;
}

label {
    font-size: 14px;
    font-weight: 500;
    margin-bottom: 6px;
    display: block;
    color: #333;
}

input[type="text"],
input[type="number"],
select {
    width: 100%;
    padding: 10px 12px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-sizing: border-box;
    transition: border 0.2s ease;
}

input[type="text"]:focus,
input[type="number"]:focus,
select:focus {
    border-color: #d4af37;
    outline: none;
}

.form-group {
    display: flex;
    flex-direction: column;
}

button[type="submit"] {
    grid-column: 1 / span 2;
    margin-top: 10px;
    padding: 12px;
    background: linear-gradient(90deg, #f0c700, #d4af37);
    color: #fff;
    font-weight: 500;
    font-size: 16px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.3s ease;
}

button[type="submit"]:hover {
    background: linear-gradient(90deg, #d4af37, #f0c700);
}
.form-title {
    text-align: center;
    font-size: 24px;
    font-weight: 600;
    color: #d4af37;
    margin-bottom: 20px;
    text-transform: uppercase;
    font-family: 'Poppins', sans-serif;
}

/* Mobile responsive */
@media (max-width: 768px) {
    form {
        grid-template-columns: 1fr;
        padding: 25px 20px;
    }

    button[type="submit"] {
        grid-column: 1;
    }
}
    </style>
</head>
<body>
    
    <form method="POST" action="edit.php?id=<?= $product['sanpham_id'] ?>">
        <h2 class="form-title">SỬA SẢN PHẨM</h2>
        <input type="hidden" name="id" value="<?= $product['sanpham_id'] ?>">

        <label>Tên sản phẩm:</label>
        <input type="text" name="ten_sanpham" value="<?= htmlspecialchars($product['ten_sanpham']) ?>" required>

        <label>Mã sản phẩm:</label>
        <input type="text" name="ma_sp" value="<?= htmlspecialchars($product['ma_sp']) ?>" required>

        <label>Bề mặt:</label>
        <input type="text" name="bemat" value="<?= htmlspecialchars($product['bemat']) ?>" required>

        <label>Chất liệu:</label>
        <input type="text" name="chatlieu" value="<?= htmlspecialchars($product['chatlieu']) ?>" required>

        <label>Công năng:</label>
        <select name="congnang" required>
            <option value="Gạch lát nền" <?= $product['congnang'] == 'Gạch lát nền' ? 'selected' : '' ?>>Gạch lát nền</option>
            <option value="Gạch ốp tường" <?= $product['congnang'] == 'Gạch ốp tường' ? 'selected' : '' ?>>Gạch ốp tường</option>
            <option value="Gạch lát sân" <?= $product['congnang'] == 'Gạch lát sân' ? 'selected' : '' ?>>Gạch lát sân</option>
        </select>

        <label>Link hình ảnh (URL):</label>
        <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>" required>

        <label>Giá (VND):</label>
        <input type="number" name="gia" value="<?= htmlspecialchars($product['gia']) ?>" min="0" required>

        <label>Loại ID:</label>
        <input type="number" name="loai_id" value="<?= htmlspecialchars($product['loai_id']) ?>" min="1" required>

        <label>Chủng loại ID:</label>
        <input type="number" name="chungloai_id" value="<?= htmlspecialchars($product['chungloai_id']) ?>" min="1" required>

        <button type="submit">Cập nhật</button>
    </form>
</body>
</html>

<?php
// Xử lý cập nhật sau khi submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST['id']);
    $ten_sanpham = $conn->real_escape_string($_POST['ten_sanpham']);
    $ma_sp = $conn->real_escape_string($_POST['ma_sp']);
    $bemat = $conn->real_escape_string($_POST['bemat']);
    $chatlieu = $conn->real_escape_string($_POST['chatlieu']);
    $congnang = $conn->real_escape_string($_POST['congnang']);
    $image = $conn->real_escape_string($_POST['image']);
    $gia = floatval($_POST['gia']);
    $loai_id = intval($_POST['loai_id']);
    $chungloai_id = intval($_POST['chungloai_id']);

    $sql = "UPDATE sanpham SET 
                ten_sanpham='$ten_sanpham',
                ma_sp='$ma_sp',
                bemat='$bemat',
                chatlieu='$chatlieu',
                congnang='$congnang',
                image='$image',
                gia=$gia,
                loai_id=$loai_id,
                chungloai_id=$chungloai_id
            WHERE sanpham_id = $id";

    if (mysqli_query($conn, $sql)) {
        session_start();
        $_SESSION['success'] = "Cập nhật sản phẩm thành công!";
        header("Location: ../products.php");
        exit();
    } else {
        echo "Lỗi cập nhật: " . mysqli_error($conn);
    }
}

$conn->close();
ob_end_flush();
?>