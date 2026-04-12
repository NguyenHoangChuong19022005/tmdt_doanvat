$(document).ready(function () {
    // Xóa spinner khi trang load
    removeSpinner();

    // Xóa spinner khi trang hiển thị (bao gồm khi quay lại từ lịch sử)
    $(window).on('pageshow', function(event) {
        if (event.originalEvent.persisted) {
            removeSpinner();
        }
    });

    // Ẩn cloai ban đầu
    $(".cloai").hide();

    // 1. Hover vào menu trái hoặc nút danh mục => hiện menu
    $("#toggleMenu, #menu").mouseenter(function () {
        $("#menu").stop(true, true).slideDown(200);
    });

    // 2. Rời chuột khỏi toàn bộ vùng menu => ẩn menu
    $(".menu-left").mouseleave(function () {
        $("#menu").stop(true, true).slideUp(200);
        $(".cloai").slideUp(200);
        $(".loaisp").removeClass("active");
    });

    // 3. Hover vào loaisp thì mở cloai tương ứng
    $(".loaisp").mouseenter(function () {
        const $this = $(this);
        const $cloai = $this.next(".cloai");

        // Đóng cloai khác
        $(".cloai").slideUp(200);
        $(".loaisp").removeClass("active");

        // Mở cloai tương ứng và điều chỉnh vị trí sát bên phải
        if ($cloai.length) {
            $cloai.css({
                left: $this.outerWidth(),
                top: 0
            }).slideDown(200);
            $this.addClass("active");
        }

        // Cập nhật breadcrumb
        const loaiName = $this.text();
        let breadcrumbText = "";
        if (loaiName !== "Sản phẩm HOT") {
            breadcrumbText = "Sản phẩm >> " + loaiName;
        }
        $("#breadcrumb-link").text(breadcrumbText).toggle(!!breadcrumbText);
    });

    // 4. Click cloai không làm ẩn menu, cập nhật breadcrumb với hiệu ứng spinner
    $(".cloai a").click(function (e) {
        e.preventDefault();
        const $this = $(this);
        const href = $this.attr("href");

        // Hiển thị spinner
        showSpinner();
        setTimeout(() => {
            window.location.href = href;
        }, 500); // Giả lập delay 500ms
    });

    // 5. Click vào chi tiết sản phẩm với hiệu ứng spinner
    $(".sp-box a").click(function (e) {
        e.preventDefault();
        const $this = $(this);
        const href = $this.attr("href");

        // Hiển thị spinner
        showSpinner();
        setTimeout(() => {
            window.location.href = href;
        }, 500); // Giả lập delay 500ms
    });

    // 6. Thêm hiệu ứng spinner cho phân trang
    $('.pagination a.pag-btn').on('click', function (e) {
        if ($(this).hasClass('disabled')) {
            e.preventDefault();
            return;
        }

        e.preventDefault();
        const $this = $(this);
        const href = $this.attr('href');

        // Hiển thị spinner
        showSpinner();
        setTimeout(() => {
            window.location.href = href;
        }, 500); // Giả lập delay 500ms
    });

    // Hàm hiển thị spinner
    function showSpinner() {
        if ($('.spinner-overlay').length === 0) {
            $('body').append('<div class="spinner-overlay"><div class="spinner"></div></div>');
        }
    }

    // Hàm xóa spinner
    function removeSpinner() {
        $('.spinner-overlay').remove();
    }
});

