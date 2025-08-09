<?php
session_start();
require_once __DIR__ . '/classes/UserManager.php';
require_once __DIR__ . '/classes/email.php';
$manager = new UserManager();
$message = '';
$user = null;

if (!isset($_SESSION['user'])) {
    if (isset($_SESSION['reset_user'])) {
        $username = $_SESSION['reset_user'];  // لأن username هو المفتاح
        $userData = $manager->getUser($username);
        if ($userData) {
            $user = $userData;
        } else {
            header('Location: index.php');
            exit;
        }
    } else {
        header('Location: index.php');
        exit;
    }
} else {
    $user = $_SESSION['user'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (strlen($newPassword) < 6) {
        $message = "❌ Password must be at least 6 characters.";
    } elseif ($newPassword !== $confirmPassword) {
        $message = "❌ Passwords do not match.";
    } else {
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $usernameToUpdate = $_SESSION['reset_user'] ?? $user['username'];
        $manager->updateUser($usernameToUpdate, ['password' => $hashed]);
        $userEmailInfo = $manager->getUser($usernameToUpdate);
if ($userEmailInfo && isset($userEmailInfo['email'], $userEmailInfo['name'])) {
    echo "Trying to send email to: " . $userEmailInfo['email']; // Debug
    sendPasswordChangedEmail($userEmailInfo['email'], $userEmailInfo['name']);
}
        if (isset($_SESSION['reset_user'])) {
            unset($_SESSION['reset_user']);
            $_SESSION['user'] = $manager->getUser($usernameToUpdate);
        } else {
            $_SESSION['user']['password'] = $hashed;
        }

        header("Location: dashboard.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password | EasyAttend</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(120deg, #0f2027, #203a43, #2c5364);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Segoe UI', sans-serif;
    }
    .card {
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 16px;
      box-shadow: 0 12px 25px rgba(0, 0, 0, 0.3);
      width: 100%;
      max-width: 400px;
    }
    .form-control:focus {
      box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
    }
    .title {
      font-size: 1.5rem;
      font-weight: bold;
      color: #203a43;
    }
  </style>
</head>
<body>

<div class="card p-4">
  <div class="text-center mb-3">
    <div class="title">Change Your Password</div>
    <small class="text-muted">Secure your account</small>
  </div>

  <?php if ($message): ?>
    <div class="alert alert-danger text-center"><?= $message ?></div>
  <?php endif; ?>

  <form method="post" class="row g-3">
    <div class="col-12">
      <label class="form-label text-dark">New Password</label>
      <input type="password" name="new_password" class="form-control" required>
    </div>
    <div class="col-12">
      <label class="form-label text-dark">Confirm Password</label>
      <input type="password" name="confirm_password" class="form-control" required>
    </div>
    <div class="col-12 d-grid">
      <button type="submit" class="btn btn-primary">Update Password</button>
    </div>
  </form>
</div>

</body>
</html>
