<?php
$conn = new mysqli("localhost", "root", "", "showroom_gach");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT loai_name FROM loai_sanpham WHERE loai_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
}

if (isset($_POST['submit'])) {
    $loai_name = $_POST['loai_name'];
    $sql = "UPDATE loai_sanpham SET loai_name = ? WHERE loai_id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $loai_name, $id);
    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../products_type.php");
    } else {
        echo "Lỗi: " . $conn->error;
    }
}

$conn->close();
?>

<!-- <form action="" method="post">
    <label for="loai_name">Tên loại sản phẩm:</label>
    <input type="text" id="loai_name" name="loai_name" value="<?php echo isset($row) ? $row['loai_name'] : ''; ?>">
    <input type="submit" name="submit" value="Sửa">
</form> -->
<!DOCTYPE html>
<html>
<head>
	<title>Edit Loại Sản Phẩm</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
	<div class="container">
		<h1>Edit Loại Sản Phẩm</h1>
		<form action="" method="post">
			<label for="loai_name">Tên loại sản phẩm:</label>
			<input type="text" id="loai_name" name="loai_name" value="<?php echo isset($row) ? $row['loai_name'] : ''; ?>">
			<input type="submit" name="submit" value="Sửa">
		</form>
	</div>
</body>
</html>
<style>
body {
	font-family: Arial, sans-serif;
	background-color: #f0f0f0;
}

.container {
	width: 80%;
	margin: 40px auto;
	background-color: #fff;
	padding: 20px;
	border: 1px solid #ddd;
	box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

h1 {
	color: #333;
	margin-bottom: 20px;
}

label {
	display: block;
	margin-bottom: 10px;
}

input[type="text"] {
	width: 100%;
	height: 40px;
	margin-bottom: 20px;
	padding: 10px;
	border: 1px solid #ccc;
}

input[type="submit"] {
	background-color:rgb(234, 255, 0);
	color: #fff;
	padding: 10px 20px;
	border: none;
	border-radius: 5px;
	cursor: pointer;
}

input[type="submit"]:hover {
	background-color:rgb(209, 241, 49);
}
</style>