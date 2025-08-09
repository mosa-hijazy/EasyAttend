<?php
session_start();
require_once __DIR__ . '/classes/UserManager.php';
require_once __DIR__ . '/lang_loader.php';
include __DIR__ . '/navbar.php';

$userManager = new UserManager();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $name  = trim($_POST['name']);
    $role  = $_POST['role'];

    if (empty($email) || empty($name) || empty($role)) {
        $message = __('admin_all_fields_required');
    } elseif ($userManager->isDuplicateUser($email, $name)) {
        $message = __('admin_duplicate_user');
    } else {
        $username = 'emp_' . time();
        $defaultPassword = '123456';
        $hashedPassword = password_hash($defaultPassword, PASSWORD_DEFAULT);

        $data = [
            'name'       => $name,
            'email'      => $email,
            'username'   => $username,
            'password'   => $hashedPassword,
            'role'       => $role,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $userManager->addUser($data);
        $message = __('admin_add_user_success', ['username' => $username, 'password' => $defaultPassword]);
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
  <meta charset="UTF-8">
  <title><?= __('admin_add_user_title') ?> | EasyAttend</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="card shadow-lg p-4 rounded">
    <h2 class="mb-4 text-center"><?= __('admin_add_user_title') ?></h2>

    <?php if ($message): ?>
      <div class="alert alert-info"><?= nl2br(htmlspecialchars($message)) ?></div>
    <?php endif; ?>

    <form method="post" class="row g-3">
      <div class="col-md-6">
        <label class="form-label"><?= __('admin_full_name') ?></label>
        <input type="text" name="name" class="form-control" required>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?= __('admin_email') ?></label>
        <input type="email" name="email" class="form-control" required>
      </div>

      <div class="col-md-6">
        <label class="form-label"><?= __('admin_role') ?></label>
        <select name="role" class="form-select" required>
          <option value=""><?= __('admin_select_role') ?></option>
          <option value="employee"><?= __('role_employee', [], 'Employee') ?></option>
          <option value="admin"><?= __('role_admin', [], 'Admin') ?></option>
        </select>
      </div>

      <div class="col-12 text-end">
        <button type="submit" class="btn btn-primary px-5"><?= __('admin_save_button') ?></button>
        <a href="list.php" class="btn btn-secondary"><?= __('admin_back_button') ?></a>
      </div>
    </form>
  </div>
</div>
</body>
</html>
