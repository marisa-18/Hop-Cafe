<?php 
include "db.php"; 
if(session_status() == PHP_SESSION_NONE) session_start();


// นับจำนวนสินค้าทั้งหมดในตะกร้า
$cartCount = 0;
if(isset($_SESSION['cart'])){
    foreach($_SESSION['cart'] as $c){
        $cartCount += $c['qty'];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>Hop Cafe</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; background:#f5f5f5; margin:0; padding:0; }
        header { background:#6a4caf; color:#fff; padding:15px; text-align:center; }
        header h1 { margin:0; font-size:28px; }
        nav a { color:#fff; text-decoration:none; margin:0 10px; font-weight:bold; }
        nav a:hover { text-decoration:underline; }

        h2 { text-align:center; color:#6a4caf; font-size:32px; margin:30px 0 20px 0; font-weight:bold; }

        .menu-list {
            display:grid;
            grid-template-columns: repeat(4, 1fr);
            gap:50px;
            max-width:1400px;
            margin:0 auto 50px auto;
            padding:0 20px;
        }

        .menu-item {
            background:#fff;
            padding:20px;
            border-radius:12px;
            text-align:center;
            box-shadow:0 6px 15px rgba(0,0,0,0.2);
            transition:0.3s;
            display:flex;
            flex-direction:column;
            align-items:center;
        }

        .menu-item:hover { transform: translateY(-5px); box-shadow:0 10px 20px rgba(0,0,0,0.25); }
        .menu-item img { width:180px; height:180px; object-fit:cover; border-radius:8px; margin-bottom:15px; }
        .menu-item b { display:block; font-size:18px; margin-bottom:10px; color:#333; }
        .menu-item .price { font-weight:bold; color:#6a4caf; margin-bottom:10px; font-size:16px; }
        .menu-item a {
            display:inline-block;
            padding:15px 25px;
            background:#6a4caf;
            color:#fff;
            border-radius:10px;
            text-decoration:none;
            transition:0.2s;
            font-weight:bold;
        }
        .menu-item a:hover { background:#5e4b8b; }

        @media(max-width:1200px){ .menu-list { grid-template-columns: repeat(3,1fr); } }
        @media(max-width:900px){ .menu-list { grid-template-columns: repeat(2,1fr); } }
        @media(max-width:600px){ .menu-list { grid-template-columns: 1fr; } }
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

<h2>เมนูขายดี</h2>
<div class="menu-list">
<?php
$menu = $conn->query("SELECT * FROM menu ORDER BY sold DESC LIMIT 4");
while($row = $menu->fetch_assoc()): ?>
    <div class="menu-item">
        <b><?=$row['name']?></b>
        <img src="assets/uploads/<?=$row['image']?>" alt="<?=$row['name']?>">
        <div class="price"><?=$row['price']?> บาท</div>
        <a href="menu.php?id=<?=$row['id']?>">สั่งซื้อ</a>
    </div>
<?php endwhile; ?>
</div>

</body>
</html>
