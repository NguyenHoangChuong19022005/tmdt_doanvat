<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giới thiệu</title>
  <link rel="stylesheet" href="../css/style.css" type="text/css" />
  <link rel="stylesheet" href="../css/gioithieu.css?v=1.1" type="text/css" />
  <link rel="stylesheet" href="../css/header-footer.css" type="text/css" />
  <script src="../jquery-3.7.1.js"></script>
</head>
<body>
  <div id="container">
    
    <!-- Header -->
<div id="header-placeholder"></div>

    <!-- Nội dung giới thiệu -->
    <main class="gioi-thieu-noi-dung">
      <section class="about-section">
        <h2>Giới thiệu về YUMMY</h2>
        <p>
          YUMMY là thiên đường của những tín đồ đam mê ẩm thực đường phố và đồ ăn vặt. Với đa dạng các loại bánh kẹo, đồ khô, mứt và nước uống giải khát, chúng tôi cam kết mang đến cho bạn những trải nghiệm tuyệt vời nhất với hương vị đậm đà, an toàn vệ sinh thực phẩm và giá cả hợp lý.
        </p>

        <h2>Sứ mệnh và giá trị</h2>
        <ul>
          <li><strong>Sứ mệnh:</strong> Cung cấp đồ ăn vặt ngon, sạch, rẻ đến tay mọi khách hàng.</li>
          <li><strong>Giá trị:</strong> An toàn vệ sinh – Đa dạng món ngon – Giao hàng siêu tốc.</li>
        </ul>

        <h2>Cam kết dịch vụ</h2>
        <ul>
          <li>100% sản phẩm đúng chất lượng, rõ nguồn gốc</li>
          <li>Đổi trả trong 3 ngày nếu lỗi do nhà sản xuất</li>
          <li>Miễn phí giao hàng nội thành với đơn từ 200.000 VNĐ</li>
        </ul>
      </section>
    </main>

    <!-- Sản phẩm ngẫu nhiên -->
    <section class="product-section">
      <h2>Sản phẩm tiêu biểu</h2>
      <div id="random-products" class="product-list"></div>
    </section>

    <!-- Footer -->
    <div id="footer-placeholder"></div>

     <!--Script random sản phẩm từ database -->
    <?php
      include 'database.php';
      mysqli_set_charset($conn, "utf8");
      
      $sanPhamList = [];
      $sql = "SELECT sanpham_id, ten_sanpham, gia, image FROM sanpham ORDER BY RAND() LIMIT 10";
      $result = mysqli_query($conn, $sql);
      if ($result && mysqli_num_rows($result) > 0) {
          while ($row = mysqli_fetch_assoc($result)) {
              $imgBase64 = base64_encode($row['image']);
              $sanPhamList[] = [
                  'img' => 'data:image/jpeg;base64,' . $imgBase64,
                  'ten' => $row['ten_sanpham'],
                  'gia' => number_format($row['gia'], 0, ',', '.') . 'đ',
                  'link' => 'chitiet.php?id=' . $row['sanpham_id']
              ];
          }
      }
      mysqli_close($conn);
    ?>
    <script>
      const sanPhamList = <?php echo json_encode($sanPhamList); ?>;

      function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
      }

      const randoms = sanPhamList.length > 0 ? shuffleArray(sanPhamList).slice(0, 3) : [];
      const box = document.getElementById("random-products");

      if (randoms.length > 0) {
        randoms.forEach(sp => {
          box.innerHTML += `
            <div class="product-item">
              <a href="${sp.link}">
                <img src="${sp.img}" alt="${sp.ten}">
              </a>
              <p>${sp.ten}</p>
              <p>${sp.gia}</p>
            </div>
          `;
        });
      } else {
        box.innerHTML = '<p>Chưa có sản phẩm nào.</p>';
      }
    </script>

  </div>
  <script>
    // chèn header
  fetch('header.php')
    .then(response => response.text())
    .then(data => {
      document.getElementById('header-placeholder').innerHTML = data;
    });
    // Chèn Footer
  fetch('footer.php')
    .then(res => res.text())
    .then(data => {
      document.getElementById('footer-placeholder').innerHTML = data;
    });
</script>
</body>
</html>
