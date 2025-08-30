<?php
include "../db.php";
if (session_status() == PHP_SESSION_NONE) session_start();

// ตรวจสอบสิทธิ์ admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// อัปเดตสถานะคำสั่งซื้อ
if(isset($_POST['update_status'])){
    $order_id = intval($_POST['order_id']);
    $status = $conn->real_escape_string($_POST['status']);
    $conn->query("UPDATE orders SET status='$status' WHERE id='$order_id'");
}

// ลบคำสั่งซื้อ
if(isset($_POST['delete_order'])){
    $order_id = intval($_POST['order_id']);
    $conn->query("DELETE FROM orders WHERE id='$order_id'");
}

// ค้นหาคำสั่งซื้อ
$search_user = $_GET['user'] ?? '';
$search_status = $_GET['status'] ?? '';

$query = "SELECT orders.*, users.username 
          FROM orders 
          LEFT JOIN users ON orders.user_id = users.id 
          WHERE 1=1";
if($search_user) $query .= " AND users.username LIKE '%".$conn->real_escape_string($search_user)."%' ";
if($search_status) $query .= " AND orders.status='".$conn->real_escape_string($search_status)."' ";
$query .= " ORDER BY orders.id DESC";

$orders = $conn->query($query);
if(!$orders){
    die("เกิดข้อผิดพลาดในการดึงคำสั่งซื้อ: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการคำสั่งซื้อ - Admin</title>
<link rel="stylesheet" href="../assets/style.css">
<style>
.container { max-width:1200px; margin:20px auto; }
h2 { color:#5e4b8b; text-align:center; margin-bottom:20px; }
table { width:100%; border-collapse:collapse; }
th, td { border:1px solid #ccc; padding:10px; text-align:left; vertical-align: top; }
th { background:#f4f4f4; }
.status { padding:3px 8px; border-radius:5px; color:#fff; font-weight:bold; }
.status.pending { background:#ffc107; }
.status.processing { background:#17a2b8; }
.status.completed { background:#28a745; }
form.inline { display:inline; }
select, input[type=text] { padding:5px 8px; border-radius:5px; margin-right:5px; }
button { background:#6a4caf; color:#fff; border:none; padding:5px 10px; border-radius:5px; cursor:pointer; margin:2px 0; }
button:hover { background:#5e4b8b; }
hr { border:none; border-top:1px solid #ccc; margin:5px 0; }
.items-list { font-size:14px; line-height:1.4; }
</style>
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
<h2>จัดการคำสั่งซื้อ</h2>

<form method="get" style="margin-bottom:15px;">
    <input type="text" name="user" placeholder="ค้นหาผู้สั่ง" value="<?=htmlspecialchars($search_user)?>">
    <select name="status">
        <option value="">ทั้งหมด</option>
        <option value="รอชำระ" <?=$search_status=='รอชำระ'?'selected':''?>>รอชำระ</option>
        <option value="กำลังทำ" <?=$search_status=='กำลังทำ'?'selected':''?>>กำลังทำ</option>
        <option value="เสร็จแล้ว" <?=$search_status=='เสร็จแล้ว'?'selected':''?>>เสร็จแล้ว</option>
    </select>
    <button type="submit">ค้นหา</button>
</form>

<table>
<tr>
    <th>ID</th>
    <th>ผู้สั่ง</th>
    <th>รายการสินค้า</th>
    <th>รวมราคา</th>
    <th>สถานะ</th>
    <th>จัดการ</th>
</tr>

<?php while($order = $orders->fetch_assoc()):
    $username = $order['username'] ?? 'Guest';
    $items = json_decode($order['items'], true);
    
    // กำหนดคลาสสถานะ
    $status_class = '';
    if($order['status']=='รอชำระ') $status_class='pending';
    elseif($order['status']=='กำลังทำ') $status_class='processing';
    elseif($order['status']=='เสร็จแล้ว') $status_class='completed';
?>
<tr>
    <td><?=$order['id']?></td>
    <td><?=htmlspecialchars($username)?></td>
    <td class="items-list">
        <?php if($items): foreach($items as $i): ?>
            <b><?=$i['name']?></b> x<?=$i['qty']?> (<?=$i['sweet']?>, <?=$i['topping']?>)
            <?php if(!empty($i['note'])): ?><br>โน้ต: <?=$i['note']?><?php endif; ?>
            <hr>
        <?php endforeach; else: ?>
            <i>ไม่มีข้อมูลสินค้า</i>
        <?php endif; ?>
    </td>
    <td><?=$order['total']?> บาท</td>
    <td><span class="status <?=$status_class?>"><?=$order['status']?></span></td>
    <td>
        <form method="post" class="inline">
            <input type="hidden" name="order_id" value="<?=$order['id']?>">
            <select name="status">
                <option value="รอชำระ" <?=$order['status']=='รอชำระ'?'selected':''?>>รอชำระ</option>
                <option value="กำลังทำ" <?=$order['status']=='กำลังทำ'?'selected':''?>>กำลังทำ</option>
                <option value="เสร็จแล้ว" <?=$order['status']=='เสร็จแล้ว'?'selected':''?>>เสร็จแล้ว</option>
            </select>
            <button type="submit" name="update_status">อัปเดต</button>
        </form>
        <form method="post" class="inline" onsubmit="return confirm('คุณต้องการลบคำสั่งซื้อนี้?');">
            <input type="hidden" name="order_id" value="<?=$order['id']?>">
            <button type="submit" name="delete_order">ลบ</button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>
</body>
</html>
