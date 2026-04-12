$(document).ready(function () {
    // Xóa spinner khi trang load
    removeSpinner();

    // Xóa spinner khi trang hiển thị lại (từ lịch sử)
    $(window).on('pageshow', function(event) {
        if (event.originalEvent.persisted) {
            removeSpinner();
        }
    });

    // Ẩn cloai ban đầu
    $(".cloai").hide();

    // Hover để hiển thị menu
    $("#toggleMenu, #menu").mouseenter(function () {
        $("#menu").stop(true, true).slideDown(200);
    });

    // Rời khỏi menu thì ẩn menu
    $(".menu-left").mouseleave(function () {
        $("#menu").stop(true, true).slideUp(200);
        $(".cloai").slideUp(200);
        $(".loaisp").removeClass("active");
    });

    // Hover vào loai thì hiện cloai tương ứng
    $(".loaisp").mouseenter(function () {
        const $this = $(this);
        const $cloai = $this.next(".cloai");

        // Đóng cloai khác
        $(".cloai").slideUp(200);
        $(".loaisp").removeClass("active");

        // Mở cloai tương ứng nếu có
        if ($cloai.length) {
            $cloai.css({
                left: $this.outerWidth(),
                top: 0
            }).slideDown(200);
            $this.addClass("active");
        }

        // Cập nhật breadcrumb nếu không phải HOT
        const loaiName = $this.text();
        let breadcrumbText = "";
        if (loaiName !== "Sản phẩm HOT") {
            breadcrumbText = "Sản phẩm >> " + loaiName;
        }
        $("#breadcrumb-link").text(breadcrumbText).toggle(!!breadcrumbText);
    });

    // Click cloai để chuyển trang (lọc theo chủng loại) kèm spinner
    $(".cloai a").click(function (e) {
        e.preventDefault();
        const href = $(this).attr("href");
        showSpinner();
        setTimeout(() => {
            window.location.href = href;
        }, 500);
    });

    // Click chi tiết sản phẩm => chuyển trang với spinner
    $(".sp-box a").click(function (e) {
        e.preventDefault();
        const href = $(this).attr("href");
        showSpinner();
        setTimeout(() => {
            window.location.href = href;
        }, 500);
    });

    // Click phân trang => hiệu ứng spinner
    $('.pagination a.pag-btn').on('click', function (e) {
        if ($(this).hasClass('disabled')) {
            e.preventDefault();
            return;
        }

        e.preventDefault();
        const href = $(this).attr('href');
        showSpinner();
        setTimeout(() => {
            window.location.href = href;
        }, 500);
    });

    // Spinner overlay hiển thị
    function showSpinner() {
        if ($('.spinner-overlay').length === 0) {
            $('body').append('<div class="spinner-overlay"><div class="spinner"></div></div>');
        }
    }

    // Xóa spinner
    function removeSpinner() {
        $('.spinner-overlay').remove();
    }
});
