<?php
include "db.php";
if(session_status() == PHP_SESSION_NONE) session_start();

// ตรวจสอบว่ามีผู้ใช้งานล็อกอิน
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

// ตรวจสอบว่ามีสินค้าในตะกร้า
if(empty($_SESSION['cart'])){
    header("Location: cart.php");
    exit;
}

$cart = $_SESSION['cart'];
$total = 0;
foreach($cart as $item){
    $total += $item['price'] * $item['qty'];
}

$toast_msg = '';
$toast_type = '';

// ประมวลผลชำระเงิน
if(isset($_POST['checkout'])){
    $user_id = intval($_SESSION['user']['id']); // ✅ บังคับเป็น int
    $payment_method = $_POST['payment_method'] ?? 'ไม่ระบุ';
    $items_json = json_encode($cart, JSON_UNESCAPED_UNICODE);

    // ตรวจสอบความยาว items_json เพื่อป้องกัน error
    if(strlen($items_json) > 65000){
        $toast_msg = "ข้อมูลสินค้าเยอะเกินไป กรุณาลองใหม่";
        $toast_type = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO orders (user_id, items, total, status, payment_method, created_at) VALUES (?, ?, ?, 'รอชำระ', ?, NOW())");
        if($stmt){
            $stmt->bind_param("isds", $user_id, $items_json, $total, $payment_method);
            if($stmt->execute()){
                unset($_SESSION['cart']); // ล้างตะกร้า
                $toast_msg = "สั่งซื้อเรียบร้อยแล้ว!";
                $toast_type = "success";
            } else {
                $toast_msg = "เกิดข้อผิดพลาด: " . $stmt->error;
                $toast_type = "error";
            }
            $stmt->close();
        } else {
            $toast_msg = "SQL Error: " . $conn->error;
            $toast_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ชำระเงิน - Hop Cafe</title>
<link rel="stylesheet" href="assets/style.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
.container { max-width:800px; margin:30px auto; background:#fff; padding:20px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.2); }
h2 { color:#5e4b8b; text-align:center; }
table { width:100%; border-collapse:collapse; margin-bottom:20px; }
th, td { padding:10px; border-bottom:1px solid #ccc; text-align:left; vertical-align: top; }
#total { font-weight:bold; color:#5e4b8b; text-align:right; }
button { background:#6a4caf; color:white; border:none; padding:10px 20px; border-radius:8px; cursor:pointer; transition:0.2s; }
button:hover { background:#5e4b8b; }
select { padding:5px 8px; border-radius:5px; margin-top:5px; }
.toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%) translateY(-20px);
    min-width: 250px;
    padding: 15px 20px;
    border-radius: 8px;
    color: #fff;
    font-size: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 1000;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.5s ease, transform 0.5s ease;
}
.toast.show { opacity:1; pointer-events:auto; transform: translateX(-50%) translateY(0); }
.toast.success { background:#28a745; }
.toast.error { background:#dc3545; }
</style>
</head>
<body>
<div class="container">
<h2>ตรวจสอบรายการสินค้าและชำระเงิน</h2>
<table>
<tr>
    <th>สินค้า</th>
    <th>ราคา</th>
    <th>จำนวน</th>
    <th>รวม</th>
</tr>
<?php foreach($cart as $item): 
    $subtotal = $item['price'] * $item['qty'];
?>
<tr>
    <td><?=$item['name']?><?php if(!empty($item['sweet']) || !empty($item['topping'])): ?>
        (<?=$item['sweet']?>, <?=$item['topping']?>)
    <?php endif; ?>
    <?php if(!empty($item['note'])): ?><br><small>โน้ต: <?=$item['note']?></small><?php endif; ?>
    </td>
    <td><?=number_format($item['price'],2)?> บาท</td>
    <td><?=$item['qty']?></td>
    <td><?=number_format($subtotal,2)?> บาท</td>
</tr>
<?php endforeach; ?>
<tr>
    <td colspan="3" id="total">รวมทั้งหมด</td>
    <td><?=number_format($total,2)?> บาท</td>
</tr>
</table>

<form method="post">
    <label>วิธีชำระเงิน</label><br>
    <select name="payment_method" required>
        <option value="">-- เลือกวิธีชำระ --</option>
        <option value="เงินสด">เงินสด</option>
        <option value="บัตรเครดิต">บัตรเครดิต</option>
        <option value="พร้อมเพย์">พร้อมเพย์</option>
    </select><br><br>
    <button type="submit" name="checkout">ชำระเงิน</button>
</form>
</div>

<div class="toast <?=$toast_type?>"><?=$toast_msg?></div>
<script>
$(document).ready(function(){
    <?php if($toast_msg != ''): ?>
        const toast = $(".toast");
        toast.addClass("show");
        setTimeout(()=>{toast.removeClass("show");},3000);
    <?php endif; ?>
});
</script>
</body>
</html>
