<?php
session_start();
if (isset($_SESSION['success'])) {
    $message = $_SESSION['success'];
    echo "<script>alert('$message');</script>";
    unset($_SESSION['success']);
}

if (
    !isset($_SESSION['user']) || !isset($_SESSION['role']) ||
    ($_SESSION['role'] !== 'admin')
) {
    header("Location: ../login.php");
    exit();
}


?>


<!-- Nội dung của products.php -->

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Sản phẩm</title>
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/styles_admin.css?v=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <?php include 'template/sidebar.php'; ?>
        <?php include 'template/header.php'; ?>
        <main class="main-content">
            <h2>Quản lý Sản phẩm</h2>

            <div class="filter-section">
                <input type="text" id="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">

                <select id="function-filter" name="congnang">
                    <option value="">Tất cả công năng</option>
                    <option value="Gạch lát nền" <?php echo (isset($_GET['congnang']) && $_GET['congnang'] == 'Gạch lát nền') ? 'selected' : ''; ?>>Gạch lát nền</option>
                    <option value="Gạch ốp tường" <?php echo (isset($_GET['congnang']) && $_GET['congnang'] == 'Gạch ốp tường') ? 'selected' : ''; ?>>Gạch ốp tường</option>
                    <option value="Gạch lát sân" <?php echo (isset($_GET['congnang']) && $_GET['congnang'] == 'Gạch lát sân') ? 'selected' : ''; ?>>Gạch lát sân</option>
                </select>

                <select id="sort" name="sort">
                                        <option value="id-asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'id-asc') ? 'selected' : ''; ?>>Cũ nhất</option>
                    <option value="id-desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'id-desc') ? 'selected' : ''; ?>>Mới nhất</option>
                    <option value="price-asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price-asc') ? 'selected' : ''; ?>>Giá tăng dần</option>
                    <option value="price-desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price-desc') ? 'selected' : ''; ?>>Giá giảm dần</option>
                </select>

                <button onclick="applyFilter()">Lọc</button>
            </div>

            <button class="add-btn" onclick="showForm()">Thêm Sản phẩm</button>
            <div id="product-form" class="hidden">
                <h3>Nhập thông tin sản phẩm</h3>
                <form id="add-product-form" method="POST" action="XuLy_Products/add.php" enctype="multipart/form-data">
                    <label for="product-name">Tên Sản phẩm:</label>
                    <input type="text" id="product-name" name="ten_sanpham" required>

                    <label for="ma_sp">Mã sản phẩm:</label>
                    <input type="text" id="ma_sp" name="ma_sp" required>

                    <label for="bemat">Bề mặt:</label>
                    <input type="text" id="bemat" name="bemat" required>

                    <label for="chatlieu">Chất liệu:</label>
                    <input type="text" id="chatlieu" name="chatlieu" required>

                    <label for="congnang">Công năng:</label>
                    <select id="congnang" name="congnang" required>
                        <option value="Gạch lát nền">Gạch lát nền</option>
                        <option value="Gạch ốp tường">Gạch ốp tường</option>
                        <option value="Gạch lát sân">Gạch lát sân</option>
                    </select>

                    <label for="image">Link hình ảnh:</label>
                    <input type="text" id="image" name="image" placeholder="Nhập URL hình ảnh..." required>

                    <label for="gia">Giá:</label>
                    <input type="number" id="gia" name="gia" required min="0">

                    <label for="loai_id">Loại ID:</label>
                    <input type="number" id="loai_id" name="loai_id" required min="1">

                    <label for="chungloai_id">Chủng loại ID:</label>
                    <input type="number" id="chungloai_id" name="chungloai_id" required min="1">

                    <button type="submit" class="submit-btn">Thêm</button>
                </form>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Sản phẩm</th>
                        <th>Mã SP</th>
                        <th>Bề mặt</th>
                        <th>Chất liệu</th>
                        <th>Công năng</th>
                        <th>Hình ảnh</th>
                        <th>Giá</th>
                        <th>Loại ID</th>
                        <th>Chủng loại ID</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
            <tbody>
                    <?php
                    $conn = new mysqli("localhost", "root", "", "showroom_gach");
                    if ($conn->connect_error) {
                        die("Kết nối thất bại: " . $conn->connect_error);
                    }

                    // Xử lý phân trang
                    $rows_per_page = 10;
                    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                    $offset = ($page - 1) * $rows_per_page;

                    // Đếm tổng số hàng
                    $count_sql = "SELECT COUNT(*) FROM sanpham WHERE 1=1";
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $search = $conn->real_escape_string($_GET['search']);
                        $count_sql .= " AND name LIKE '%$search%'";
                    }
                    if (isset($_GET['category']) && !empty($_GET['category'])) {
                        $category = $conn->real_escape_string($_GET['category']);
                        $count_sql .= " AND category = '$category'";
                    }
                    $count_result = mysqli_query($conn, $count_sql);
                    $total_rows = mysqli_fetch_array($count_result)[0];
                    $total_pages = ceil($total_rows / $rows_per_page);

                    // Truy vấn sản phẩm với phân trang
                    $sql = "SELECT * FROM sanpham WHERE 1=1";
                    if (isset($_GET['search']) && !empty($_GET['search'])) {
                        $sql .= " AND name LIKE '%$search%'";
                    }
                    if (isset($_GET['congnang']) && !empty($_GET['congnang'])) {
                        $congnang = $conn->real_escape_string($_GET['congnang']);
                        $sql .= " AND congnang = '$congnang'";
                    }
                    if (isset($_GET['category']) && !empty($_GET['category'])) {
                        $sql .= " AND category = '$category'";
                    }
                    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id-asc'; // Mặc định là cũ nhất (ID tăng dần)

                    if ($sort == 'price-asc') {
                        $sql .= " ORDER BY gia ASC";
                    } elseif ($sort == 'price-desc') {
                        $sql .= " ORDER BY gia DESC";
                    } elseif ($sort == 'id-desc') {
                        $sql .= " ORDER BY sanpham_id DESC"; // Mới nhất
                    } elseif ($sort == 'id-asc') {
                        $sql .= " ORDER BY sanpham_id ASC"; // Cũ nhất
                    } else {
                        $sql .= " ORDER BY sanpham_id ASC"; // fallback vẫn là cũ nhất
                    }
                    $sql .= " LIMIT $offset, $rows_per_page";
                    $result = mysqli_query($conn, $sql);

                    while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>" . $row['sanpham_id'] . "</td>";
                    echo "<td>" . $row['ten_sanpham'] . "</td>";
                    echo "<td>" . $row['ma_sp'] . "</td>";
                    echo "<td>" . $row['bemat'] . "</td>";
                    echo "<td>" . $row['chatlieu'] . "</td>";
                    echo "<td>" . $row['congnang'] . "</td>";

                    // Hiển thị ảnh từ BLOB
                    $imageData = $row['image'];
                    $base64 = base64_encode($imageData);
                    echo "<td><img src='data:image/jpeg;base64,{$base64}' width='60'/></td>";

                    echo "<td>" . ($row['gia'] !== null ? number_format($row['gia'], 0, ',', '.') . " VND" : 'NULL') . "</td>";
                    echo "<td>" . $row['loai_id'] . "</td>";
                    echo "<td>" . $row['chungloai_id'] . "</td>";
                    echo "<td>
                            <a class='edit-btn' href='XuLy_Products/edit.php?id=" . $row['sanpham_id'] . "'>Sửa</a>
                            <a class='delete-btn' href='XuLy_Products/delete.php?id=" . $row['sanpham_id'] . "' onclick='return confirm(\"Bạn có chắc chắn muốn xóa?\")'>Xóa</a>
                        </td>";
                    echo "</tr>";
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
                    echo "<a href='products.php?page=" . ($page - 1) . "&search=" . urlencode($_GET['search'] ?? '') . "&category=" . urlencode($_GET['category'] ?? '') . "&sort=" . urlencode($_GET['sort'] ?? '') . "'><</a>";
                } else {
                    echo "<a style='background: #e0e0e0; color: #666666; cursor: not-allowed;'><</a>";
                }

                // Hiển thị số trang
                $max_pages_to_show = 5;
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + $max_pages_to_show - 1);

                if ($start_page > 1) {
                    echo "<a href='products.php?page=1&search=" . urlencode($_GET['search'] ?? '') . "&category=" . urlencode($_GET['category'] ?? '') . "&sort=" . urlencode($_GET['sort'] ?? '') . "'>1</a>";
                    if ($start_page > 2) {
                        echo "<span class='dots'>...</span>";
                    }
                }

                for ($i = $start_page; $i <= $end_page; $i++) {
                    if ($i == $page) {
                        echo "<a class='active'>" . $i . "</a>";
                    } else {
                        echo "<a href='products.php?page=" . $i . "&search=" . urlencode($_GET['search'] ?? '') . "&category=" . urlencode($_GET['category'] ?? '') . "&sort=" . urlencode($_GET['sort'] ?? '') . "'>" . $i . "</a>";
                    }
                }

                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo "<span class='dots'>...</span>";
                    }
                    echo "<a href='products.php?page=" . $total_pages . "&search=" . urlencode($_GET['search'] ?? '') . "&category=" . urlencode($_GET['category'] ?? '') . "&sort=" . urlencode($_GET['sort'] ?? '') . "'>" . $total_pages . "</a>";
                }

                // Nút "Sau"
                if ($page < $total_pages) {
                    echo "<a href='products.php?page=" . ($page + 1) . "&search=" . urlencode($_GET['search'] ?? '') . "&category=" . urlencode($_GET['category'] ?? '') . "&sort=" . urlencode($_GET['sort'] ?? '') . "'>></a>";
                } else {
                    echo "<a style='background: #e0e0e0; color: #666666; cursor: not-allowed;'>></a>";
                }
                ?>
            </div>
        </main>
        <?php include 'template/footer.php'; ?>
    </div>
    <script>
        function showForm() {
            var form = document.getElementById('product-form');
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
            } else {
                form.classList.add('hidden');
            }
        }

        function applyFilter() {
            const search = document.getElementById('search').value;
            const congnang = document.getElementById('function-filter').value;
            const sort = document.getElementById('sort').value;

            let url = 'products.php?';
            let params = [];
            if (search) params.push('search=' + encodeURIComponent(search));
            if (congnang) params.push('congnang=' + encodeURIComponent(congnang));
            if (sort) params.push('sort=' + encodeURIComponent(sort));

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