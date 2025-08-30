<?php
include "db.php";
if (session_status() == PHP_SESSION_NONE) session_start();

// ตรวจสอบข้อมูลที่ส่งมา
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $qty = intval($_POST['qty'] ?? 1);
    $sweet = $_POST['sweet'] ?? "ปกติ";
    $topping = $_POST['topping'] ?? "ไม่มี";
    $note = $_POST['note'] ?? "";

    $menu_item = $conn->query("SELECT * FROM menu WHERE id=$id");
    if ($menu_item && $menu_item->num_rows > 0) {
        $row = $menu_item->fetch_assoc();

        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        // ถ้าในตะกร้ามีแล้วให้บวกจำนวน
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += $qty;
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'price' => $row['price'],
                'image' => $row['image'],
                'qty' => $qty,
                'sweet' => $sweet,
                'topping' => $topping,
                'note' => $note
            ];
        }

        // นับจำนวนรวมในตะกร้า
        $cartCount = 0;
        foreach ($_SESSION['cart'] as $c) $cartCount += $c['qty'];

        echo json_encode([
            "status" => "success",
            "cartCount" => $cartCount,
            "name" => $row['name'],
            "qty" => $qty
        ]);
    } else {
        echo json_encode(["status" => "error", "msg" => "ไม่พบสินค้า"]);
    }
} else {
    echo json_encode(["status" => "error", "msg" => "ไม่รองรับการเรียกใช้งาน"]);
}
