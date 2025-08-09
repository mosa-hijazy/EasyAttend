<?php
require_once __DIR__ . '/lang_loader.php';      // טוען session_start() ומשתנה השפה
date_default_timezone_set('Asia/Jerusalem');
require_once __DIR__ . '/classes/AttendanceManager.php';
require_once __DIR__ . '/classes/email.php';
include    __DIR__ . '/navbar.php';

// אם לא משתמש מחובר או לא עובד – העבר לעמוד הכניסה
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'employee') {
    header('Location: index.php');
    exit;
}

$user     = $_SESSION['user'];
$userId   = $user['username'];
$today    = date('Y-m-d');

$attendanceManager = new AttendanceManager();
$record            = $attendanceManager->getDayAttendance($userId, $today);
$shifts            = $record['shifts'] ?? [];

$message      = '';
$hasOpenShift = false;
foreach ($shifts as $shift) {
    if (empty($shift['check_out'])) {
        $hasOpenShift = true;
        break;
    }
}

// טיפול בטפסי POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check In
    if (isset($_POST['check_in']) && !$hasOpenShift) {
        $currentTime = strtotime(date('H:i'));
        if ($currentTime < strtotime('08:00')) {
            $message = __('early_checkin_block');
        } else {
            $success = $attendanceManager->checkIn($userId);
            $message = $success
                ? __('checkin_success')
                : __('checkin_fail');

            $isFirstShift = count($shifts) === 0;
            $isLate       = $currentTime > strtotime('08:30');
            if ($success && $isFirstShift && $isLate) {
                $to       = $user['email'] ?? null;
                if ($to) {
                    $mailSent = sendLateEmail($to, $user['name'], $today);
                    if (!$mailSent) {
                        $message .= '<br>' . __('email_fail');
                    }
                }
            }
        }
    }
    // Check Out
    elseif (isset($_POST['check_out']) && $hasOpenShift) {
        $lastShift   = end($shifts);
        $checkInTime = strtotime("$today {$lastShift['check_in']}");
        $now         = time();
        $minutesDiff = ($now - $checkInTime) / 60;

        if ($minutesDiff < 60 && !isset($_POST['confirm_checkout'])) {
            $message = __('less_than_hour');
        } else {
            $success = $attendanceManager->checkOut($userId);
            $message = $success
                ? __('checkout_success')
                : __('checkout_fail');
        }
    }

    // רענון
    $record      = $attendanceManager->getDayAttendance($userId, $today);
    $shifts      = $record['shifts'] ?? [];
    $hasOpenShift = false;
    foreach ($shifts as $shift) {
        if (empty($shift['check_out'])) {
            $hasOpenShift = true;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8" />
  <title><?= __('today_attendance') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<body>
<main class="main-content">
  <div class="container-fluid">
    <h1 class="mb-4"><?= __('today_attendance') ?></h1>

    <?php if ($message): ?>
      <div class="alert alert-<?= strpos($message, '⚠️') === 0 ? 'warning' : (strpos($message, '✅') === 0 ? 'success' : 'danger') ?> fw-semibold">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <div class="d-flex gap-3 mb-4">
      <?php if (!$hasOpenShift): ?>
        <form method="post">
          <button type="submit" name="check_in" class="btn btn-success">
            <i class="fas fa-sign-in-alt me-2"></i><?= __('check_in') ?>
          </button>
        </form>
      <?php endif; ?>

      <?php if ($hasOpenShift): ?>
        <form method="post">
          <?php if ($message === __('less_than_hour')): ?>
            <input type="hidden" name="confirm_checkout" value="1">
            <button type="submit" name="check_out" class="btn btn-warning">
              <i class="fas fa-clock me-2"></i><?= __('confirm_checkout') ?>
            </button>
          <?php else: ?>
            <button type="submit" name="check_out" class="btn btn-danger">
              <i class="fas fa-sign-out-alt me-2"></i><?= __('check_out') ?>
            </button>
          <?php endif; ?>
        </form>
      <?php endif; ?>
    </div>

    <div class="card">
      <h5 class="mb-3"><?= __('todays_shifts') ?></h5>
      <div class="table-responsive">
        <table class="table table-striped table-bordered text-center align-middle mb-0">
          <thead>
            <tr>
              <th><?= __('date') ?></th>
              <th><?= __('check_in_time') ?></th>
              <th><?= __('check_out_time') ?></th>
              <th><?= __('total_hours') ?></th>
              <th><?= __('extra_hours') ?></th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($shifts)):
              $totalHours = 0;
              foreach ($shifts as $shift):
                $checkIn  = htmlspecialchars($shift['check_in'] ?? '-');
                $checkOut = htmlspecialchars($shift['check_out'] ?? '-');
                $hours    = floatval($shift['total_hours'] ?? 0);
                $totalHours += $hours;
            ?>
              <tr>
                <td><?= $today ?></td>
                <td><?= $checkIn ?></td>
                <td><?= $checkOut ?></td>
                <td><?= $hours > 0 ? $hours : '-' ?></td>
                <td><?= $hours > 8 ? $hours - 8 : '-' ?></td>
              </tr>
            <?php endforeach; ?>
              <tr class="table-secondary fw-bold">
                <td colspan="3" class="text-end"><?= __('total_for') ?> <?= $today ?>:</td>
                <td><?= $totalHours ?></td>
                <td><?= $totalHours > 8 ? $totalHours - 8 : '-' ?></td>
              </tr>
            <?php else: ?>
              <tr><td colspan="5" class="text-muted"><?= __('no_records') ?></td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</body>
</html>
