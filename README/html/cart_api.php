<?php
session_start();
header('Content-Type: application/json');
ob_start();
include("../connect.php");

if (!$conn) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Lỗi kết nối database']);
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if (!isset($_POST['action'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Thiếu tham số action']);
    exit;
}

$action = $_POST['action'];
switch ($action) {
    case 'add_to_cart':
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);
        $name = $_POST['name'] ?? '';
        if ($product_id <= 0 || $quantity <= 0 || empty($name)) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            break;
        }
        $stmt = $conn->prepare("SELECT sp.gia, sp.ten_sanpham, sp.ma_sp, sp.image, ud.phamtram_uudai, ud.giasau_uudai
                                FROM sanpham sp
                                LEFT JOIN uudai ud ON sp.sanpham_id = ud.sanpham_id AND ud.trangthai_uudai = 1
                                WHERE sp.sanpham_id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $gia = (float)$row['gia'];
            $uudai = (float)$row['phamtram_uudai'];
            $gia_sau_uudai = (float)$row['giasau_uudai'];
            $price = ($gia > 0) ? ($gia_sau_uudai > 0 ? $gia_sau_uudai : ($uudai > 0 ? $gia - ($gia * $uudai / 100) : $gia)) : $gia;
            $found = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $product_id) {
                    $item['quantity'] += $quantity;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $_SESSION['cart'][] = [
                    'id' => $product_id,
                    'name' => $name,
                    'price' => $price,
                    'quantity' => $quantity,
                    'ma_sp' => $row['ma_sp'],
                    'image' => $row['image'] ? base64_encode($row['image']) : '',
                    'selected' => true
                ];
            }
            ob_end_clean();
            echo json_encode(['success' => true, 'message' => 'Đã thêm vào giỏ hàng']);
        } else {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại']);
        }
        $stmt->close();
        break;

    case 'get_cart_count':
        $count = 0;
        foreach ($_SESSION['cart'] as $item) {
            $count += (int)$item['quantity'];
        }
        ob_end_clean();
        echo json_encode(['success' => true, 'count' => $count]);
        break;

    case 'get_cart_items':
        $items = [];
        foreach ($_SESSION['cart'] as $item) {
            $items[] = [
                'id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'ma_sp' => $item['ma_sp'],
                'image' => $item['image'],
                'selected' => $item['selected']
            ];
        }
        ob_end_clean();
        echo json_encode(['success' => true, 'items' => $items]);
        break;

    case 'update_quantity':
        $item_id = (int)($_POST['item_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        if ($item_id <= 0 || $quantity <= 0) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            break;
        }
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $item_id) {
                $item['quantity'] = $quantity;
                $found = true;
                break;
            }
        }
        ob_end_clean();
        echo json_encode(['success' => $found, 'message' => $found ? 'Cập nhật số lượng thành công' : 'Không tìm thấy sản phẩm']);
        break;

    case 'remove_item':
        $item_id = (int)($_POST['item_id'] ?? 0);
        if ($item_id <= 0) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            break;
        }
        $new_cart = [];
        $found = false;
        foreach ($_SESSION['cart'] as $item) {
            if ($item['id'] != $item_id) {
                $new_cart[] = $item;
            } else {
                $found = true;
            }
        }
        $_SESSION['cart'] = $new_cart;
        ob_end_clean();
        echo json_encode(['success' => $found, 'message' => $found ? 'Xóa sản phẩm thành công' : 'Không tìm thấy sản phẩm']);
        break;

    case 'update_selection':
        $item_id = (int)($_POST['item_id'] ?? 0);
        $selected = $_POST['selected'] === 'true';
        if ($item_id <= 0) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            break;
        }
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['id'] == $item_id) {
                $item['selected'] = $selected;
                $found = true;
                break;
            }
        }
        ob_end_clean();
        echo json_encode(['success' => $found, 'message' => $found ? 'Cập nhật lựa chọn thành công' : 'Không tìm thấy sản phẩm']);
        break;

    default:
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Action không hợp lệ']);
}
$conn->close();
ob_end_flush();
?>