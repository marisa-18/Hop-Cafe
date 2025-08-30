<?php 
include "db.php"; 
if (session_status() == PHP_SESSION_NONE) session_start();

// ตรวจสอบ user session
$user = $_SESSION['user'] ?? null;
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ตะกร้าสินค้า - Hop Cafe</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
/* Container Card */
.container {
    max-width: 900px;
    margin: 20px auto 50px auto;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}
h2 { color:#5e4b8b; text-align:center; margin-bottom:20px; }
table { width:100%; border-collapse:collapse; }
th, td { padding:10px; border-bottom:1px solid #ccc; text-align:left; }
th { background:#6a4caf; color:white; }
td input { width:60px; padding:5px; text-align:center; border-radius:5px; border:1px solid #ccc; }
button { background:#6a4caf; color:white; border:none; padding:8px 16px; border-radius:8px; cursor:pointer; transition:0.2s; }
button:hover { background:#5e4b8b; }
.btn-delete { background:#e74c3c; }
.btn-delete:hover { background:#c0392b; }
.toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
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
#total { font-weight:bold; color:#5e4b8b; }
</style>
</head>
<body>

<header>
  <h1><i class="fa-solid fa-mug-hot"></i> Hop Cafe</h1>
  <nav>
    <a href="index.php"><i class="fa-solid fa-house"></i> หน้าร้าน</a>
    <a href="menu.php"><i class="fa-solid fa-list"></i> เมนูทั้งหมด</a>
    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> ตะกร้า<span id="cart-count"><?php if($cartCount>0) echo " ($cartCount)"; ?></span></a>
    <a href="orders.php"><i class="fa-solid fa-receipt"></i> คำสั่งซื้อ</a>
    <?php if($user): ?>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ (<?=htmlspecialchars($user['username'])?>)</a>
    <?php else: ?>
        <a href="login.php"><i class="fa-solid fa-right-to-bracket"></i> เข้าสู่ระบบ</a>
    <?php endif; ?>
  </nav>
</header>

<div class="container">
<h2><i class="fa-solid fa-cart-shopping"></i> ตะกร้าสินค้า</h2>

<div id="cart-content">
<?php if(empty($_SESSION['cart'])): ?>
    <p style="text-align:center; color:#5e4b8b;">ไม่มีสินค้าในตะกร้า</p>
<?php else: ?>
<table>
    <tr>
        <th>สินค้า</th>
        <th>ราคา</th>
        <th>จำนวน</th>
        <th>รวม</th>
        <th>จัดการ</th>
    </tr>
    <?php $total=0; foreach($_SESSION['cart'] as $k=>$c): ?>
    <tr data-key="<?=$k?>">
        <td>
            <?=htmlspecialchars($c['name'])?>
            <?php if(!empty($c['sweet']) || !empty($c['topping'])): ?>
                (<?=htmlspecialchars($c['sweet'] ?? '-')?>, <?=htmlspecialchars($c['topping'] ?? '-')?>)
            <?php endif; ?>
            <?php if(!empty($c['note'])): ?>
                <br><small><?=htmlspecialchars($c['note'])?></small>
            <?php endif; ?>
        </td>
        <td><?=number_format($c['price'],2)?> บาท</td>
        <td><input type="number" class="qty" min="1" value="<?=$c['qty']?>"></td>
        <td class="subtotal"><?=number_format($c['price']*$c['qty'],2)?> บาท</td>
        <td><button class="btn-delete">ลบ</button></td>
    </tr>
    <?php $total += $c['price']*$c['qty']; endforeach; ?>
    <tr>
        <td colspan="3" align="right"><b>รวมทั้งหมด</b></td>
        <td colspan="2" id="total"><b><?=number_format($total,2)?> บาท</b></td>
    </tr>
</table>
<br>
<div style="text-align:center;" id="checkout-section">
    <?php if($user): ?>
        <form action="checkout.php" method="post">
            <button type="submit" name="checkout"><i class="fa-solid fa-credit-card"></i> ชำระเงิน</button>
        </form>
    <?php else: ?>
        <p style="color:red;">กรุณาเข้าสู่ระบบก่อนชำระเงิน</p>
        <a href="login.php"><button><i class="fa-solid fa-right-to-bracket"></i> เข้าสู่ระบบ</button></a>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>
</div>

<div class="toast"></div>

<script>
$(document).ready(function(){

    function showToast(msg, type="success"){
        $(".toast").text(msg).removeClass("success error").addClass(type+" show");
        setTimeout(()=>{$(".toast").removeClass("show");},2000);
    }

    // ฟังก์ชัน format ตัวเลขให้สวยงาม
    function formatNumber(num){
        return parseFloat(num).toLocaleString('th-TH', {minimumFractionDigits:2, maximumFractionDigits:2});
    }

    // อัพเดตจำนวนสินค้า
    $(".qty").on("change", function(){
        var row = $(this).closest("tr");
        var key = row.data("key");
        var qty = Math.max(1, $(this).val()); 
        $(this).val(qty);
        $.post("update_cart.php", {key:key, qty:qty}, function(res){
            if(res.success){
                row.find(".subtotal").text(formatNumber(res.subtotal) + " บาท");
                $("#total").html("<b>" + formatNumber(res.total) + " บาท</b>");
                $("#cart-count").text(res.cartCount > 0 ? " ("+res.cartCount+")" : "");
                showToast("อัปเดตจำนวนสินค้าเรียบร้อย");
            } else {
                showToast("อัปเดตไม่สำเร็จ","error");
            }
        }, "json");
    });

    // ลบสินค้า
    $(".btn-delete").on("click", function(){
        var row = $(this).closest("tr");
        var key = row.data("key");
        $.post("update_cart.php", {key:key, remove:1}, function(res){
            if(res.success){
                row.remove();
                $("#total").html("<b>" + formatNumber(res.total) + " บาท</b>");
                $("#cart-count").text(res.cartCount > 0 ? " ("+res.cartCount+")" : "");
                showToast("ลบสินค้าเรียบร้อย");

                // ✅ ถ้าตะกร้าว่างให้แทนที่ด้วยข้อความ
                if(res.total == 0){
                    $("#cart-content").html('<p style="text-align:center; color:#5e4b8b;">ไม่มีสินค้าในตะกร้า</p>');
                }
            } else {
                showToast("ลบสินค้าไม่สำเร็จ","error");
            }
        }, "json");
    });

});
</script>
</body>
</html>
