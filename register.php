<?php
include "db.php";
if (session_status() == PHP_SESSION_NONE) session_start();

$toast_msg = '';
$toast_type = '';
$redirect = false; // ตัวแปรบอกว่าจะ redirect หรือไม่

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    // ตรวจสอบว่ามี username ซ้ำหรือไม่
    $stmt = $conn->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        $toast_msg = "ชื่อผู้ใช้นี้ถูกใช้แล้ว!";
        $toast_type = "error";
    } else {
        // เข้ารหัสรหัสผ่าน
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $hashed_password, $email);

        if ($stmt->execute()) {
            $toast_msg = "สมัครสมาชิกสำเร็จ! กำลังพาไปหน้าเข้าสู่ระบบ...";
            $toast_type = "success";
            $redirect = true;
        } else {
            $toast_msg = "เกิดข้อผิดพลาด: " . $conn->error;
            $toast_type = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>สมัครสมาชิก - Hop Cafe</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* รีเซ็ต */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Prompt', sans-serif;
}
body {
  background: linear-gradient(135deg,#5e4b8b,#8b6fc9);
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}
/* กล่องฟอร์ม */
.card {
  background:#fff;
  padding:30px;
  border-radius:15px;
  box-shadow:0 8px 20px rgba(0,0,0,0.2);
  width:350px;
  text-align:center;
}
h2 { color:#5e4b8b; margin-bottom:20px; }
input { 
  width:90%; 
  padding:10px; 
  margin:8px 0; 
  border:1px solid #ccc; 
  border-radius:8px; 
}
button {
  background:#6a4caf; 
  color:white; 
  border:none; 
  padding:10px;
  width:100%; 
  border-radius:8px; 
  cursor:pointer; 
  font-size:16px;
}
button:hover { background:#5e4b8b; }
a { color:#6a4caf; text-decoration:none; margin-top:10px; display:block; }
a:hover { text-decoration:underline; }
/* Toast */
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
.toast.show { 
  opacity:1; 
  pointer-events:auto; 
  transform: translateX(-50%) translateY(0); 
}
.toast.success { background:#28a745; }
.toast.error { background:#dc3545; }
</style>
</head>
<body>
<div class="card">
    <h2>สมัครสมาชิก</h2>
    <form method="post">
        <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" required>
        <input type="email" name="email" placeholder="อีเมล" required>
        <button type="submit" name="register">สมัครสมาชิก</button>
    </form>
    <a href="role_select.php">กลับไปเลือกประเภทผู้ใช้งาน</a>
</div>

<?php if($toast_msg != ''): ?>
<div id="toast" class="toast <?= $toast_type ?>"><?= $toast_msg ?></div>
<script>
const toast = document.getElementById('toast');
toast.classList.add('show');
setTimeout(() => { toast.classList.remove('show'); }, 4000);

// ถ้าสมัครสำเร็จ -> redirect ไป login.php
<?php if($redirect): ?>
    setTimeout(() => { window.location.href = "login.php"; }, 2000);
<?php endif; ?>
</script>
<?php endif; ?>
</body>
</html>
