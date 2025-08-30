<?php
include "db.php"; 
if (session_status() == PHP_SESSION_NONE) session_start();

// ตรวจสอบ session
if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit;
}

// ดึง user_id จาก session
$user_id = $_SESSION['user']['id'];

// ดึงคำสั่งซื้อของผู้ใช้งาน
$orders = $conn->query("SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC");
if(!$orders){
    die("เกิดข้อผิดพลาดในการดึงคำสั่งซื้อ: " . $conn->error);
}

// จำนวนสินค้าตะกร้า
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>คำสั่งซื้อของฉัน - Hop Cafe</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
.container { max-width:900px; margin:20px auto; }
.order { background:#fff; padding:15px; border-radius:10px; box-shadow:0 4px 12px rgba(0,0,0,0.2); margin-bottom:15px; }
.order ul { list-style:none; padding-left:0; }
.order li { margin-bottom:5px; }
h2 { color:#5e4b8b; }
</style>
</head>
<body>
<header>
  <h1><i class="fa-solid fa-mug-hot"></i> Hop Cafe</h1>
  <nav>
    <a href="index.php"><i class="fa-solid fa-house"></i> หน้าร้าน</a>
    <a href="menu.php"><i class="fa-solid fa-list"></i> เมนูทั้งหมด</a>
    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> ตะกร้า<?php if($cartCount>0) echo " ($cartCount)"; ?></a>
    <a href="orders.php"><i class="fa-solid fa-receipt"></i> คำสั่งซื้อ</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ</a>
  </nav>
</header>

<div class="container">
<h2>คำสั่งซื้อของฉัน</h2>

<?php if($orders->num_rows==0): ?>
    <p>ยังไม่มีคำสั่งซื้อ</p>
<?php else: ?>
    <?php while($o = $orders->fetch_assoc()): ?>
    <div class="order">
        <b>Order #<?=$o['id']?></b> | วันที่ <?=$o['created_at']?> | 
        สถานะ: <?=$o['status']?><br>

        <ul>
        <?php
        // ดึงสินค้าในคำสั่งซื้อนี้
        $items = $conn->query("SELECT * FROM order_items WHERE order_id='".$o['id']."'");
        while($i = $items->fetch_assoc()): ?>
            <li>
                <?=$i['name']?> x<?=$i['qty']?> 
                (หวาน: <?=$i['sweet']?>, ท้อปปิ้ง: <?=$i['topping']?>)
                <?php if(!empty($i['note'])): ?> | โน้ต: <?=$i['note']?><?php endif; ?>
            </li>
        <?php endwhile; ?>
        </ul>

        <b>รวม: <?=$o['total']?> บาท</b>
    </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>
</body>
</html>
