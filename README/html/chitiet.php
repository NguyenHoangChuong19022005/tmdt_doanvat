<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang sản phẩm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../css/style.css" rel="stylesheet"/>
    <link href="../css/sanpham.css" rel="stylesheet"/>
    <link href="../css/chitiet.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    <script src="../jquery-3.7.1.js"></script>
    <script src="../js/chitiet.js"></script>
</head>
<body>
<div id="container">
    <div id="include-header"></div>
    <script>
        $(function () {
            $("#include-header").load("header.php");
        });
    </script>
    <main>
        <?php
        $link = mysqli_connect("localhost", "root", "", "doan_vat");
        mysqli_set_charset($link, "utf8");
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        $sql = "SELECT sp.*, l.loai_name, cl.loaidoan, ud.phamtram_uudai, ud.giasau_uudai
                FROM sanpham sp
                JOIN loai_sanpham l ON sp.loai_id = l.loai_id
                JOIN chungloai_sanpham cl ON sp.chungloai_id = cl.chungloai_id
                LEFT JOIN uudai ud ON sp.sanpham_id = ud.sanpham_id AND ud.trangthai_uudai = 1
                WHERE sp.sanpham_id = $id";
        $result = mysqli_query($link, $sql);

        if ($sp = mysqli_fetch_assoc($result)) {
            $gia = (float)$sp['gia'];
            $uudai = (float)$sp['phamtram_uudai'];
            $gia_sau_uudai = (float)$sp['giasau_uudai'];
            $gia_km = ($gia > 0) ? ($gia_sau_uudai > 0 ? $gia_sau_uudai : ($uudai > 0 ? $gia - ($gia * $uudai / 100) : 0)) : 0;
            $gia_format = $gia > 0 ? number_format($gia) . " đ" : "Liên hệ";
            $gia_km_format = $gia_km > 0 ? number_format($gia_km) . " đ" : "Liên hệ";
            $img = $sp['image'] ? base64_encode($sp['image']) : '';

            echo "<div class='product-detail-container'>";
            echo "<div class='zoom-container'>";
            echo "<img src='data:image/jpeg;base64,$img' alt='" . htmlspecialchars($sp['ten_sanpham']) . "' onerror='this.src=\"placeholder.jpg\"'>";
            echo "</div>";
            echo "<div class='product-info'>";
            echo "<h2>" . htmlspecialchars($sp['ten_sanpham']) . "</h2>";
            echo "<table class='product-table'>";
            echo "<tr><td>Mã sản phẩm</td><td>" . htmlspecialchars($sp['ma_sp']) . "</td></tr>";
            echo "<tr><td>Kiểu</td><td>" . htmlspecialchars($sp['loai_name']) . "</td></tr>";
            echo "<tr><td>Loại món ăn</td><td>" . htmlspecialchars($sp['loaidoan']) . "</td></tr>";
            echo "<tr><td>Vị</td><td>" . htmlspecialchars($sp['thanhphan']) . "</td></tr>";
            echo "<tr><td>Thành phần</td><td>" . htmlspecialchars($sp['vi']) . "</td></tr>";
            if ($gia_km > 0 && $gia_km < $gia) {
                echo "<tr><td>Ưu đãi</td><td>-" . htmlspecialchars($uudai) . "%</td></tr>";
                echo "<tr><td>Giá gốc</td><td><del>$gia_format</del></td></tr>";
                echo "<tr><td>Giá khuyến mãi</td><td class='price-promo'>$gia_km_format</td></tr>";
            } else {
                echo "<tr><td>Giá bán</td><td class='price-promo'>$gia_format</td></tr>";
            }
            if (!empty($sp['mota'])) {
                echo "<tr><td>Mô tả</td><td>" . nl2br(htmlspecialchars($sp['mota'])) . "</td></tr>";
            }
            echo "</table>";
            echo "<div class='quantity-container'>";
            echo "<label for='quantity'>Số lượng:</label>";
            echo "<button class='quantity-btn' onclick='changeQuantity(-1, \"quantity\")'>-</button>";
            echo "<input type='text' id='quantity' class='quantity-input' value='1' readonly>";
            echo "<button class='quantity-btn' onclick='changeQuantity(1, \"quantity\")'>+</button>";
            echo "</div>";
            echo "<div class='button-container'>";
            echo "<button class='btn-add-to-cart' data-product-id='" . htmlspecialchars($sp['sanpham_id']) . "'><i class='fas fa-cart-plus'></i> Thêm vào giỏ hàng</button>";
            echo "<button class='btn-buy' data-product-id='" . htmlspecialchars($sp['sanpham_id']) . "'><i class='fas fa-shopping-cart'></i> Mua hàng</button>";
            echo "</div>";
            echo "</div></div>";
        } else {
            echo "<p style='color:red;'>Không tìm thấy sản phẩm.</p>";
        }
        mysqli_close($link);
        ?>
    </main>
    <div id="include-footer"></div>
    <script>
        $(function () {
            $("#include-footer").load("footer.php");
        });
    </script>
</div>
</body>
</html>