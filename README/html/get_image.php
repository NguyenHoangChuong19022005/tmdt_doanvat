<?php
require '../connect.php';
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "SELECT image FROM sanpham WHERE id = $id";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row['image']) {
            header('Content-Type: image/jpeg');
            echo $row['image'];
        } else {
            header('Content-Type: image/jpeg');
            readfile('images/placeholder.jpg');
        }
    } else {
        header('Content-Type: image/jpeg');
        readfile('images/placeholder.jpg');
    }
} else {
    header('Content-Type: image/jpeg');
    readfile('images/placeholder.jpg');
}
exit;
?>