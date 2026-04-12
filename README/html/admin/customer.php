<?php
session_start();
if (isset($_SESSION['success'])) {
    $message = $_SESSION['success'];
    echo "<script>alert('$message');</script>";
    unset($_SESSION['success']);
}

// Kiểm tra nếu chưa đăng nhập hoặc không phải admin/staff
if (!isset($_SESSION['user']) || !isset($_SESSION['role']) || 
    ($_SESSION['role'] !== 'admin')) {
    
    // Chuyển về trang login
    header("Location: ../login.php");
    exit();
}
?>

<?php
$conn = new mysqli("localhost", "root", "", "showroom_gach");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Nếu có từ khóa tìm kiếm, thay đổi câu truy vấn
$rows_per_page = 10; // Số hàng trên mỗi trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;

// Câu lệnh đếm tổng số bản ghi phù hợp với tìm kiếm
$count_sql = "SELECT COUNT(*) FROM users 
              WHERE name LIKE '%$search%' 
              OR email LIKE '%$search%' 
              OR phone LIKE '%$search%'";
$count_result = mysqli_query($conn, $count_sql);
$total_rows = mysqli_fetch_array($count_result)[0];
$total_pages = ceil($total_rows / $rows_per_page);

// Truy vấn dữ liệu người dùng với giới hạn phân trang (không dùng ORDER BY created_at)
$sql = "SELECT * FROM users 
        WHERE name LIKE '%$search%' 
        OR email LIKE '%$search%' 
        OR phone LIKE '%$search%' 
        LIMIT $offset, $rows_per_page";
$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Khách hàng</title>
    <link rel="stylesheet" href="css/styles_admin.css?v=1">
    <link rel="stylesheet" href="css/customer.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <!-- menu -->
        <?php include 'template/sidebar.php'; ?>
        <!-- End menu -->
        
        <!-- header -->
        <?php include 'template/header.php'; ?>
        <!-- end header -->
        
        <!-- Nội dung chính -->
        <main class="main-content">
            <h2>Quản lý Khách hàng</h2>
            <form method="GET" action="customer.php">
                <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Tìm kiếm khách hàng">
                <button type="submit">Tìm kiếm</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Mật Khẩu</th>
                        <th>Số Điện Thoại</th>
                        <th>Địa Chỉ</th>
                        <th>Trạng Thái</th>
                        <th>Role</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['id'] . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['email']) . "</td>";  
                            echo "<td>" . htmlspecialchars($row['password']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['phone']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['address']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                            echo "<td>
                                    <a class='edit-btn' href='XuLy_Customer/edit.php?id=" . $row['id'] . "'>Sửa</a> |
                                    <a class='delete-btn' href='XuLy_Customer/delete.php?id=" . $row['id'] . "' onclick='return confirm(\"Bạn có chắc chắn muốn xóa người dùng này?\")'>Xóa</a>
                                </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='8'>Không tìm thấy khách hàng nào.</td></tr>";
                    }

                    mysqli_close($conn);
                    ?>
                </tbody>
            </table>
            <!-- Phân trang -->
            <div class="pagination">
                <?php
                // Nút "Trước"
                if ($page > 1) {
                    echo "<a href='customer.php?page=" . ($page - 1) . "&search=" . urlencode($search) . "'><</a>";
                } else {
                    echo "<a style='background: #e0e0e0; color: #666666; cursor: not-allowed;'><</a>";
                }

                // Hiển thị số trang
                $max_pages_to_show = 5; // Số trang tối đa hiển thị trước khi dùng "..."
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);

                if ($start_page > 1) {
                    echo "<a href='customer.php?page=1&search=" . urlencode($search) . "'>1</a>";
                    if ($start_page > 2) {
                        echo "<span class='dots'>...</span>";
                    }
                }

                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $page) {
                        echo "<a class='active'>" . $i . "</a>";
                    } else {
                        echo "<a href='customer.php?page=" . $i . "&search=" . urlencode($search) . "'>" . $i . "</a>";
                    }
                }

                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo "<span class='dots'>...</span>";
                    }
                    echo "<a href='customer.php?page=" . $total_pages . "&search=" . urlencode($search) . "'>" . $total_pages . "</a>";
                }

                // Nút "Sau"
                if ($page < $total_pages) {
                    echo "<a href='customer.php?page=" . ($page + 1) . "&search=" . urlencode($search) . "'>></a>";
                } else {
                    echo "<a style='background: #e0e0e0; color: #666666; cursor: not-allowed;'>></a>";
                }
                ?>
            </div>
        </main>
        
        <!-- Footer -->
        <?php include 'template/footer.php'; ?> 
        <!-- End Footer -->
    </div>

</body>
</html>