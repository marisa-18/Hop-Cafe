<?php
include "db.php";
if (session_status() == PHP_SESSION_NONE) session_start();

// ตรวจสอบว่ามี role ใน session หรือยัง ถ้าไม่ให้กลับไปหน้าเลือก role
if(!isset($_SESSION['role'])){
    header("Location: role_select.php");
    exit;
}

// ดึง role จาก session
$role = $_SESSION['role'];

$toast_msg = '';
$toast_type = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // ใช้ prepared statement ป้องกัน SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND role=? LIMIT 1");
    $stmt->bind_param("ss", $username, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role']
            ];

            $toast_msg = "เข้าสู่ระบบสำเร็จ!";
            $toast_type = "success";

            // Redirect หลังแสดง toast
            $redirect_url = ($user['role'] == 'admin') ? 'admin/orders.php' : 'index.php';
            header("Refresh:1; url=$redirect_url");
            exit;
        } else {
            $toast_msg = "รหัสผ่านไม่ถูกต้อง";
            $toast_type = "error";
        }
    } else {
        $toast_msg = "ไม่พบผู้ใช้สำหรับประเภทนี้";
        $toast_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>เข้าสู่ระบบ - Hop Cafe</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="assets/style.css">
<style>
/* รีเซ็ต */
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Prompt', sans-serif; }

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

input { width:90%; padding:10px; margin:8px 0; border:1px solid #ccc; border-radius:8px; }

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
    <h2>เข้าสู่ระบบ (<?= ucfirst($role) ?>)</h2>
    <form method="post">
        <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
        <input type="password" name="password" placeholder="รหัสผ่าน" required>
        <button type="submit" name="login">เข้าสู่ระบบ</button>
    </form>

    <a href="register.php"><i class="fa-solid fa-user-plus"></i> สมัครสมาชิก</a>
    <a href="forgot_password.php"><i class="fa-solid fa-key"></i> ลืมรหัสผ่าน</a>
    <a href="role_select.php">กลับไปเลือกประเภทผู้ใช้งาน</a>
</div>

<?php if($toast_msg != ''): ?>
<div id="toast" class="toast <?= $toast_type ?>"><?= $toast_msg ?></div>
<script>
const toast = document.getElementById('toast');
toast.classList.add('show');
setTimeout(() => { toast.classList.remove('show'); }, 3000);
</script>
<?php endif; ?>

</body>
</html>
