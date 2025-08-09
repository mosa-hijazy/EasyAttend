<?php
session_start();
require_once __DIR__ . '/lang_loader.php';           // <-- ייבוא המודול של שפות
require_once __DIR__ . '/classes/LeaveManager.php';
include __DIR__ . '/navbar.php';

$user = $_SESSION['user'] ?? null;
if (!$user || $user['role'] !== 'employee') {
    header('Location: index.php');
    exit;
}

require_once 'includes/firebase.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from   = $_POST['from_date']   ?? '';
    $to     = $_POST['to_date']     ?? '';
    $type   = $_POST['leave_type']  ?? '';
    $reason = trim($_POST['reason'] ?? '');

    if ($from && $to && $type && $reason) {
        $ref = $database->getReference('leave_requests')->push([
            'user_id'      => $user['username'],
            'name'         => $user['name'],
            'from_date'    => $from,
            'to_date'      => $to,
            'type'         => $type,
            'reason'       => $reason,
            'status'       => 'pending',
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);
        $message = __('leave_submit_success');
    } else {
        $message = __('all_fields_required');
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8" />
  <title><?= __('request_leave_title') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
  <div class="layout">
    <main class="main-content">
      <div class="container-fluid">
        <h1 class="mb-4"><?= __('request_leave_title') ?></h1>

        <?php if ($message): ?>
          <div class="alert <?= str_starts_with($message, '✅') ? 'alert-success' : 'alert-danger' ?>">
            <?= htmlspecialchars($message) ?>
          </div>
        <?php endif; ?>

        <form method="post" class="row g-4">
          <div class="col-md-6">
            <label class="form-label"><?= __('from_date') ?></label>
            <input type="date" name="from_date" class="form-control" required />
          </div>
          <div class="col-md-6">
            <label class="form-label"><?= __('to_date') ?></label>
            <input type="date" name="to_date" class="form-control" required />
          </div>

          <div class="col-md-6">
            <label class="form-label"><?= __('leave_type') ?></label>
            <select name="leave_type" class="form-select" required>
              <option value=""><?= __('select_placeholder') ?></option>
              <option value="Sick Leave"><?= __('sick_leave') ?></option>
              <option value="Vacation"><?= __('vacation') ?></option>
              <option value="Emergency"><?= __('emergency') ?></option>
              <option value="Other"><?= __('other') ?></option>
            </select>
          </div>

          <div class="col-md-6">
            <label class="form-label"><?= __('reason') ?></label>
            <textarea name="reason" class="form-control" rows="3"
                      placeholder="<?= __('reason_placeholder') ?>" required></textarea>
          </div>

          <div class="col-12 d-grid">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-paper-plane me-2"></i><?= __('submit_request') ?>
            </button>
          </div>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
