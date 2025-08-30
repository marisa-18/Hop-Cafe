<?php
include "db.php";
if (session_status() == PHP_SESSION_NONE) session_start();

$toast_msg = '';
$toast_type = '';

if (isset($_POST['reset'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    // ใช้ prepared statement ป้องกัน SQL Injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND email=? LIMIT 1");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // สร้างรหัสผ่านใหม่แบบสุ่ม 8 ตัว
        $new_pass = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);

        // เข้ารหัสใหม่ก่อนเก็บ
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);

        $row = $result->fetch_assoc();
        $update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $update->bind_param("si", $hashed, $row['id']);
        $update->execute();

        $toast_msg = "รหัสผ่านใหม่ของคุณคือ: <b>".$new_pass."</b>";
        $toast_type = "success";
    } else {
        $toast_msg = "ไม่พบข้อมูลผู้ใช้นี้";
        $toast_type = "error";
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<title>ลืมรหัสผ่าน - Hop Cafe</title>
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

h2 { 
  color:#5e4b8b; 
  margin-bottom:20px; 
}

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

a { 
  color:#6a4caf; 
  text-decoration:none; 
  margin-top:10px; 
  display:block; 
}

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
    <h2>ลืมรหัสผ่าน</h2>
    <form method="post">
        <input type="text" name="username" placeholder="ชื่อผู้ใช้" required>
        <input type="email" name="email" placeholder="อีเมล" required>
        <button type="submit" name="reset">กู้คืนรหัสผ่าน</button>
    </form>
    <a href="role_select.php">กลับไปเลือกประเภทผู้ใช้งาน</a>
</div>

<?php if($toast_msg != ''): ?>
<div id="toast" class="toast <?= $toast_type ?>"><?= $toast_msg ?></div>
<script>
const toast = document.getElementById('toast');
toast.classList.add('show');
setTimeout(() => { toast.classList.remove('show'); }, 5000);
</script>
<?php endif; ?>
</body>
</html>
