<?php
session_start();
require_once __DIR__ . '/classes/UserManager.php';

$manager = new UserManager();
$message = '';

<<<<<<< HEAD

=======
// لو المستخدم مسجّل مسبقًا
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? '';
    if ($role === 'admin') {
        header('Location: attendance_dashboard.php');
    } elseif ($role === 'employee') {
        header('Location: attendance.php');
    } else {
        header('Location: index.php');
    }
    exit;
}

<<<<<<< HEAD

=======
// معالجة تسجيل الدخول
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '') {
        $users = $manager->getAllUsers();

        foreach ($users as $user) {
            if (
                isset($user['username'], $user['password']) &&
                $user['username'] === $username &&
                password_verify($password, $user['password'])
            ) {
<<<<<<< HEAD
                
=======
                // خزّن الجلسة
>>>>>>> 267c7f8db6d46cb8eac9b5208e409ad222c0d007
                $_SESSION['user'] = [
                    'username' => $user['username'],
                    'name'     => $user['name'] ?? $user['username'],
                    'role'     => $user['role'] ?? 'employee',
                    'email'    => $user['email'] ?? '',
                ];

                // لو أول دخول بكلمة 123456 مثلاً وتحب تجبر تغييرها:
                // if (password_verify('123456', $user['password'])) {
                //     $_SESSION['reset_user'] = $user['username'];
                //     header('Location: change_password.php');
                //     exit;
                // }

                // لو أدمن -> شغّل نسخ احتياطي بالخلفية
               if (($_SESSION['user']['role'] ?? '') === 'admin') {
   /* // عدّل المسارات حسب جهازك
    $phpExe = 'C:\\xampp\\php\\php.exe';
    $script = 'C:\\xampp\\htdocs\\EasyAttend\\credentials\\backup_and_upload.php';

    // 1) لوج بسيط يساعدنا نعرف صار التنفيذ ولا لأ
    @file_put_contents(__DIR__ . '/backup_boot.log', date('c') . " - trying to start backup\n", FILE_APPEND);

    // 2) استخدم cmd /c لأن start أمر داخلي في cmd
    //    ولاحظ: نحط "" بعد start كعنوان نافذة
    $cmd = 'cmd /c start "" /B "' . $phpExe . '" -f "' . $script . '" >NUL 2>&1';

    // 3) تأكد إن popen/pclose مش متعطّلين بالـ php.ini
    if (function_exists('popen') && function_exists('pclose')) {
        @pclose(@popen($cmd, 'r'));
        @file_put_contents(__DIR__ . '/backup_boot.log', date('c') . " - popen issued\n", FILE_APPEND);
    } else {
        // بديل لو popen متعطل
        @exec($cmd . ' &', $out, $ret);
        @file_put_contents(__DIR__ . '/backup_boot.log', date('c') . " - exec issued, ret=$ret\n", FILE_APPEND);
    }

    $_SESSION['flash_backup'] = 'بدأنا النسخة الاحتياطية بالخلفية 🎒';
    */
    header('Location: attendance_dashboard.php');
    exit;
}

                // موظف
                header('Location: attendance.php');
                exit;
            }
        }
        $message = "❌ Invalid username or password.";
    } else {
        $message = "❌ Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>EasyAttend | Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
    rel="stylesheet">
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: linear-gradient(to right, #0f2027, #203a43, #2c5364);
      color: #fff;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }
    .hero { padding: 60px 20px; text-align: center; }
    .btn-start { padding: 12px 30px; font-size: 1.1rem; margin-top: 20px; }
    #loginForm {
      display: none;
      max-width: 400px;
      background-color: #ffffff; color: #000;
      padding: 30px; border-radius: 15px;
      box-shadow: 0 12px 30px rgba(0,0,0,0.4);
      margin: 40px auto;
    }
    .form-control:focus { box-shadow: 0 0 10px rgba(13, 110, 253, 0.5); }
    .logo { font-size: 1.8rem; font-weight: bold; color: #2c5364; }
    footer { background-color: #0f2027; padding: 40px 20px; margin-top: auto; color: #ccc; text-align: center; }
    footer h5 { color: #fff; font-weight: bold; margin-bottom: 15px; }
    footer p { max-width: 600px; margin: auto; font-size: 0.95rem; }
    @media (max-width: 576px) { .hero h1 { font-size: 1.8rem; } }
  </style>
</head>
<body>

<div class="hero">
  <h1 class="mb-3">Welcome to EasyAttend</h1>
  <p class="mb-4">Simple. Smart. Employee Attendance Management.</p>
  <button class="btn btn-warning btn-start" onclick="showLogin()">Get Started</button>
</div>

<div id="loginForm">
  <div class="text-center mb-3">
    <div class="logo">EasyAttend</div>
    <small class="text-muted">Employee Attendance System</small>
  </div>

  <?php if (!empty($message)): ?>
    <div class="alert alert-danger text-center"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="post" class="row g-3">
    <div class="col-12">
      <label class="form-label">Username</label>
      <input type="text" name="username" class="form-control" required>
    </div>
    <div class="col-12">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <div class="col-12 d-grid">
      <button type="submit" class="btn btn-primary">Login</button>
    </div>
    <div class="text-center mt-2">
      <a href="forgot_password.php" class="text-decoration-none">🔑 Forgot your password?</a>
    </div>
  </form>
</div>

<footer>
  <h5>Your Attendance, Simplified</h5>
  <p>Track working hours, manage employees, and streamline your HR processes — all in one platform. EasyAttend empowers your business with precision and ease.</p>
  <small>&copy; <?= date("Y") ?> EasyAttend. All rights reserved.</small>
</footer>

<script>
  function showLogin() {
    document.getElementById("loginForm").style.display = "block";
    document.querySelector(".btn-start").style.display = "none";
  }
</script>

</body>
</html>
