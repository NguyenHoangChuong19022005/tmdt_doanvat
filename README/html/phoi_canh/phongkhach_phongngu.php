<?php include("../../connect.php"); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phối cảnh phòng khách & phòng ngủ</title>
    <link href="../../foldercss/style.css" rel="stylesheet"/>
    <link href="../../foldercss/phoicanh.css" rel="stylesheet"/>
    <script src="../../jquery-3.7.1.js"></script>
    <style>
        .phoicanh-detail-page {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }
        .page-header h1 {
            color: #333;
            margin-bottom: 10px;
        }
        .page-header p {
            color: #666;
            font-size: 16px;
        }
        .back-nav {
            margin-bottom: 20px;
        }
        .back-nav a {
            display: inline-block;
            background: #007bff;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .back-nav a:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <div id="container">
        <!-- Gọi header -->
        <div id="include-header"></div>
        <script>
        $(function () {
            $("#include-header").load("../header.php");
        });
        </script>

        <div class="phoicanh-detail-page">
            <div class="back-nav">
                <a href="../phoicanh.php">← Quay lại trang phối cảnh</a>
            </div>

            <div class="page-header">
                <h1>Phối cảnh phòng khách & phòng ngủ</h1>
                <p>Khám phá những mẫu gạch lát nền đẹp cho phòng khách và phòng ngủ</p>
            </div>

            <div class="phoicanh-gallery" id="phoicanh-gallery">
                <?php
                // Lấy dữ liệu phối cảnh từ database
                $sql = "SELECT * FROM phoicanh WHERE id_phoicanh BETWEEN 1 AND 4 ORDER BY id_phoicanh";
                $result = mysqli_query($conn, $sql);
                
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo '<div class="phoicanh-item" data-id="' . $row['id_phoicanh'] . '">';
                        echo '<img src="../../img/imgphoicanh/' . $row['hinhanh'] . '" alt="Phối cảnh ' . $row['id_phoicanh'] . '">';
                        echo '<div class="info">';
                        echo '<h4>Phối cảnh phòng khách & phòng ngủ ' . $row['id_phoicanh'] . '</h4>';
                        echo '<p>' . $row['mota'] . '</p>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p>Chưa có dữ liệu phối cảnh.</p>';
                }
                ?>
            </div>
        </div>

        <!-- Modal hiển thị chi tiết sản phẩm -->
        <div class="modal" id="product-modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <div id="product-detail">
                    <!-- Chi tiết sản phẩm sẽ được load từ AJAX -->
                </div>
            </div>
        </div>

        <!-- Gọi footer -->
        <div id="include-footer"></div>
        <script>
        $(function () {
            $("#include-footer").load("../footer.php");
        });
        </script>
    </div>

    <script>
        $(document).ready(function() {
            // Xử lý click vào phối cảnh item
            $('.phoicanh-item').click(function() {
                showRandomProduct();
            });

            // Xử lý modal
            const modal = document.querySelector('#product-modal');
            const closeBtn = document.querySelector('.close');

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    modal.style.display = 'none';
                });
            }

            if (modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                });
            }
        });

        // Hiển thị sản phẩm ngẫu nhiên
        function showRandomProduct() {
            $.ajax({
                url: '../phoicanh_api.php',
                method: 'POST',
                data: { action: 'get_sanpham_random' },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data.length > 0) {
                        const randomProduct = response.data[Math.floor(Math.random() * response.data.length)];
                        showProductDetail(randomProduct.sanpham_id);
                    }
                },
                error: function() {
                    console.error('Lỗi khi load sản phẩm');
                }
            });
        }

        // Hiển thị chi tiết sản phẩm
        function showProductDetail(sanphamId) {
            $.ajax({
                url: '../phoicanh_api.php',
                method: 'POST',
                data: { 
                    action: 'get_sanpham',
                    sanpham_id: sanphamId 
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        displayProductDetail(response.data);
                        $('#product-modal').show();
                    }
                },
                error: function() {
                    console.error('Lỗi khi load chi tiết sản phẩm');
                }
            });
        }

        // Hiển thị chi tiết sản phẩm trong modal
        function displayProductDetail(product) {
            const price = product.gia ? formatPrice(product.gia) + ' VNĐ' : 'Liên hệ';
            
            $('#product-detail').html(`
                <div class="product-detail">
                    <div class="product-image">
                        <img src="../../foldercss/Anhsp/${getImagePath(product.chungloai_id)}/${product.image}" alt="${product.ten_sanpham}">
                    </div>
                    <div class="product-info">
                        <h2>${product.ten_sanpham}</h2>
                        <div class="info-group">
                            <label>Mã sản phẩm:</label>
                            <span>${product.ma_sp}</span>
                        </div>
                        <div class="info-group">
                            <label>Bề mặt:</label>
                            <span>${product.bemat}</span>
                        </div>
                        <div class="info-group">
                            <label>Chất liệu:</label>
                            <span>${product.chatlieu}</span>
                        </div>
                        <div class="info-group">
                            <label>Công năng:</label>
                            <span>${product.congnang}</span>
                        </div>
                        <div class="info-group">
                            <label>Loại:</label>
                            <span>${product.ten_loai || 'Không xác định'}</span>
                        </div>
                        <div class="info-group">
                            <label>Kích thước:</label>
                            <span>${product.ten_chungloai || 'Không xác định'}</span>
                        </div>
                        <div class="price">${price}</div>
                    </div>
                </div>
            `);
        }

        // Lấy đường dẫn hình ảnh
        function getImagePath(chungloaiId) {
            switch(parseInt(chungloaiId)) {
                case 1:
                    return 'cloai3030/Latnen';
                case 2:
                    return 'cloai6060/Latnen';
                case 3:
                    return 'cloai8080/Latnen';
                default:
                    return 'cloai6060/Latnen';
            }
        }

        // Format giá tiền
        function formatPrice(price) {
            return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    </script>
</body>
</html>
