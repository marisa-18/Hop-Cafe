<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // เริ่ม session หากยังไม่เริ่ม
}
?>
<header>
  <h1><i class="fa-solid fa-mug-hot"></i> Hop Cafe</h1>
  <nav>
    <a href="index.php"><i class="fa-solid fa-house"></i> หน้าร้าน</a>
    <a href="menu.php"><i class="fa-solid fa-list"></i> เมนูทั้งหมด</a>
    <a href="cart.php"><i class="fa-solid fa-cart-shopping"></i> ตะกร้า</a>
    <a href="orders.php"><i class="fa-solid fa-receipt"></i> คำสั่งซื้อ</a>

    <?php if(isset($_SESSION['user'])): ?>
        <?php if($_SESSION['user']['role'] == 'admin'): ?>
            <a href="admin/index.php"><i class="fa-solid fa-user-shield"></i> แอดมิน</a>
        <?php endif; ?>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> ออกจากระบบ</a>
        <span>สวัสดี, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
    <?php endif; ?>
  </nav>
</header>
