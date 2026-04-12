function changeQuantity(amount, inputId) {
    const input = document.getElementById(inputId);
    let value = parseInt(input.value);
    if (!isNaN(value)) {
        value += amount;
        if (value < 1) value = 1;
        input.value = value;
    }
}

$(document).ready(function() {
    // Kiểm tra jQuery đã được tải chưa
    if (typeof $ === 'undefined') {
        console.error('Lỗi: jQuery chưa được tải. Vui lòng kiểm tra tệp jQuery trong trang.');
        alert('Lỗi hệ thống: Không tải được jQuery. Vui lòng thử lại sau!');
        return;
    }

    // Xử lý nút Thêm vào giỏ hàng
    $('.btn-add-to-cart').click(function() {
        const productId = parseInt($(this).data('product-id'));
        const quantity = parseInt($('#quantity').val());
        const productName = $('.product-info h2').text().trim() || 'Sản phẩm không xác định';
        if (!productId || !quantity || productId <= 0 || quantity <= 0) {
            alert('Dữ liệu không hợp lệ! Product ID: ' + productId + ', Quantity: ' + quantity);
            console.log('Add to cart - Product ID:', productId, 'Quantity:', quantity, 'Name:', productName);
            return;
        }
        $.ajax({
            url: 'cart_api.php',
            method: 'POST',
            data: { action: 'add_to_cart', product_id: productId, quantity: quantity, name: productName },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Đã thêm vào giỏ hàng!');
                    $(document).trigger('cartUpdated');
                    if (window.updateCartCount) window.updateCartCount();
                } else {
                    alert('Lỗi: ' + response.message);
                    console.log('Add to cart error:', response);
                }
            },
            error: function(xhr, status, error) {
                alert('Lỗi kết nối. Vui lòng thử lại!');
                console.log('Add to cart AJAX error:', xhr.status, error, xhr.responseText);
            }
        });
    });

    // Xử lý nút Mua hàng
    $('.btn-buy').click(function() {
        const productId = parseInt($(this).data('product-id'));
        const quantity = parseInt($('#quantity').val());
        const productName = $('.product-info h2').text().trim() || 'Sản phẩm không xác định';
        
        // Kiểm tra dữ liệu
        if (!productId || !quantity || productId <= 0 || quantity <= 0) {
            alert('Dữ liệu không hợp lệ! Product ID: ' + productId + ', Quantity: ' + quantity);
            console.log('Buy - Product ID:', productId, 'Quantity:', quantity, 'Name:', productName);
            return;
        }

        console.log('Bắt đầu thêm sản phẩm vào giỏ hàng, ID:', productId);
        $.ajax({
            url: 'cart_api.php',
            method: 'POST',
            data: { action: 'add_to_cart', product_id: productId, quantity: quantity, name: productName },
            dataType: 'json',
            success: function(response) {
                console.log('Phản hồi từ server:', response);
                if (response && response.success) {
                    // Hiển thị hộp thoại xác nhận
                    const confirmMessage = `Bạn có chắc muốn mua hàng sản phẩm hiện tại + giỏ hàng?`;
                    if (confirm(confirmMessage)) {
                        console.log('Buy confirmed, redirecting to thanhtoan.php');
                        window.location.href = 'thanhtoan.php';
                    } else {
                        console.log('Buy cancelled, staying on current page');
                    }
                } else {
                    console.error('Lỗi từ server:', response ? response.message : 'Không nhận được dữ liệu phản hồi');
                    alert('Lỗi khi thêm sản phẩm: ' + (response ? response.message : 'Không nhận được phản hồi từ server'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Lỗi AJAX:', status, error, 'Phản hồi từ server:', xhr.responseText);
                alert('Lỗi kết nối đến server. Vui lòng kiểm tra và thử lại!');
            }
        });
    });

    // Cho phép kéo ảnh lớn khi zoom
    $('.zoom-container').on('mousemove', function(e) {
        const $img = $(this).find('img');
        const offset = $(this).offset();
        const x = e.pageX - offset.left;
        const y = e.pageY - offset.top;
        const width = $(this).width();
        const height = $(this).height();

        const xPercent = x / width * 100;
        const yPercent = y / height * 100;

        $img.css('transform-origin', `${xPercent}% ${yPercent}%`);
    });
});