<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEADER</title>
    <link href="../css/style.css" rel="stylesheet" />
    <link href="../css/header-footer.css" rel="stylesheet" />
    <script src="../jquery-3.7.1.js"></script>
    <script src="../javascript/index.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
</head>
<body>
    <div id="topbar">
        <div class="topbar-left">
            <a href="index.php">
                <img src="../img/logo.jpg" alt="Logo BLife" id="logo">
            </a>
            <a href="index.php" class="brand-link">
                <span class="brand">
                    <span class="brand-main">BLife</span>
                    <span class="brand-sub">.com</span>
                    <div class="brand-slogan">Nâng tầm phong cách sống</div>
                </span>
            </a>
            <span class="hours">
                <i class="fa-solid fa-clock"></i>
                <b>08:00 - 18:00</b>
            </span>
        </div>
        <div class="topbar-right">
            
           <!-- Giỏ hàng -->
        <a href="cart.php" class="cart-icon">
            <i class="fa-solid fa-shopping-cart"></i>
            <span class="cart-count" id="cart-count">0</span>
        </a>
        <span class="divider">|</span>
        
        <span class="icon user-icon">👤</span>
        <?php
        if (isset($_SESSION['user_name'])) {
            echo '<span class="top-bar__right__item">Hi, ' . htmlspecialchars($_SESSION['user_name']) . '</span><span class="pipe2">|</span>';
            echo '<a href="logout.php" class="top-bar__right__item">Đăng xuất</a>';
        } else {
            echo '<a href="register.php" class="top-bar__right__item">Đăng ký</a><span class="pipe2">|</span>';
            echo '<a href="login.php" class="top-bar__right__item">Đăng nhập</a>';
        }
        ?>
    </div>
</div>
<script src="../jquery-3.7.1.js"></script>
<script>
function updateCartCount() {
    $.ajax({
        url: 'cart_api.php',
        method: 'POST',
        data: { action: 'get_cart_count' },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                $('#cart-count').text(response.count);
            } else {
                console.log('Lỗi cập nhật số lượng:', response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('Lỗi cập nhật số lượng giỏ hàng:', xhr.status, error, xhr.responseText);
        }
    });
}
$(document).ready(function() {
    updateCartCount();
    $(document).on('cartUpdated', updateCartCount);
});
</script>

    <!-- Main bar -->
    <nav id="main-nav">
        <a href="index.php">TRANG CHỦ</a>
        <a href="gioithieu.php">GIỚI THIỆU</a>
        <a href="sanpham.php">SẢN PHẨM</a>
        <a href="tintuc.php">TIN TỨC</a>
        <a href="khuyenmai.php">ƯU ĐÃI</a>
        <a href="map.php">BẢN ĐỒ</a>
    </nav>
    
    <script>
        // Load cart count when page loads
        $(document).ready(function() {
            updateCartCount();
        });
        
        // Function to update cart count
        function updateCartCount() {
            $.ajax({
                url: 'cart_api.php',
                method: 'POST',
                data: { action: 'get_cart_count' },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const count = response.count;
                        const cartCountElement = $('#cart-count');
                        
                        if (count > 0) {
                            cartCountElement.text(count).removeClass('hidden');
                        } else {
                            cartCountElement.addClass('hidden');
                        }
                    }
                },
                error: function() {
                    console.error('Error loading cart count');
                }
            });
        }
        
        // Make updateCartCount available globally
        window.updateCartCount = updateCartCount;
    </script>
</body>
</html>
