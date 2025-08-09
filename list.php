<?php
session_start();
require_once __DIR__ . '/classes/UserManager.php';
require_once __DIR__ . '/lang_loader.php';
include __DIR__ . '/navbar.php';

$userManager = new UserManager();
$users = $userManager->getAllUsers();

$deleted = $_SESSION['deleted'] ?? false;
unset($_SESSION['deleted']);
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
  <meta charset="UTF-8">
  <title><?= __('admin_users_list') ?> | EasyAttend</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .table th { white-space: nowrap; }
    .action-buttons { min-width: 180px; }
  </style>
</head>
<body class="bg-light">
<div class="container py-5">
  <div class="card shadow-lg p-4 rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0"><?= __('admin_users_list') ?></h2>
      <a href="add.php" class="btn btn-primary"><?= __('admin_add_user_title') ?></a>
    </div>

    <?php if ($deleted): ?>
      <div class="alert alert-success"><?= __('admin_user_deleted') ?></div>
    <?php endif; ?>

    <?php if (empty($users)): ?>
      <div class="alert alert-info"><?= __('admin_no_users_found') ?></div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th><?= __('admin_table_username') ?></th>
              <th><?= __('admin_table_fullname') ?></th>
              <th><?= __('admin_email') ?></th>
              <th><?= __('admin_role') ?></th>
              <th class="action-buttons"><?= __('admin_table_action') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php $i = 1; foreach ($users as $username => $user): ?>
              <?php if (($user['role'] ?? '') === 'admin') continue; ?>
              <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($username) ?></td>
                <td><?= htmlspecialchars($user['name'] ?? '-') ?></td>
                <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                <td>
                  <span class="badge bg-<?= $user['role'] === 'admin' ? 'primary' : 'secondary' ?>">
                    <?= htmlspecialchars($user['role'] ?? '-') ?>
                  </span>
                </td>
                <td>
                  <div class="d-flex gap-2">
                    <form method="post" action="edit.php" class="d-inline">
                      <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                      <button class="btn btn-sm btn-warning">
                        <?= __('admin_edit') ?>
                      </button>
                    </form>
                    <form method="post" action="delete.php" class="d-inline" 
                          onsubmit="return confirm('<?= __('admin_confirm_delete') ?>');">
                      <input type="hidden" name="username" value="<?= htmlspecialchars($username) ?>">
                      <button type="submit" class="btn btn-sm btn-danger">
                        <?= __('admin_delete') ?>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>
</body>
</html>
