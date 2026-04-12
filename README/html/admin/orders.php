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
    header("Location: ../login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "showroom_gach");
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Xử lý phân trang
$rows_per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;

// Đếm tổng số hàng để tính số trang
$count_sql = "SELECT COUNT(*) FROM orders 
              WHERE full_name LIKE '%$search%' 
                  OR email LIKE '%$search%' 
                  OR phone LIKE '%$search%' 
                  OR order_code LIKE '%$search%'";
$count_result = $conn->query($count_sql);
$total_rows = $count_result->fetch_array()[0];
$total_pages = ceil($total_rows / $rows_per_page);

// Truy vấn chính lấy dữ liệu từ orders
$sql = "SELECT orders.* 
        FROM orders 
        WHERE full_name LIKE '%$search%' 
            OR email LIKE '%$search%' 
            OR phone LIKE '%$search%' 
            OR order_code LIKE '%$search%' 
        LIMIT $offset, $rows_per_page";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Hóa đơn</title>
    <link rel="stylesheet" href="css/styles_admin.css?v=1">
    <link rel="stylesheet" href="css/order.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
<div class="wrapper">
    <?php include 'template/sidebar.php'; ?>
    <?php include 'template/header.php'; ?>

    <main class="main-content">
        <h2>Quản lý Hóa đơn</h2>

        <form method="GET" action="orders.php">
            <input type="text" name="search" value="<?= htmlspecialchars($search); ?>" placeholder="Tìm kiếm theo tên, email, SĐT, mã đơn...">
            <button type="submit">Tìm kiếm</button>
        </form>

        <table>
            <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Khách hàng</th>
                <th>Email</th>
                <th>Số ĐT</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
            </thead>
            <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['order_code']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td><?= htmlspecialchars($row['phone']) ?></td>
                    <td>
                        <form action="XuLy_Order/update_status.php" method="POST">
                            <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                            <select name="status" class="status-select" onchange="this.form.submit()">
                                <?php
                                $statuses = ['đang chờ','đang xử lý','đã vận chuyển','đã giao hàng','đã huỷ'];
                                foreach ($statuses as $status) {
                                    $selected = $status == $row['status'] ? 'selected' : '';
                                    echo "<option value='$status' $selected>$status</option>";
                                }
                                ?>
                            </select>
                        </form>
                    </td>
                    <td>
                        <a class="edit-btn" href="XuLy_Order/view.php?id=<?= $row['id'] ?>">Chi tiết</a> |
                        <a class="delete-btn" href="XuLy_Order/delete.php?id=<?= $row['id'] ?>" 
                           onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này không?')">Xóa</a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>

        <!-- Phân trang -->
        <div class="pagination">
            <?php
            // Nút "Trước"
            if ($page > 1) {
                echo "<a href='orders.php?page=" . ($page - 1) . "&search=" . urlencode($search) . "'><</a>";
            } else {
                echo "<a style='background: #e0e0e0; color: #666666; cursor: not-allowed;'><</a>";
            }

            // Hiển thị số trang
            $max_pages_to_show = 5;
            $start_page = max(1, $page - 2);
            $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);

            if ($start_page > 1) {
                echo "<a href='orders.php?page=1&search=" . urlencode($search) . "'>1</a>";
                if ($start_page > 2) {
                    echo "<span class='dots'>...</span>";
                }
            }

            for ($i = $start_page; $i <= $end_page; $i++) {
                if ($i == $page) {
                    echo "<a class='active'>" . $i . "</a>";
                } else {
                    echo "<a href='orders.php?page=" . $i . "&search=" . urlencode($search) . "'>" . $i . "</a>";
                }
            }

            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo "<span class='dots'>...</span>";
                }
                echo "<a href='orders.php?page=" . $total_pages . "&search=" . urlencode($search) . "'>" . $total_pages . "</a>";
            }

            // Nút "Sau"
            if ($page < $total_pages) {
                echo "<a href='orders.php?page=" . ($page + 1) . "&search=" . urlencode($search) . "'>></a>";
            } else {
                echo "<a style='background: #e0e0e0; color: #666666; cursor: not-allowed;'>></a>";
            }
            ?>
        </div>
    </main>

    <?php include 'template/footer.php'; ?>
</div>
</body>
</html>