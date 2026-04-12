<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Khuyến mãi sản phẩm</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/khuyenmai.css">
  <script src="../jquery-3.7.1.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
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
  <script src="../js/khuyenmai.js"></script>
  <?php
  $link = mysqli_connect("localhost", "root", "", "showroom_gach");
  mysqli_set_charset($link, "utf8");

  $loai_id = isset($_GET['loai_id']) ? (int)$_GET['loai_id'] : 0;
  $chungloai_id = isset($_GET['chungloai_id']) ? (int)$_GET['chungloai_id'] : 0;
  $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
  $limit = 20;
  $start = ($page - 1) * $limit;

  echo "<div class='sanpham-container'>";

  // Menu 
  echo "<div class='menu-hot-row'>";
  echo "<div class='menu-left'>";
  echo "<button id='toggleMenu' class='toggle-button'>☰ Danh mục sản phẩm khuyến mãi</button>";
  echo "<div id='menu' class='menu-content'>";

  $res_loai = mysqli_query($link, "SELECT * FROM loai_sanpham");
  while ($loai = mysqli_fetch_assoc($res_loai)) {
    echo "<div class='loaisp-wrapper'>";
    echo "<div class='loaisp' data-loai-id='{$loai['loai_id']}'>{$loai['loai_name']}</div>";
    $sql_cl = "SELECT DISTINCT cls.chungloai_id, cls.kichthuoc FROM chungloai_sanpham cls JOIN sanpham sp ON cls.chungloai_id = sp.chungloai_id WHERE sp.loai_id = {$loai['loai_id']}";
    $res_cl = mysqli_query($link, $sql_cl);
    echo "<div class='cloai' data-loai-id='{$loai['loai_id']}'>";
    while ($cl = mysqli_fetch_assoc($res_cl)) {
      echo "<a href='?loai_id={$loai['loai_id']}&chungloai_id={$cl['chungloai_id']}#duoi'>{$cl['kichthuoc']}</a>";
    }
    echo "</div></div>";
  }
  echo "</div></div>";

  echo "<div class='menu-right'>";
  $breadcrumb = '';
  $current_category = '';
  if ($loai_id > 0) {
    $res_loai_name = mysqli_query($link, "SELECT loai_name FROM loai_sanpham WHERE loai_id = $loai_id");
    if ($row = mysqli_fetch_assoc($res_loai_name)) {
      $current_category = $row['loai_name'];
      $breadcrumb = "Sản phẩm >> $current_category";
      if ($chungloai_id > 0) {
        $res_cl_name = mysqli_query($link, "SELECT kichthuoc FROM chungloai_sanpham WHERE chungloai_id = $chungloai_id");
        if ($cl_row = mysqli_fetch_assoc($res_cl_name)) {
          $breadcrumb .= " >> " . $cl_row['kichthuoc'];
          $current_category .= " ( " . $cl_row['kichthuoc'] . " )";
        }
      }
    }
  } else {
    $current_category = "TẤT CẢ SẢN PHẨM KHUYẾN MÃI";
  }
  echo "<div class='breadcrumb' id='breadcrumb-link' style='display: ".($breadcrumb ? 'block' : 'none').";'>$breadcrumb</div>";
  echo "</div></div>";

  // Tiêu đề danh mục
  echo "<div class='category-title' style='text-align: center; font-size: 24px; margin: 0px 0;'>";
  echo $current_category;
  echo "</div>";

  // Nội dung
  echo "<div class='content' id='duoi'>";
  $where = "uu.trangthai_uudai = 1";
  if ($loai_id > 0) $where .= " AND sp.loai_id = $loai_id";
  if ($chungloai_id > 0) $where .= " AND sp.chungloai_id = $chungloai_id";

  $total = mysqli_fetch_assoc(mysqli_query($link, "SELECT COUNT(*) AS total FROM sanpham sp JOIN uudai uu ON sp.sanpham_id = uu.sanpham_id WHERE $where"))['total'];
  $total_pages = ceil($total / $limit);

  $sql = "SELECT sp.*, uu.phamtram_uudai, uu.giasau_uudai FROM sanpham sp JOIN uudai uu ON sp.sanpham_id = uu.sanpham_id WHERE $where LIMIT $start, $limit";
  $res = mysqli_query($link, $sql);

  echo "<div class='sp-grid'>";
  if (mysqli_num_rows($res) > 0) {
    while ($sp = mysqli_fetch_assoc($res)) {
      $img = base64_encode($sp['image']);
      $gia = $sp['gia'];
      $uudai = (float)$sp['phamtram_uudai'];
      $gia_km = ($uudai > 0 && $gia > 0) ? $gia - ($gia * $uudai / 100) : $gia;
      $id = $sp['sanpham_id'];
      $is_hot = ($sp['id_sp_hot'] == 1);

      echo "<div class='sp-box'>";
      if ($uudai > 0) echo "<div class='badge-ud'>-{$uudai}%</div>";
      if ($is_hot) echo "<div class='badge-hot'>HOT</div>";

      echo "<a href='chitiet.php?id=$id'>";
      echo "<img class='sp-img' src='data:image/jpeg;base64,$img'>";
      echo "<div class='sp-name'>{$sp['ten_sanpham']}</div>";
      echo "</a>";
      echo "<div class='sp-code'>Mã: {$sp['ma_sp']}</div>";

      if ($uudai > 0 && $gia > 0) {
        echo "<div class='sp-old-price'><del>" . number_format($gia) . " đ</del></div>";
        echo "<div class='sp-price'>" . number_format($gia_km) . " đ</div>";
      } else {
        echo "<div class='sp-price'>" . ($gia > 0 ? number_format($gia) . " đ" : "Liên hệ") . "</div>";
      }

      echo "</div>";
    }
  } else {
    echo "<p>Không có sản phẩm khuyến mãi nào.</p>";
  }
  echo "</div>";

  // Phân trang
  if ($total_pages > 1) {
    echo "<div class='pagination'>";
    for ($i = 1; $i <= $total_pages; $i++) {
      $active = ($i == $page) ? "active" : "";
      $link_page = "?page=$i";
      if ($loai_id > 0) {
        $link_page = "?loai_id=$loai_id";
        if ($chungloai_id > 0) $link_page .= "&chungloai_id=$chungloai_id";
        $link_page .= "&page=$i";
      }
      echo "<a href='{$link_page}#duoi' class='pag-btn $active'>$i</a>";
    }
    echo "</div>";
  }

  echo "</div></div>";
  mysqli_close($link);
  ?>
  <script src="../js/khuyenmai.js"></script>
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
