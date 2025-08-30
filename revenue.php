<?php
// เชื่อมต่อฐานข้อมูล
include "../db.php"; // ปรับ path ให้ตรงกับตำแหน่งไฟล์ db.php

// กำหนดวันที่วันนี้
$today = date('Y-m-d');

// ดึงข้อมูลรายรับวันนี้
$res = $conn->query("SELECT SUM(total) as total_today FROM orders WHERE DATE(created_at)='$today'");
$row = $res->fetch_assoc();
$todayRevenue = $row['total_today'] ?? 0;
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>รายรับวันนี้ - Hop Cafe</title>
<link rel="stylesheet" href="../assets/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header>
  <h1>Admin Panel</h1>
  <nav>
    <a href="orders.php">จัดการคำสั่งซื้อ</a>
    <a href="menu_manage.php">จัดการเมนู</a>
    <a href="revenue.php">รายรับ</a>
    <a href="../logout.php">ออกจากระบบ</a>
  </nav>
</header>

<div class="container">
    <h2>รายรับวันนี้ (<?php echo $today; ?>)</h2>
    <p>รวมยอด: <b><?php echo number_format($todayRevenue, 2); ?> บาท</b></p>
</div>
</body>
</html>
