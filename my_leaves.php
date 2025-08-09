<?php
session_start();
require_once __DIR__ . '/lang_loader.php';
require_once __DIR__ . '/includes/firebase.php';
include __DIR__ . '/navbar.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employee') {
    header("Location: index.php");
    exit;
}

$user = $_SESSION['user'];
$filterStatus = $_POST['filter_status'] ?? '';

$allRequests = $database->getReference('leave_requests')->getValue() ?? [];

$leaves = array_filter($allRequests, function ($req) use ($user, $filterStatus) {
    $isMine = $req['user_id'] === $user['username'];
    $matchStatus = !$filterStatus || ($req['status'] ?? '') === $filterStatus;
    return $isMine && $matchStatus;
});
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8" />
  <title><?= __('my_leave_requests') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
    <main class="main-content">
      <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="mb-0"><?= __('my_leave_requests') ?></h1>
          <a href="leave_request.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i><?= __('new_request') ?>
          </a>
        </div>

        <form method="post" class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label fw-bold"><?= __('filter_by_status') ?></label>
            <select name="filter_status" class="form-select">
              <option value=""><?= __('all') ?></option>
              <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>><?= __('pending') ?></option>
              <option value="approved" <?= $filterStatus === 'approved' ? 'selected' : '' ?>><?= __('approved') ?></option>
              <option value="rejected" <?= $filterStatus === 'rejected' ? 'selected' : '' ?>><?= __('rejected') ?></option>
            </select>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-outline-primary w-100">
              <i class="fas fa-filter me-1"></i><?= __('apply') ?>
            </button>
          </div>
        </form>

        <div class="card">
          <h5 class="mb-3"><?= __('leave_history') ?></h5>
          <div class="table-responsive">
            <table class="table table-striped table-bordered text-center align-middle mb-0">
              <thead class="table-dark">
                <tr>
                  <th>#</th>
                  <th><?= __('from_to') ?></th>
                  <th><?= __('type') ?></th>
                  <th><?= __('reason') ?></th>
                  <th><?= __('status') ?></th>
                  <th><?= __('submitted_at') ?></th>
                </tr>
              </thead>
              <tbody>
                <?php $i = 1; foreach ($leaves as $id => $leave): ?>
                  <tr>
                    <td><?= $i++ ?></td>
                    <td>
                      <?= htmlspecialchars($leave['from_date']) ?>
                      → <?= htmlspecialchars($leave['to_date']) ?>
                    </td>
                    <td><?= htmlspecialchars($leave['type']) ?></td>
                    <td><?= htmlspecialchars($leave['reason']) ?></td>
                    <td>
                      <span class="badge bg-<?= 
                        $leave['status'] === 'approved' ? 'success' : 
                        ($leave['status'] === 'rejected' ? 'danger' : 'warning') ?>">
                        <?= __( $leave['status'] ) /* pending/approved/rejected */ ?>
                      </span>
                    </td>
                    <td><?= htmlspecialchars($leave['submitted_at']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($leaves)): ?>
                  <tr>
                    <td colspan="6" class="text-muted"><?= __('no_leave_requests') ?></td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </main>
</body>
</html>
