<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// ✅ تغيير اللغة باستخدام POST بدون GET
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_lang'])) {
    $newLang = $_POST['change_lang'];
    if (in_array($newLang, ['en', 'he', 'ar'])) {
        $_SESSION['lang'] = $newLang;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

require_once __DIR__ . '/lang_loader.php';

$user = $_SESSION['user'] ?? null;
if (!$user) return;
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    :root {
      --primary-color: #4e73df;
      --secondary-color: #f8f9fc;
      --accent-color: #2e59d9;
      --dark-color: #1a1a2e;
    }

    body {
      background-color: var(--secondary-color);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .navbar {
      background: linear-gradient(135deg, var(--dark-color) 0%, var(--primary-color) 100%);
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      padding: 0.5rem 1rem;
    }

    .nav-link {
      font-weight: 500;
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      transition: all 0.3s ease;
      position: relative;
    }

    .nav-link:hover {
      background-color: rgba(255, 255, 255, 0.1);
      transform: translateY(-2px);
    }

    .nav-link.active {
      background-color: rgba(255, 255, 255, 0.2);
    }

    .dropdown-menu {
      background-color: var(--dark-color);
    }

    .dropdown-item {
      color: rgba(255, 255, 255, 0.8);
    }

    .dropdown-item:hover {
      background-color: var(--primary-color);
      color: white;
    }

    .user-badge {
      background: rgba(255, 255, 255, 0.1);
      padding: 0.3rem 0.8rem;
      border-radius: 50px;
      border-left: 3px solid var(--accent-color);
    }

    .btn-logout {
      border: 2px solid rgba(255, 255, 255, 0.3);
      transition: all 0.3s ease;
    }

    .btn-logout:hover {
      background-color: rgba(255, 255, 255, 0.1);
      border-color: white;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="assets/logo1.png" alt="Logo" height="45" class="me-2">
      <span>EasyAttend</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <?php if ($user['role'] === 'admin'): ?>
          <li class="nav-item"><a class="nav-link" href="list.php"><?= __('users') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="add.php"><?= __('add_user') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="attendance_dashboard.php"><?= __('attendance') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="leave_admin.php"><?= __('leaves') ?></a></li>
        <?php elseif ($user['role'] === 'employee'): ?>
          <li class="nav-item"><a class="nav-link" href="attendance.php"><?= __('my_attendance') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="dashboard.php"><?= __('dashboard') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="leave_request.php"><?= __('request_leave') ?></a></li>
          <li class="nav-item"><a class="nav-link" href="my_leaves.php"><?= __('my_leaves') ?></a></li>
        <?php endif; ?>

        <li class="nav-item"><a class="nav-link" href="chat.php"><?= __('chat') ?></a></li>

        <!-- Language Dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="langDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-translate"></i> <?= strtoupper($_SESSION['lang'] ?? 'EN') ?>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="langDropdown">
            <li>
              <form method="post"><input type="hidden" name="change_lang" value="en">
                <button type="submit" class="dropdown-item">English</button>
              </form>
            </li>
            <li>
              <form method="post"><input type="hidden" name="change_lang" value="he">
                <button type="submit" class="dropdown-item">עברית</button>
              </form>
            </li>
            <li>
              <form method="post"><input type="hidden" name="change_lang" value="ar">
                <button type="submit" class="dropdown-item">العربية</button>
              </form>
            </li>
          </ul>
        </li>
      </ul>

      <div class="d-flex align-items-center">
        <?php if ($user['role'] === 'employee'): ?>
          <a href="paystub.php" class="btn btn-sm btn-outline-light me-3"><?= __('pay_stub') ?></a>
        <?php endif; ?>
        <div class="user-badge me-3">
          <i class="bi bi-person-circle me-1"></i>
          <?= htmlspecialchars($user['name']) ?>
          <small class="opacity-75">(<?= __('role_' . $user['role']) ?>)</small>
        </div>
        <a href="logout.php" class="btn btn-sm btn-logout">
          <i class="bi bi-box-arrow-right"></i> <?= __('logout') ?>
        </a>
      </div>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
  document.querySelectorAll('.nav-link').forEach(link => {
    if (link.href === window.location.href) {
      link.classList.add('active');
    }
  });
</script>
</body>
</html>
