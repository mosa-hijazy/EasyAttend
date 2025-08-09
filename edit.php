<?php
session_start();
require_once __DIR__ . '/classes/UserManager.php';
include __DIR__ . '/navbar.php';

$manager = new UserManager();
$message = '';

if (!isset($_POST['username'])) {
    exit("❌ No username provided.");
}

$username = trim($_POST['username']);
$user = $manager->getUser($username);

if (!$user) {
    echo "<pre>Checked username: '$username'</pre>";
    exit("❌ User not found.");
}


if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['name'], $_POST['email'], $_POST['role']) &&
    !empty($_POST['name']) && !empty($_POST['email'])
) {
    $updatedData = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'role' => $_POST['role']
    ];

    $manager->updateUser($username, $updatedData);
    $message = "✅ User updated successfully!";
    $user = $manager->getUser($username); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit User</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-lg p-4 rounded">
    <h2 class="mb-4 text-center">Edit User</h2>

    <?php if ($message): ?>
      <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
      <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">

      <div class="col-md-6">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($user['name'] ?? '') ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($user['email'] ?? '') ?>">
      </div>

      <div class="col-md-6">
        <label class="form-label">Role</label>
        <select name="role" class="form-select" required>
          <option value="employee" <?= ($user['role'] ?? '') === 'employee' ? 'selected' : '' ?>>Employee</option>
          <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
        </select>
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary px-4">Save Changes</button>
        <a href="list.php" class="btn btn-secondary">Back</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
