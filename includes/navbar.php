<?php
if (!isset($_SESSION)) session_start();
$user = $_SESSION['user'] ?? null;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">EasyAttend</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        <?php if ($user): ?>
          <?php if ($user['role'] === 'admin'): ?>
            <li class="nav-item"><a class="nav-link" href="/list.php">Users</a></li>
            <li class="nav-item"><a class="nav-link" href="/dashboard.php">Dashboard</a></li>
          <?php elseif ($user['role'] === 'employee'): ?>
            <li class="nav-item"><a class="nav-link" href="/attendance.php">My Attendance</a></li>
            <li class="nav-item"><a class="nav-link" href="/dashboard.php">My Dashboard</a></li>
          <?php endif; ?>
        <?php endif; ?>

      </ul>
      <ul class="navbar-nav">
        <?php if ($user): ?>
          <li class="nav-item"><span class="nav-link text-light">👤 <?= htmlspecialchars($user['username']) ?></span></li>
          <li class="nav-item"><a class="nav-link" href="/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="/index.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
