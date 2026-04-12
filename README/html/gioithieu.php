<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Giới thiệu</title>
  <link rel="stylesheet" href="../css/style.css" type="text/css" />
  <link rel="stylesheet" href="../css/gioithieu.css" type="text/css" />
  <link rel="stylesheet" href="../css/header-footer.css" type="text/css" />
  <script src="../jquery-3.7.1.js"></script>
</head>
<body>
  <div id="container">
    
    <!-- Header -->
<div id="header-placeholder"></div>

    <!-- Nội dung giới thiệu -->
    <main class="gioi-thieu-noi-dung">
      <h2>Giới thiệu về BLife</h2>
      <p>
        BLife là đơn vị chuyên cung cấp các dòng gạch ốp lát chất lượng cao, đa dạng về mẫu mã, kiểu dáng và kích thước. Với định hướng hiện đại và sang trọng, chúng tôi luôn nỗ lực mang đến những sản phẩm tốt nhất, phù hợp với mọi không gian sống từ căn hộ, biệt thự đến công trình thương mại.
      </p>

      <h2>Sứ mệnh và giá trị</h2>
      <ul>
        <li><strong>Sứ mệnh:</strong> Là đối tác tin cậy trong lĩnh vực vật liệu xây dựng & trang trí nội thất.</li>
        <li><strong>Giá trị:</strong> Uy tín – Chất lượng – Tận tâm – Đổi mới sáng tạo.</li>
      </ul>

      <h2>Thế mạnh của chúng tôi</h2>
      <ul>
        <li>Kho hàng lớn, luôn có sẵn nhiều mẫu gạch thịnh hành</li>
        <li>Tư vấn miễn phí, hỗ trợ chọn mẫu theo thiết kế</li>
        <li>Chính sách chiết khấu hấp dẫn cho nhà thầu và công trình lớn</li>
        <li>Vận chuyển toàn quốc, hỗ trợ bốc xếp tận nơi</li>
      </ul>

      <h2>Cam kết dịch vụ</h2>
      <ul>
        <li>100% sản phẩm đúng chất lượng, rõ nguồn gốc</li>
        <li>Đổi trả trong 3 ngày nếu lỗi do sản xuất</li>
        <li>Miễn phí giao hàng nội thành với đơn từ 5 triệu VNĐ</li>
      </ul>
    </main>

    <!-- Sản phẩm ngẫu nhiên -->
    <section class="product-section">
      <h2>Sản phẩm tiêu biểu</h2>
      <div id="random-products" class="product-list"></div>
    </section>

    <!-- Footer -->
    <div id="footer-placeholder"></div>

     <!--Script random sản phẩm -->
    <script>
      const sanPhamList = [
        { img: "../img/imgduan/duan5.jpeg", ten: "Gạch men cao cấp"},
        { img: "../img/banner4.jpg", ten: "Gạch bông gió"},
        { img: "../img/banner2.jpg", ten: "Gạch ceramic "},
        { img: "../img/imgduan/duan3.webp", ten: "Gạch vân đá"},
        { img: "../img/gachlatsan.jpg", ten: "Gạch lát sàn"}
      ];

      function shuffleArray(array) {
        for (let i = array.length - 1; i > 0; i--) {
          const j = Math.floor(Math.random() * (i + 1));
          [array[i], array[j]] = [array[j], array[i]];
        }
        return array;
      }

      const randoms = shuffleArray(sanPhamList).slice(0, 3);
      const box = document.getElementById("random-products");

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
