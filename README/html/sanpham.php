<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang sản phẩm</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { text-align:center }
    </style>
    <link href="../css/style.css" rel="stylesheet"/>
    <link href="../css/sanpham.css" rel="stylesheet"/>
    <script src="../jquery-3.7.1.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
<div id="container">
    <!-- Hiển thị topbar và header  -->
    <div id="include-header"></div>
    <script>
    $(function () {
        $("#include-header").load("header.php");
    });
    </script>

    <!-- Nội dung phần dự án -->
    <main>
    <script src="../js/sanpham.js"></script>
    <?php
    $link = mysqli_connect("localhost", "root", "", "doan_vat");
    mysqli_set_charset($link, "utf8");

    $loai_id = isset($_GET['loai_id']) ? (int)$_GET['loai_id'] : 0;
    $chungloai_id = isset($_GET['chungloai_id']) ? (int)$_GET['chungloai_id'] : 0;
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 20;
    $start = ($page - 1) * $limit;

    $hien_hot = ($loai_id == 0);
    echo "<div class='sanpham-container'>";

    // Menu
    echo "<div class='menu-hot-row'>";
    echo "<div class='menu-left'>";
    // Loại bỏ <div class='menu-title'>Danh mục sản phẩm</div>
    echo "<button id='toggleMenu' class='toggle-button'>☰ Danh mục sản phẩm</button>";
    echo "<div id='menu' class='menu-content'>";
    $res_loai = mysqli_query($link, "SELECT * FROM loai_sanpham");
    while ($loai = mysqli_fetch_assoc($res_loai)) {
        echo "<div class='loaisp-wrapper'>";
        echo "<div class='loaisp' data-loai-id='{$loai['loai_id']}'>{$loai['loai_name']}</div>";
        $sql_cl = "SELECT DISTINCT cls.chungloai_id, cls.loaidoan 
                   FROM chungloai_sanpham cls 
                   JOIN sanpham sp ON cls.chungloai_id = sp.chungloai_id 
                   WHERE sp.loai_id = {$loai['loai_id']}";
        $res_cl = mysqli_query($link, $sql_cl);
        echo "<div class='cloai' data-loai-id='{$loai['loai_id']}'>";
        while ($cl = mysqli_fetch_assoc($res_cl)) {
            echo "<a href='?loai_id={$loai['loai_id']}&chungloai_id={$cl['chungloai_id']}#duoi' data-chungloai-id='{$cl['chungloai_id']}'>{$cl['loaidoan']}</a>";
        }
        echo "</div>";
        echo "</div>";
    }
    // Add Sản phẩm HOT as a category with hot class
    echo "<div class='loaisp-wrapper'>";
    echo "<div class='loaisp hot' data-loai-id='0'>Sản phẩm HOT</div>";
    echo "<div class='cloai' data-loai-id='0'>";
    echo "<a href='sanpham.php#duoi' data-chungloai-id='0'>HOT</a>";
    echo "</div>";
    echo "</div>";
    echo "</div>"; // end #menu
    echo "</div>"; // end .menu-left

    // Breadcrumb
    $breadcrumb = '';
    if ($hien_hot) {
        $breadcrumb = ''; // No breadcrumb for Sản phẩm HOT
    } elseif ($loai_id > 0) {
        $res_loai_name = mysqli_query($link, "SELECT loai_name FROM loai_sanpham WHERE loai_id = $loai_id");
        if ($row = mysqli_fetch_assoc($res_loai_name)) {
            $breadcrumb = "Sản phẩm >> {$row['loai_name']}";
            if ($chungloai_id > 0) {
                $res_cl_name = mysqli_query($link, "SELECT loaidoan FROM chungloai_sanpham WHERE chungloai_id = $chungloai_id");
                if ($cl_row = mysqli_fetch_assoc($res_cl_name)) {
                    $breadcrumb .= " >> {$cl_row['loaidoan']}";
                }
            }
        }
    }
    echo "<div class='menu-right'>";
    echo "<div class='breadcrumb' id='breadcrumb-link' style='display: " . ($breadcrumb ? 'block' : 'none') . "'>$breadcrumb</div>";
    echo "</div>";

    echo "</div>"; // end .menu-hot-row

    // Hiển thị tên danh mục hoặc "SẢN PHẨM HOT" phía trên
    $current_category = '';
    if ($hien_hot) {
        $current_category = 'SẢN PHẨM HOT';
    } elseif ($loai_id > 0) {
        $res_loai_name = mysqli_query($link, "SELECT loai_name FROM loai_sanpham WHERE loai_id = $loai_id");
        if ($row = mysqli_fetch_assoc($res_loai_name)) {
            $current_category = $row['loai_name'];
            if ($chungloai_id > 0) {
                $res_cl_name = mysqli_query($link, "SELECT loaidoan FROM chungloai_sanpham WHERE chungloai_id = $chungloai_id");
                if ($cl_row = mysqli_fetch_assoc($res_cl_name)) {
                    $current_category .= ' ( ' . $cl_row['loaidoan'] .' ) ';
                }
            }
        }
    }
    echo "<div class='category-title' style='text-align: center; font-size: 24px; margin: 0px 0; '>$current_category</div>";

    // Nội dung chính
    echo "<div class='content' id='duoi'>";
    if ($hien_hot) {
        $where = "sp.id_sp_hot = 1";
    } else {
        $where = "sp.loai_id = $loai_id";
        if ($chungloai_id > 0) {
            $where .= " AND sp.chungloai_id = $chungloai_id";
        }
    }

    // Đếm tổng
    $count = mysqli_query($link, "SELECT COUNT(*) AS total 
        FROM sanpham sp 
        LEFT JOIN uudai ud ON sp.sanpham_id = ud.sanpham_id AND ud.trangthai_uudai = 1 
        WHERE $where");
    $total = mysqli_fetch_assoc($count)['total'];
    $total_pages = ceil($total / $limit);

    // Lấy sản phẩm
    $sql_sp = "SELECT sp.*, ud.phamtram_uudai, ud.giasau_uudai 
               FROM sanpham sp 
               LEFT JOIN uudai ud ON sp.sanpham_id = ud.sanpham_id AND ud.trangthai_uudai = 1 
               WHERE $where 
               LIMIT $start, $limit";
    $res_sp = mysqli_query($link, $sql_sp);

    echo "<div class='sp-grid'>";
    if (mysqli_num_rows($res_sp) > 0) {
        while ($sp = mysqli_fetch_assoc($res_sp)) {
            $img = base64_encode($sp['image']);
            $gia = $sp['gia'];
            $uudai = (float)$sp['phamtram_uudai'];
            $gia_km = ($uudai > 0 && $gia > 0) ? $gia - ($gia * $uudai / 100) : $gia;
            $id = $sp['sanpham_id'];
            $is_hot = ($sp['id_sp_hot'] == 1);

            echo "<div class='sp-box'>";

            // Badge khuyến mãi và HOT
            if ($uudai > 0) {
                echo "<div class='badge-ud'>-{$uudai}%</div>";
            }
            if ($is_hot) {
                echo "<div class='badge-hot'>HOT</div>";
            }

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
        echo "<p>Không có sản phẩm nào phù hợp.</p>";
    }
    echo "</div>";

    // Phân trang
    if ($total_pages > 1) {
        echo "<div class='pagination'>";
        $prev_page = $page > 1 ? $page - 1 : 1;
        $next_page = $page < $total_pages ? $page + 1 : $total_pages;
        $prev_link = "?page=$prev_page";
        $next_link = "?page=$next_page";
        if (!$hien_hot) {
            $prev_link = "?loai_id=$loai_id";
            $next_link = "?loai_id=$loai_id";
            if ($chungloai_id > 0) {
                $prev_link .= "&chungloai_id=$chungloai_id";
                $next_link .= "&chungloai_id=$chungloai_id";
            }
            $prev_link .= "&page=$prev_page";
            $next_link .= "&page=$next_page";
        }
        echo "<a href='{$prev_link}#duoi' class='pag-btn prev " . ($page == 1 ? 'disabled' : '') . "'><i class='fas fa-angle-left'></i></a>";
        for ($i = 1; $i <= $total_pages; $i++) {
            $active = ($i == $page) ? "active" : "";
            $link_page = "?page=$i";
            if (!$hien_hot) {
                $link_page = "?loai_id=$loai_id";
                if ($chungloai_id > 0) $link_page .= "&chungloai_id=$chungloai_id";
                $link_page .= "&page=$i";
            }
            echo "<a href='{$link_page}#duoi' class='pag-btn $active'>$i</a>";
        }
        echo "<a href='{$next_link}#duoi' class='pag-btn next " . ($page == $total_pages ? 'disabled' : '') . "'><i class='fas fa-angle-right'></i></a>";
        echo "</div>";
    }

    echo "</div></div>";
    mysqli_close($link);
    ?>
    </main>
</div>

 <!-- Hiển thị footer -->
    <div id="include-footer">
    <script>
        $(function () {
            $("#include-footer").load("footer.php");
        });
    </script>
    </div>
</body>
</html>