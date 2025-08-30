<?php
include "db.php";
if (session_status() == PHP_SESSION_NONE) session_start();

// นับจำนวนสินค้าทั้งหมดในตะกร้า
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $c) $cartCount += $c['qty'];
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เมนูทั้งหมด - Hop Cafe</title>
<link rel="stylesheet" href="assets/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<style>
/* โครงสร้างเมนู */
.menu-list { display:flex; flex-wrap:wrap; gap:20px; justify-content:center; }
.menu-item { background:#fff; padding:15px; border-radius:10px; width:200px; text-align:center; box-shadow:0 4px 12px rgba(0,0,0,0.2); display:flex; flex-direction:column; align-items:center; }
.menu-item img { width:120px; border-radius:8px; margin:8px 0; }
.menu-item form { margin-top:10px; width:100%; display:flex; flex-direction:column; align-items:center; gap:5px; }

/* ชื่อเมนูเด่น ด้านบน */
.menu-item .menu-name {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    gap:5px;
    padding:5px 10px;
    border:2px solid #6a4caf;
    border-radius:8px;
    font-weight:bold;
    color:#6a4caf;
    background:#f9f7fd;
    font-size:14px;
}

/* ราคาด้านล่างภาพ */
.menu-item .price {
    font-weight:bold;
    color:#6a4caf;
    margin-top:5px;
    font-size:16px;
}

/* ปุ่มจำนวนสินค้า */
.qty-group { display:flex; align-items:center; gap:5px; justify-content:center; margin-bottom:5px; }
.qty-group button {
    width:36px;
    height:36px;
    border:none;
    border-radius:6px;
    background:#6a4caf;
    color:#fff;
    font-weight:bold;
    font-size:18px;
    cursor:pointer;
    transition:0.2s;
}
.qty-group button:hover { background:#5e4b8b; }
.qty-group input {
    width:50px;
    height:36px;
    text-align:center;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:16px;
    font-weight:bold;
}

/* ปุ่มเพิ่มสินค้า */
.menu-item button[type="submit"] { background:#6a4caf; color:white; border:none; padding:8px 12px; border-radius:6px; cursor:pointer; transition:0.2s; }
.menu-item button[type="submit"]:hover { background:#5e4b8b; }

/* Badge ตะกร้า */
nav a .badge {
    background:#dc3545;
    color:#fff;
    font-size:12px;
    padding:2px 6px;
    border-radius:50%;
    margin-left:5px;
    display:inline-block;
}

/* Toast */
#toast {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%) translateY(-50px);
    background: #28a745;
    color: #fff;
    padding: 12px 25px;
    border-radius: 12px;
    font-size: 14px;
    font-weight: 500;
    box-shadow: 0 4px 15px rgba(0,0,0,0.25);
    z-index: 9999;
    opacity: 0;
    pointer-events: none;
    transition: all 0.5s ease;
}
#toast.show { transform: translateX(-50%) translateY(0); opacity: 1; }
#toast.success { background: #28a745; }
#toast.error { background: #dc3545; }
</style>
</head>
<body>

<header>
  <h1><i class="fa-solid fa-mug-hot"></i> Hop Cafe</h1>
  <nav>
    <a href="index.php"><i class="fa-solid fa-house"></i> หน้าร้าน</a>
    <a href="menu.php"><i class="fa-solid fa-list"></i> เมนูทั้งหมด</a>
    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> ตะกร้า <span class="badge"><?= $cartCount ?></span></a>
    <a href="orders.php"><i class="fa-solid fa-receipt"></i> คำสั่งซื้อ</a>
    <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ</a>
  </nav>
</header>

<div class="container">
<h2>เมนูทั้งหมด</h2>
<div class="menu-list">
<?php
$menu = $conn->query("SELECT * FROM menu ORDER BY id DESC");
while($row = $menu->fetch_assoc()): ?>
    <div class="menu-item">
        <div class="menu-name"><?=$row['name']?></div>
        <img src="assets/uploads/<?=$row['image']?>" alt="<?=$row['name']?>">
        <div class="price"><?=$row['price']?> บาท</div>
        
        <form class="add-to-cart" data-id="<?=$row['id']?>">
    <input type="hidden" name="id" value="<?=$row['id']?>">
    <div class="qty-group">
        <button type="button" class="qty-btn" data-action="minus">-</button>
        <input type="number" name="qty" value="1" min="1">
        <button type="button" class="qty-btn" data-action="plus">+</button>
    </div>

    <select name="sweet">
        <option value="ปกติ">ปกติ</option>
        <option value="หวานน้อย">หวานน้อย</option>
        <option value="หวานมาก">หวานมาก</option>
    </select>
    <select name="topping">
        <option value="ไม่มี">ไม่มีท้อปปิ้ง</option>
        <option value="ไข่มุก">ไข่มุก</option>
        <option value="พุดดิ้ง">พุดดิ้ง</option>
    </select>
    <input type="text" name="note" placeholder="โน้ตเพิ่มเติม">

    <button type="submit">เพิ่มตะกร้า</button>
</form>

    </div>
<?php endwhile; ?>
</div>
</div>

<div id="toast"></div>

<script>
function showToast(msg, type="success"){
    const toast = $("#toast");
    toast.text(msg)
         .removeClass("success error")
         .addClass(type + " show");
    setTimeout(() => { toast.removeClass("show"); }, 2500);
}

// ปุ่ม + / -
$(document).on("click", ".qty-btn", function(){
    const action = $(this).data("action");
    const input = $(this).siblings('input[name="qty"]');
    let val = parseInt(input.val());
    if(action === "plus") val++;
    if(action === "minus" && val > 1) val--;
    input.val(val);
});

// เพิ่มสินค้าในตะกร้า
$(document).on("submit", ".add-to-cart", function(e){
    e.preventDefault();
    const form = $(this);
    $.ajax({
        url: "add_to_cart.php",
        type: "POST",
        data: form.serialize(),
        dataType: "json",
        success: function(res){
            $(".badge").text(res.cartCount).show();
            showToast(`✅ เพิ่ม ${res.name} x${res.qty} ลงตะกร้าแล้ว`, "success");
        },
        error: function(){
            showToast("❌ เกิดข้อผิดพลาด ลองใหม่อีกครั้ง", "error");
        }
    });
});
</script>
</body>
</html>
