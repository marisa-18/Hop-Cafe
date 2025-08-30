<?php
include "../db.php"; // เชื่อม DB
if (session_status() == PHP_SESSION_NONE) session_start();

// ✅ ตรวจสอบสิทธิ์ admin (統一ให้เหมือนทุกไฟล์)
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$toast_msg = '';
$toast_type = '';

if (isset($_POST['add_menu'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);

    // อัปโหลดรูปภาพ
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $filename = time() . "_" . basename($_FILES['image']['name']); // ป้องกันชื่อซ้ำ
        $target = '../assets/uploads/' . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $filename;
        }
    }

    $sql = "INSERT INTO menu (name, price, image) VALUES ('$name', '$price', '$image')";
    if ($conn->query($sql)) {
        $toast_msg = "เพิ่มเมนูสำเร็จ!";
        $toast_type = "success";
    } else {
        $toast_msg = "เกิดข้อผิดพลาด: " . $conn->error;
        $toast_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>จัดการเมนู - Admin</title>
<link rel="stylesheet" href="../assets/style.css">
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
<div class="card">
    <h2>เพิ่มเมนูใหม่</h2>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="name" placeholder="ชื่อเมนู" required>
        <input type="number" step="0.01" name="price" placeholder="ราคา" required>
        <input type="file" name="image" accept="image/*" required>
        <button type="submit" name="add_menu">เพิ่มเมนู</button>
    </form>
    <a href="index.php">กลับไปหน้าแอดมิน</a>
</div>

<?php if ($toast_msg != ''): ?>
<div id="toast" class="toast <?= $toast_type ?>"><?= $toast_msg ?></div>
<script>
const toast = document.getElementById('toast');
toast.classList.add('show');
setTimeout(()=>{toast.classList.remove('show');},3000);
</script>
<?php endif; ?>
</body>
</html>
