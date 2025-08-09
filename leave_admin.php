<?php
session_start();
require_once __DIR__ . '/includes/firebase.php';
require_once __DIR__ . '/lang_loader.php';
include __DIR__ . '/navbar.php';

// تأكد من صلاحية الأدمن
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// التعامل مع الموافقة أو الرفض
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $requestId = $_POST['request_id'] ?? '';
    $action    = $_POST['action'] ?? '';

    if ($requestId && in_array($action, ['approved', 'rejected'])) {
        $database->getReference("leave_requests/$requestId/status")->set($action);
        header("Location: leave_admin.php");
        exit;
    }
}

// جلب الطلبات
$leaveRequests = $database->getReference('leave_requests')->getValue() ?? [];
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8">
  <title><?= __('admin_leave_title') ?> | EasyAttend</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/leave_admin.css">
</head>
<body>

<div class="container py-5">
  <div class="card shadow-lg p-4">
    <h2 class="text-center mb-4"><?= __('admin_leave_title') ?></h2>

    <div class="table-responsive">
      <table class="table table-bordered text-center align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th><?= __('admin_leave_employee') ?></th>
            <th><?= __('admin_table_date') ?></th>
            <th><?= __('admin_leave_type') ?></th>
            <th><?= __('admin_leave_reason') ?></th>
            <th><?= __('admin_leave_status') ?></th>
            <th><?= __('admin_leave_submitted_at') ?></th>
            <th><?= __('admin_table_action') ?></th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 1; foreach ($leaveRequests as $requestId => $leave): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td>
              <?= htmlspecialchars($leave['name']) ?><br>
              <small class="text-muted">(<?= htmlspecialchars($leave['user_id']) ?>)</small>
            </td>
            <td><?= htmlspecialchars($leave['from_date']) ?> → <?= htmlspecialchars($leave['to_date']) ?></td>
            <td><?= htmlspecialchars($leave['type']) ?></td>
            <td><?= htmlspecialchars($leave['reason']) ?></td>
            <td>
              <span class="badge bg-<?= 
                $leave['status'] === 'approved' ? 'success' :
                ($leave['status'] === 'rejected' ? 'danger' : 'warning')
              ?>">
                <?= __('admin_leave_status_' . $leave['status']) ?>
              </span>
            </td>
            <td><?= htmlspecialchars($leave['submitted_at']) ?></td>
            <td>
              <?php if ($leave['status'] === 'pending'): ?>
                <form method="post" class="d-flex flex-column gap-2">
                  <input type="hidden" name="request_id" value="<?= $requestId ?>">
                  <button name="action" value="approved" class="btn btn-outline-success btn-sm w-100">
                    ✅ <?= __('admin_leave_approve') ?>
                  </button>
                  <button name="action" value="rejected" class="btn btn-outline-danger btn-sm w-100">
                    ❌ <?= __('admin_leave_reject') ?>
                  </button>
                </form>
              <?php else: ?>
                <span class="text-muted fst-italic"><?= __('admin_leave_reviewed') ?></span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>

          <?php if ($i === 1): ?>
            <tr>
              <td colspan="8" class="text-muted text-center py-4"><?= __('admin_leave_empty') ?></td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
