<?php
session_start();
if (isset($_SESSION['success'])) {
    $message = $_SESSION['success'];
    echo "<script>alert('$message');</script>";
    unset($_SESSION['success']);
}
// Xóa session khi người dùng vào trang admin.php


// Kiểm tra nếu chưa đăng nhập hoặc không phải admin/staff
if (!isset($_SESSION['user']) || !isset($_SESSION['role']) || 
    ($_SESSION['role'] !== 'admin')) {
    
    // Chuyển về trang login
    header("Location: ../login.php");
    exit();
}
?>



<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Danh mục</title>
    <link rel="stylesheet" href="css/styles_admin.css?v=1">
    <link rel="stylesheet" href="css/categories.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <!-- Menu -->
        <?php include 'template/sidebar.php'; ?>
        <!-- End menu -->
        <!-- Header -->
        <?php include 'template/header.php'; ?>
        <!-- End header -->
        <!-- Nội dung chính -->
        <main class="main-content">
            <h2>Quản lý loại sản phẩm</h2>

            <!-- Tìm kiếm và lọc -->
            <div class="filter-section">
                <input type="text" id="search" placeholder="Tìm kiếm ..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                <button onclick="applyFilter()">Lọc</button>
            </div>

            <!-- Nút thêm danh mục -->
            <button class="add-btn" onclick="showForm()">Thêm loại gạch mới</button>

            <!-- Form thêm danh mục -->
            <div id="category-form" class="hidden">
                <h3>Thêm loại gạch mới</h3>
                <form id="add-category-form" method="POST" action="XuLy_ProductsType/add.php">
                    <label for="category-name">Tên loại:</label>
                    <input type="text" id="category-name" name="category_name" required>
                    <button type="submit" class="submit-btn">Thêm</button>
                </form>
            </div>

            <!-- Bảng danh sách danh mục -->
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên loại gạch</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Kết nối CSDL
                    require_once("./database.php");
                    $search = isset($_GET['search']) ? $_GET['search'] : '';
                    $sql = "SELECT * FROM loai_sanpham";
                    if ($search) {
                        $sql .= " WHERE loai_name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
                    }
                    $result = mysqli_query($conn, $sql);
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";
                        echo "<td>" . $row['loai_id'] . "</td>";
                        echo "<td>" . htmlspecialchars($row['loai_name']) . "</td>";
                        echo "<td>
                                <a href='XuLy_ProductsType/edit.php?id=" . $row['loai_id'] . "'>Sửa</a> | 
                                <a href='XuLy_ProductsType/delete.php?id=" . $row['loai_id'] . "' onclick=\"return confirm('Bạn có chắc muốn xóa?')\">Xóa</a>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
            <!-- End Bảng danh sách danh mục -->
        </main>
        <!-- End Nội dung chính -->
        <!-- Footer -->
        <?php include 'template/footer.php'; ?>
        <!-- End Footer -->
    </div>

    <script>
    function showForm() {
        var form = document.getElementById('category-form');
        if (form.classList.contains('hidden')) {
            form.classList.remove('hidden');
        } else {
            form.classList.add('hidden');
        }
    }

    function applyFilter() {
        const search = document.getElementById('search').value;
        const status = document.getElementById('status-filter').value;
        let url = 'products_type.php?';
        let params = [];
        if (search) params.push('search=' + encodeURIComponent(search));
        if (status) params.push('status=' + encodeURIComponent(status));
        url += params.join('&');
        window.location.href = url;
    }

    document.getElementById('search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            applyFilter();
        }
    });
    </script>
</body>
</html>