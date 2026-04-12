
$(document).ready(function() {
    // 1. Kiểm tra jQuery đã được tải chưa
    if (typeof $ === 'undefined') {
        console.error('Lỗi: jQuery chưa được tải. Vui lòng kiểm tra tệp jQuery.');
        return; // Dừng thực thi nếu jQuery không có
    }

    // Tải các sản phẩm trong giỏ hàng khi trang được tải hoặc khi có sự kiện cartUpdated
    loadCartItems();
    $(document).on('cartUpdated', loadCartItems);

    // 2. Xử lý checkbox "Chọn tất cả"
    $('#select-all').on('change', function() {
        $('.item-checkbox').prop('checked', $(this).is(':checked'));
        updateCartSummary(); // Cập nhật tổng tiền và số lượng khi chọn/bỏ chọn tất cả
    });

    // 3. Xử lý nút "Xóa đã chọn"
    $('#delete-selected').on('click', function() {
        const selectedItems = $('.item-checkbox:checked');
        if (selectedItems.length === 0) {
            console.warn('Vui lòng chọn sản phẩm cần xóa.');
            // Bạn có thể thêm một thông báo nhỏ trên UI thay vì alert nếu muốn
            return;
        }
        if (confirm('Bạn có chắc muốn xóa những sản phẩm đã chọn?')) {
            selectedItems.each(function() {
                const itemId = parseInt($(this).data('item-id'));
                removeItem(itemId); // Gọi hàm xóa từng mục
            });
        }
    });

    // 4. Xử lý nút "Thanh toán"
    $('#checkout-btn').on('click', function() {
        const selectedItems = $('.item-checkbox:checked');
        if (selectedItems.length === 0) {
            console.warn('Vui lòng chọn sản phẩm để thanh toán.');
            // Thêm thông báo trên UI nếu muốn
            return;
        }
        // Chuyển hướng đến trang thanh toán
        window.location.href = 'thanhtoan.php'; // Đảm bảo đường dẫn này đúng
    });

    // 5. Gắn sự kiện động cho các checkbox mục sản phẩm (sử dụng delegation)
    $(document).on('change', '.item-checkbox', function() {
        const itemId = parseInt($(this).data('item-id'));
        const isChecked = $(this).is(':checked');
        updateSelection(itemId, isChecked); // Gửi yêu cầu cập nhật trạng thái lựa chọn lên server
    });

    // 6. Gắn sự kiện động cho nút giảm số lượng
    $(document).on('click', '.qty-minus', function() {
        const itemId = parseInt($(this).data('item-id'));
        const input = $(`.qty-input[data-item-id="${itemId}"]`);
        let qty = parseInt(input.val());
        if (qty > 1) {
            updateQuantity(itemId, qty - 1);
        }
    });

    // 7. Gắn sự kiện động cho nút tăng số lượng
    $(document).on('click', '.qty-plus', function() {
        const itemId = parseInt($(this).data('item-id'));
        const input = $(`.qty-input[data-item-id="${itemId}"]`);
        let qty = parseInt(input.val());
        updateQuantity(itemId, qty + 1);
    });

    // 8. Gắn sự kiện động cho input số lượng (khi người dùng tự nhập)
    $(document).on('change', '.qty-input', function() {
        const itemId = parseInt($(this).data('item-id'));
        let qty = parseInt($(this).val());
        if (isNaN(qty) || qty < 1) {
            qty = 1; // Đảm bảo số lượng không nhỏ hơn 1
            $(this).val(1);
        }
        updateQuantity(itemId, qty);
    });

    // 9. Gắn sự kiện động cho nút xóa từng sản phẩm
    $(document).on('click', '.remove-item', function() {
        const itemId = parseInt($(this).data('item-id'));
        if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
            removeItem(itemId);
        }
    });

    // --- Các hàm AJAX và cập nhật UI ---

    // Hàm tải sản phẩm từ giỏ hàng (từ server)
    function loadCartItems() {
        $.ajax({
            url: 'cart_api.php', // Đường dẫn tới cart_api.php từ thư mục hiện tại (ví dụ: /cart/cart_api.php)
            method: 'POST',
            data: { action: 'get_cart_items' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    displayCartItems(response.items);
                    updateCartSummary();
                } else {
                    console.error('Lỗi khi tải giỏ hàng:', response.message || 'Không rõ lỗi.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Lỗi AJAX khi tải giỏ hàng:', status, error, 'Phản hồi:', xhr.responseText);
            }
        });
    }

    // Hàm hiển thị các mục sản phẩm trong giỏ hàng
    function displayCartItems(items) {
        const cartItemsBody = $('#cart-items');
        const emptyCartDiv = $('#empty-cart');
        const cartTable = $('.cart-table-container table');
        const cartFooter = $('.cart-footer');

        cartItemsBody.empty(); // Xóa tất cả các mục hiện có

        if (items.length === 0) {
            emptyCartDiv.show();
            cartTable.hide();
            cartFooter.hide();
        } else {
            emptyCartDiv.hide();
            cartTable.show();
            cartFooter.show();
            items.forEach(function(item) {
                const itemPrice = parseFloat(item.price) || 0;
                const itemTotal = itemPrice * (parseInt(item.quantity) || 0); // Đảm bảo quantity là số
                const row = `
                    <tr data-item-id="${item.id}">
                        <td><input type="checkbox" class="item-checkbox" data-item-id="${item.id}" ${item.selected ? 'checked' : ''}></td>
                        <td><img src="data:image/jpeg;base64,${item.image || ''}" alt="${item.name || 'Sản phẩm'}" class="product-image" onerror="this.src='../placeholder.jpg'"></td>
                        <td><div class="product-info"><h4>${item.name || 'Không rõ'}</h4><p class="product-code">Mã: ${item.ma_sp || 'N/A'}</p></div></td>
                        <td class="price">${formatPrice(itemPrice)} VNĐ</td>
                        <td>
                            <div class="quantity-controls">
                                <button class="qty-btn qty-minus" data-item-id="${item.id}">-</button>
                                <input type="number" class="qty-input" value="${item.quantity}" min="1" data-item-id="${item.id}">
                                <button class="qty-btn qty-plus" data-item-id="${item.id}">+</button>
                            </div>
                        </td>
                        <td class="total-price">${formatPrice(itemTotal)} VNĐ</td>
                        <td><button class="btn btn-danger btn-sm remove-item" data-item-id="${item.id}"><i class="fas fa-trash"></i></button></td>
                    </tr>`;
                cartItemsBody.append(row);
            });
        }
    }

    // Hàm cập nhật trạng thái chọn (selected) của sản phẩm trong session
    function updateSelection(itemId, selected) {
        $.ajax({
            url: 'cart_api.php', // Đường dẫn tới cart_api.php
            method: 'POST',
            data: { action: 'update_selection', item_id: itemId, selected: selected },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    console.log(`Cập nhật lựa chọn cho sản phẩm ${itemId}: ${selected}`);
                    updateCartSummary(); // Cập nhật tổng kết sau khi chọn/bỏ chọn
                } else {
                    console.error('Lỗi cập nhật lựa chọn:', response.message || 'Không rõ lỗi.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Lỗi AJAX cập nhật lựa chọn:', status, error, 'Phản hồi:', xhr.responseText);
            }
        });
    }

    // Hàm cập nhật số lượng sản phẩm trong giỏ hàng (trong session)
    function updateQuantity(itemId, quantity) {
        $.ajax({
            url: 'cart_api.php', // Đường dẫn tới cart_api.php
            method: 'POST',
            data: { action: 'update_quantity', item_id: itemId, quantity: quantity },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    console.log(`Số lượng sản phẩm ${itemId} được cập nhật thành ${quantity}.`);
                    loadCartItems(); // Tải lại giỏ hàng để cập nhật UI
                } else {
                    console.error('Lỗi cập nhật số lượng:', response.message || 'Không rõ lỗi.');
                    // Nếu lỗi, có thể đặt lại giá trị input về giá trị cũ
                    $(`.qty-input[data-item-id="${itemId}"]`).val(response.old_quantity || 1);
                }
            },
            error: function(xhr, status, error) {
                console.error('Lỗi AJAX cập nhật số lượng:', status, error, 'Phản hồi:', xhr.responseText);
            }
        });
    }

    // Hàm xóa sản phẩm khỏi giỏ hàng
    function removeItem(itemId) {
        $.ajax({
            url: 'cart_api.php', // Đường dẫn tới cart_api.php
            method: 'POST',
            data: { action: 'remove_item', item_id: itemId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    console.log(`Sản phẩm ${itemId} đã được xóa khỏi giỏ hàng.`);
                    loadCartItems(); // Tải lại giỏ hàng để cập nhật UI
                    if (window.updateCartCount) window.updateCartCount(); // Cập nhật icon giỏ hàng ở header
                } else {
                    console.error('Lỗi xóa sản phẩm:', response.message || 'Không rõ lỗi.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Lỗi AJAX xóa sản phẩm:', status, error, 'Phản hồi:', xhr.responseText);
            }
        });
    }

    // Hàm cập nhật tổng kết giỏ hàng (số lượng, tổng tiền)
    function updateCartSummary() {
        let totalItems = 0;
        let selectedCount = 0;
        let subtotal = 0;

        $('#cart-items tr').each(function() {
            const qty = parseInt($(this).find('.qty-input').val()) || 0;
            totalItems += qty;

            const isChecked = $(this).find('.item-checkbox').is(':checked');
            if (isChecked) {
                selectedCount += qty;
                const priceText = $(this).find('.price').text();
                // Loại bỏ "VNĐ" và dấu chấm phân cách hàng nghìn để parse thành số
                const price = parseFloat(priceText.replace(' VNĐ', '').replace(/\./g, '')) || 0;
                subtotal += price * qty;
            }
        });

        $('#total-items').text(totalItems);
        $('#selected-count').text(selectedCount);
        $('#subtotal').text(formatPrice(subtotal) + ' VNĐ');
        $('#total').text(formatPrice(subtotal) + ' VNĐ'); // Tổng cộng cũng là tạm tính cho các mục đã chọn

        // Bật/tắt nút thanh toán
        $('#checkout-btn').prop('disabled', selectedCount === 0);

        // Xử lý checkbox "select-all"
        const totalCheckboxes = $('.item-checkbox').length;
        const checkedCheckboxes = $('.item-checkbox:checked').length;

        $('#select-all').prop('indeterminate', checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes)
                        .prop('checked', checkedCheckboxes === totalCheckboxes && totalCheckboxes > 0);
    }

    // Hàm định dạng giá tiền (tách số hàng nghìn)
    function formatPrice(price) {
        return price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});