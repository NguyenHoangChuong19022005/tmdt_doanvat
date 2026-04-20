<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Footer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../css/header-footer.css">
</head>
<body>
    <footer id="main-footer">
        <div class="footer-container">
            <div class="footer-col company-info">
                <img src="../img/logo.jpg" alt="Logo" id="logo">
                <p>Hệ thống phân phối online với mức giá chiết khấu cao.<br></p>
                <ul class="footer-contact">
                    <li><i class="fa-solid fa-location-dot"></i>  02 Võ Oanh, Phường 25, Bình Thạnh, Hồ Chí Minh</li>
                    <li><i class="fa-solid fa-phone"></i>  028 3899 2862</li>
                    <li><i class="fa-solid fa-envelope"></i> Email: thuantt1708@ut.edu.vn</li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Chính sách bán hàng</h3>
                <ul>
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Quy định đổi trả</a></li>
                    <li><a href="#">Chính sách bảo hành</a></li>
                    <li><a href="#">Hướng dẫn thanh toán</a></li>
                    <li><a href="#">Phương thức giao hàng</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h3>Hỗ trợ dịch vụ</h3>
                <ul>
                    <li><a href="#">Liên hệ hỗ trợ</a></li>
                    <li><a href="#">Thông tin tư vấn</a></li>
                    <li><a href="#">Tra cứu đơn hàng</a></li>
                    <li><a href="#">Tài khoản khách hàng</a></li>
                </ul>
            </div>
            <div class="footer-col app-social">
                <div>
                    <b>Ứng dụng trên:</b>
                    <div class="footer-apps">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/7/78/Google_Play_Store_badge_EN.svg" alt="Google Play" class="footer-app-badge">
                        <img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store" class="footer-app-badge">
                    </div>
                </div>
                <div class="footer-social">
                    <b>Kết nối với chúng tôi:</b>
                    <div class="footer-icons">
                        <a href="#"><i class="fa-brands fa-facebook-square" style="color:#4267B2"></i></a>
                        <a href="#"><i class="fa-brands fa-x-twitter" style="color:#222"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram" style="color:#C13584"></i></a>
                        <a href="#"><i class="fa-brands fa-youtube" style="color:#FF0000"></i></a>
                        <a href="#"><i class="fa-brands fa-pinterest" style="color:#E60023"></i></a>
                        <a href="#"><i class="fa-brands fa-linkedin" style="color:#0077B5"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating Action Buttons -->
    <style>
        .floating-buttons {
            position: fixed !important;
            bottom: 30px !important;
            right: 30px !important;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 999999 !important;
        }
        .float-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
            position: relative;
        }
        .float-btn:hover {
            transform: scale(1.15);
        }
        .map-btn {
            background-color: #fff;
            border: 4px solid rgba(255, 99, 99, 0.4);
            box-sizing: content-box;
            width: 42px;
            height: 42px;
        }
        .map-btn img {
            width: 26px !important;
            height: 26px !important;
            object-fit: contain;
        }
        .phone-btn {
            background-color: #32a852;
            color: #fff;
            font-size: 24px;
            border-radius: 14px;
            animation: phoneRing 2s infinite ease-in-out;
        }
        @keyframes phoneRing {
            0% { transform: scale(1) rotate(0deg); }
            10% { transform: scale(1.1) rotate(-15deg); }
            20% { transform: scale(1.1) rotate(15deg); }
            30% { transform: scale(1.1) rotate(-15deg); }
            40% { transform: scale(1.1) rotate(15deg); }
            50% { transform: scale(1) rotate(0deg); }
            100% { transform: scale(1) rotate(0deg); }
        }
    </style>
    <div class="floating-buttons">
        <!-- Nút Map -->
        <a href="https://maps.google.com/?q=02+Võ+Oanh,+Phường+25,+Bình+Thạnh,+Hồ+Chí+Minh" target="_blank" class="float-btn map-btn" title="Chỉ đường trên Google Maps">
            <img src="https://upload.wikimedia.org/wikipedia/commons/a/aa/Google_Maps_icon_%282020%29.svg" alt="Google Maps" style="width:26px; height:26px; object-fit:contain;">
        </a>
        
        <!-- Nút Gọi điện -->
        <a href="tel:02838992862" class="float-btn phone-btn" title="Gọi ngay cho chúng tôi">
            <i class="fa-solid fa-phone-volume"></i>
        </a>
    </div>
</body>
</html>
