<!DOCTYPE html >
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ</title>    
<link href="../css/style.css" rel="stylesheet" />
<link href="../css/index.css" rel="stylesheet" />
<script src="../jquery-3.7.1.js"></script>
<script src="../js/index.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
<div id="container">
    <!-- Top Bar, Main bar -->
    <div id="include-header"></div>
    <script>
    $(function () {
        $("#include-header").load("header.php");
    });
    </script>

    <section id="slideshow-section">
  <div id="slideshow-wrapper">
    <button class="slide-btn prev"><i class="fa-solid fa-chevron-left"></i></button>
    <div id="slideshow">
      <img src="../img/banner1.jpg" class="slide active" alt="Banner 1">
      <img src="../img/banner2.jpg" class="slide" alt="Banner 2">
      <img src="../img/banner3.jpg" class="slide" alt="Banner 3">
      <img src="../img/banner4.jpg" class="slide" alt="Banner 4">
      <img src="../img/banner5.jpg" class="slide" alt="Banner 5">
    </div>
    <button class="slide-btn next"><i class="fa-solid fa-chevron-right"></i></button>
    <div class="slide-dots">
      <span class="dot active"></span>
      <span class="dot"></span>
      <span class="dot"></span>
      <span class="dot"></span>
      <span class="dot"></span>
    </div>
  </div>
</section>

<section id="main-content" class="main-content">
  <div class="product-categories">
    <div class="category-item">
  <div class="img-hover-box">
    <img src="../img/banner4.jpg" alt="Gạch ốp tường">
    <button class="choose-btn" onclick="window.location.href='sanpham.php'">
      <i class="fa-solid fa-layer-group"></i> CHỌN GẠCH ỐP TƯỜNG
    </button>
  </div>
  <h2>GẠCH ỐP TƯỜNG</h2>
  <p>
    Các mẫu gạch ốp tường cho hàng rào, giếng trời, tiểu cảnh, mặt tiền cùng các loại tượng đèn đặt để sân vườn.
  </p>
</div>
<div class="category-item">
  <div class="img-hover-box">
    <img src="../img/gachlatsan.jpg" alt="Gạch lát sân">
    <button class="choose-btn" onclick="window.location.href='sanpham.php'">
      <i class="fa-solid fa-layer-group"></i> CHỌN ĐÁ LÁT SÂN
    </button>
  </div>
  <h2>GẠCH LÁT SÂN</h2>
  <p>
    Các mẫu gạch lát sân vườn, lát lối đi, lát vỉa hè, sân thượng, lát nền nhà và bó vỉa hè, sỏi cuội.
  </p>
</div>
<div class="category-item">
  <div class="img-hover-box">
    <img src="../img/banner3.jpg" alt="Gạch ốp lát">
    <button class="choose-btn" onclick="window.location.href='sanpham.php'">
      <i class="fa-solid fa-layer-group"></i> CHỌN GẠCH ỐP LÁT
    </button>
  </div>
  <h2>GẠCH ỐP LÁT</h2>
  <p>
    Gạch ốp tường 30x60, 60x120. Gạch lát nền 60x60, 80x80, 100x100. Gạch giả gỗ 20x120. Gạch lát sân...
  </p>
</div>
  </div>
</section>
<section class="intro-section">
  <div class="intro-container">
    <div class="intro-text">
      <div class="intro-title">giới thiệu</div>
      <h1>BetterLife</h1>
      <blockquote>
       BetterLife là thương hiệu showroom uy tín chuyên cung cấp các dòng gạch ốp lát, gạch ốp tường cao cấp cùng nhiều giải pháp hoàn thiện nội ngoại thất hiện đại. Với phương châm “Chất lượng là nền tảng - Thẩm mỹ là điểm nhấn”, chúng tôi cam kết mang đến cho khách hàng sự lựa chọn đa dạng, mẫu mã tinh tế, và dịch vụ tư vấn tận tâm. BetterLife không chỉ cung cấp vật liệu, mà còn đồng hành cùng bạn kiến tạo nên những không gian sống đẹp hơn - bền hơn - đẳng cấp hơn mỗi ngày.
      </blockquote>
      <button class="intro-btn" onclick="window.location.href='gioithieu.html'">Tìm hiểu thêm</button>
    </div>
    <div class="intro-image">
      <img src="../img/gioithieu.jpg" alt="Giới thiệu kho đá sân vườn">
    </div>
  </div>
</section>
</div>
<!-- Thêm dưới đây -->
<section class="project-section">
  <div class="project-bg-overlay"></div>
  <h2 class="project-title">CÔNG TRÌNH HOÀN THIỆN</h2>
  <div class="project-slider">
    <button class="project-btn prev"></button>
    <div class="project-content">
      <div class="project-image">
        <img src="../img/imgduan/duan6.png" alt="Nhà phố Hưng Yên" />
      </div>
      <div class="project-info">
        <h3>Biệt thự Hoàng gia</h3>
        <p><span style="font-weight:bold">Địa chỉ:</span> Thành phố Hồ Chí Minh</p>
        <p><span style="font-weight:bold">Loại gạch:</span> Đá trang trí đá núi lửa</p>
        <p><span style="font-weight:bold">Thời gian thi công:</span> 1 tuần</p>
      </div>
    </div>
  </div>
  <div class="project-footer">
    <button class="other-projects-btn" onclick="window.location.href='duan.php'">Các dự án khác</button>
  </div>
</section>

<section class="news-section">
  <h2 class="news-title">TIN TỨC</h2>
  <div class="news-list">
    <div class="news-item">
      <img src="../img/tintuc1.jpg" alt="Catalog gạch ốp lát nhà tắm">
      <h3>Catalog gạch ốp lát nhà tắm</h3>
      <p>Catalog gạch ốp lát nhà tắm Trường Phát Ceramics giới thiệu bộ sưu tập Catalog gạch ốp lát nhà tắm...</p>
      <a href="chitiettin1.php" class="news-btn">ĐỌC TIẾP</a>
    </div>
    <div class="news-item">
      <img src="../img/tintuc2.jpg" alt="Catalog gạch lát nền online">
      <h3>Catalog gạch lát nền online</h3>
      <p>Catalog gạch lát nền online Trường Phát Ceramics giới thiệu bộ sưu tập gạch lát nền...</p>
      <a href="chitiettin2.php" class="news-btn">ĐỌC TIẾP</a>
    </div>
    <div class="news-item">
      <img src="../img/tintuc3.jpg" alt="Quy trình mua và đặt hàng, bảo hành đổi trả">
      <h3>Quy trình mua và đặt hàng, bảo hành đổi trả</h3>
      <p>TẠI SAO NÊN MUA ĐÁ SÂN VƯỜN - GẠCH ỐP LÁT TẠI KHO ĐÁ SÂN VƯỜN BETTERLIFE...</p>
      <a href="chitiettin3.php" class="news-btn">ĐỌC TIẾP</a>
    </div>
  </div>
  <div class="news-list">
    <div class="news-item">
      <img src="../img/tintuc4.jpg" alt="Hoa lưu tô">
      <h3>Hoa lưu tô</h3>
      <p>Cây Hoa lưu tô (Chionanthus retusa) Cây này có tên khoa học là Chionanthus retusa...</p>
      <a href="chitiettin4.php" class="news-btn">ĐỌC TIẾP</a>
    </div>
    <div class="news-item">
      <img src="../img/tintuc5.jpg" alt="Đá trầm tích">
      <h3>Đá trầm tích</h3>
      <p>Đá trầm tích là một trong 3 dòng đá chính (gồm đá mắc-ma và đá biến chất)...</p>
      <a href="chitiettin5.php" class="news-btn">ĐỌC TIẾP</a>
    </div>
  </div>
</section>

<div id="include-footer">
<script>
    $(function () {
        $("#include-footer").load("footer.php");
    });
    </script>
</div>
</body>
</html>
