<?php
$link = mysqli_connect("localhost", "root", "", "doan_vat");
mysqli_set_charset($link, "utf8");

$q = $_GET['q'] ?? '';
$q = trim($q);

// tách từ khóa
$words = explode(" ", $q);

$where = [];
foreach ($words as $w) {
    $w = mysqli_real_escape_string($link, $w);
    $where[] = "(ten_sanpham LIKE '%$w%' OR ma_sp LIKE '%$w%')";
}

$sql_where = implode(" AND ", $where);

$sql = "SELECT ten_sanpham, ma_sp 
        FROM sanpham 
        WHERE $sql_where 
        LIMIT 10";

$res = mysqli_query($link, $sql);

while ($row = mysqli_fetch_assoc($res)) {
    echo "<div class='suggest-item'>{$row['ma_sp']} - {$row['ten_sanpham']}</div>";
}
?>