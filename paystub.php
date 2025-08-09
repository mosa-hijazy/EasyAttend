<?php
session_start();
require_once __DIR__ . '/lang_loader.php';     // ← load translations
require_once 'classes/AttendanceManager.php';
include __DIR__ . '/navbar.php';

date_default_timezone_set('Asia/Jerusalem');

$employee = $_SESSION['user'] ?? null;
if (!$employee) {
    header("Location: index.php");
    exit;
}

$emp_id = $employee['username'];
$month  = date("m");
$year   = date("Y");
$minimum_hours = 182;
$hourly_rate   = 50;

$attendanceManager = new AttendanceManager();
$allUserAttendance = $attendanceManager->getUserAttendance($emp_id);

$total_hours = 0;
$extra_hours = 0;
$daily_limit = 8;

foreach ($allUserAttendance as $date => $data) {
    // רק חודשי
    if (substr($date, 0, 7) !== "$year-$month") continue;

    $daily_total = 0;
    // סכום משמרות רק עם סטטוס present
    foreach ($data['shifts'] ?? [] as $shift) {
        if (isset($shift['status']) && strtolower($shift['status']) === 'present') {
            $daily_total += floatval($shift['total_hours'] ?? 0);
        }
    }

    $total_hours += $daily_total;

    if ($daily_total > $daily_limit) {
        $extra_hours += ($daily_total - $daily_limit);
    }
}

$gross_salary = round($total_hours * $hourly_rate, 2);
$extra_salary = round($extra_hours * $hourly_rate, 2);
$total_salary = $gross_salary + $extra_salary;
$has_warning  = $total_hours < $minimum_hours;
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8">
  <title><?= __('salary_slip_title') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="main-content">
      <div class="container-fluid">
        <h1 class="text-center mb-4"><?= __('salary_slip_heading', ["month"=>$month, "year"=>$year]) ?></h1>

        <div class="card p-4 shadow-sm">
          <table class="table table-bordered table-striped">
            <tr><th><?= __('employee_name') ?></th><td><?= htmlspecialchars($employee['name']) ?></td></tr>
            <tr><th><?= __('employee_id') ?></th><td><?= $emp_id ?></td></tr>
            <tr><th><?= __('hourly_rate') ?></th><td><?= $hourly_rate ?> ₪</td></tr>
            <tr><th><?= __('total_hours') ?></th><td><?= number_format($total_hours, 2) ?></td></tr>
            <tr><th><?= __('extra_hours') ?></th><td><?= number_format($extra_hours, 2) ?></td></tr>
            <tr><th><?= __('base_salary') ?></th><td><?= number_format($gross_salary, 2) ?> ₪</td></tr>
            <tr><th><?= __('extra_salary') ?></th><td><?= number_format($extra_salary, 2) ?> ₪</td></tr>
            <tr class="table-success fw-bold"><th><?= __('total_salary') ?></th><td><?= number_format($total_salary, 2) ?> ₪</td></tr>
          </table>

          <?php if ($has_warning): ?>
            <div class="alert alert-warning mt-3">
              <?= __('warning_below_minimum', ["min"=>$minimum_hours]) ?>
            </div>
          <?php else: ?>
            <div class="alert alert-success mt-3">
              <?= __('congrats_completed') ?>
            </div>
          <?php endif; ?>

          <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">
              <i class="fas fa-arrow-left me-2"></i> <?= __('back_to_dashboard') ?>
            </a>
          </div>
        </div>
      </div>
    </main>
</body>
</html>
