<?php
session_start();
header('Content-Type: application/json');

if(!isset($_SESSION['cart'])) {
    echo json_encode(['success'=>false, 'message'=>'ไม่มีสินค้าในตะกร้า']);
    exit;
}

$cart = &$_SESSION['cart'];
$total = 0;

// รับค่าจาก AJAX
$key     = $_POST['key'] ?? null;
$qty     = isset($_POST['qty']) ? intval($_POST['qty']) : null;
$sweet   = $_POST['sweet'] ?? null;
$topping = $_POST['topping'] ?? null;
$note    = $_POST['note'] ?? null;
$remove  = isset($_POST['remove']) ? true : false;

if($key === null || !isset($cart[$key])){
    echo json_encode(['success'=>false, 'message'=>'สินค้าไม่ถูกต้อง']);
    exit;
}

$subtotal = 0;

if($remove){
    unset($cart[$key]);
} else {
    if($qty !== null && $qty > 0) $cart[$key]['qty'] = $qty;
    if($sweet   !== null) $cart[$key]['sweet']   = $sweet;
    if($topping !== null) $cart[$key]['topping'] = $topping;
    if($note    !== null) $cart[$key]['note']    = $note;

    // คำนวณ subtotal ของสินค้านี้
    $subtotal = $cart[$key]['price'] * $cart[$key]['qty'];
}

// คำนวณ total ใหม่
foreach($cart as $c){
    $total += $c['price'] * $c['qty'];
}

// ส่งกลับ JSON
echo json_encode([
    'success'  => true,
    'subtotal' => $subtotal,   // ✅ ถ้าเป็น remove จะเป็น 0
    'total'    => $total
]);
