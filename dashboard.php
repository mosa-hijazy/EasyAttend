<?php
session_start();
require_once __DIR__ . '/lang_loader.php';
require_once __DIR__ . '/classes/AttendanceManager.php';
require_once __DIR__ . '/classes/Attend.php';
include __DIR__ . '/navbar.php';

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$user     = $_SESSION['user'];
$username = $user['username'] ?? null;
if (!$username) die(__('error_no_username'));

// الآن القراءة من POST فقط
$year     = $_POST['year']      ?? date('Y');
$month    = $_POST['month']     ?? date('m');
$startDay = $_POST['start_day'] ?? 1;

$month = str_pad($month, 2, '0', STR_PAD_LEFT);
$monthName   = date('F', strtotime("$year-$month-01"));
$daysInMonth = date('t', strtotime("$year-$month-01"));
$dates = [];
for ($day = $startDay; $day <= $daysInMonth; $day++) {
    $dates[] = sprintf('%04d-%02d-%02d', $year, $month, $day);
}

$attendanceManager = new AttendanceManager();
$attendance        = $attendanceManager->getAttendanceRecords($username, $year, $month, $startDay);

$attend        = new Attend($attendance, $dates);
$presentCount  = $attendanceManager->countStrictPresentDays($attendance);
$absentCount   = $attend->countAbsentDays();
$totalHours    = $attend->calculateTotalHours();
$extraHours    = $attendanceManager->sumMonthlyExtraHours($username, $year, $month);

if (empty($attendance)) {
    $presentCount = $absentCount = $totalHours = $extraHours = 0;
}

// بدل GET
$showTable = isset($_POST['year'], $_POST['month'], $_POST['start_day']);
?>

<!DOCTYPE html>
<html lang="<?= htmlspecialchars($_SESSION['lang'] ?? 'en') ?>">
<head>
  <meta charset="UTF-8">
  <title><?= __('dashboard_title') ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/dashboard.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<main class="main-content">
  <div class="container-fluid">
    <h1 class="mb-4"><?= __('dashboard_overview') ?></h1>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card text-center">
          <h6><?= __('present_days') ?></h6>
          <div class="display-6 text-success"><?= $presentCount ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <h6><?= __('absent_days') ?></h6>
          <div class="display-6 text-danger"><?= $absentCount ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <h6><?= __('total_hours') ?></h6>
          <div class="display-6 text-info"><?= $totalHours ?></div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center">
          <h6><?= __('extra_hours') ?></h6>
          <div class="display-6 text-warning"><?= $extraHours ?></div>
        </div>
      </div>
    </div>

    <!-- User Info -->
    <div class="card p-4 mb-4">
      <h4 class="mb-3"><?= __('user_info') ?></h4>
      <table class="table table-borderless">
        <tr><th><?= __('name') ?>:</th><td><?= htmlspecialchars($user['name']) ?></td></tr>
        <tr><th><?= __('email') ?>:</th><td><?= htmlspecialchars($user['email']) ?></td></tr>
        <tr><th><?= __('role') ?>:</th><td><?= htmlspecialchars($user['role'] ?? '-') ?></td></tr>
        <tr><th><?= __('username') ?>:</th><td><?= htmlspecialchars($username) ?></td></tr>
      </table>
    </div>

    
    <div class="card p-4 mb-4">
      <h4 class="mb-3"><?= __('attendance_chart') ?></h4>
      <h6 class="text-muted"><?= __('summary_for') ?> <?= $monthName ?> <?= $year ?></h6>
      <canvas id="attendanceChart" height="150"></canvas>
    </div>

    <div class="card p-4 mb-4">
      <h4 class="mb-3"><?= __('search_attendance') ?></h4>
      <form method="post" class="row g-3 mb-4">
  <div class="col-md-4">
    <label><?= __('year') ?></label>
    <input type="number" name="year" class="form-control" value="<?= $year ?>" required>
  </div>
  <div class="col-md-4">
    <label><?= __('month') ?></label>
    <input type="number" name="month" class="form-control" value="<?= $month ?>" min="1" max="12" required>
  </div>
  <div class="col-md-4">
    <label><?= __('start_day') ?></label>
    <input type="number" name="start_day" class="form-control" value="<?= $startDay ?>" min="1" max="31" required>
  </div>
  <div class="col-12 text-end">
    <button type="submit" class="btn btn-primary mt-2">
      <i class="fas fa-search me-2"></i><?= __('show_attendance') ?>
    </button>
  </div>
</form>

      <?php if ($showTable): ?>
        <h4 class="mb-3"><?= __('attendance_for') ?> <?= $monthName ?> <?= $year ?></h4>
        <?php if (empty($attendance)): ?>
          <div class="alert alert-info"><?= __('no_attendance_records') ?></div>
        <?php else: ?>
          <div class="table-responsive" style="max-height: 400px;">
            <table class="table table-striped table-bordered">
              <thead class="table-light">
                <tr><th><?= __('date') ?></th><th><?= __('status') ?></th><th><?= __('details') ?></th></tr>
              </thead>
              <tbody>
                <?php foreach ($dates as $date): $record = $attendance[$date] ?? null; ?>
                <tr>
                  <td><?= $date ?></td>
                  <td>
                    <?php
                      $isPresent = false;
                      if ($record && !empty($record['shifts'])) {
                        foreach ($record['shifts'] as $shift) {
                          if (($shift['status'] ?? '')==='present' && !empty(trim($shift['check_in']))) {
                            $isPresent = true; break;
                          }
                        }
                      }
                    ?>
                    <span class="badge <?= $isPresent?'bg-success':'bg-danger' ?>">
                      <?= $isPresent?__('present'):__('absent') ?>
                    </span>
                  </td>
                  <td>
                    <?php if (!empty($record['shifts'])): ?>
                      <?php foreach ($record['shifts'] as $shift): ?>
                        <?php if (($shift['status'] ?? '')==='present'): ?>
                          <div>
                            <?= __('in') ?>: <?= $shift['check_in'] ?? '-' ?> |
                            <?= __('out') ?>: <?= $shift['check_out'] ?? '-' ?> |
                            <?= __('total') ?>: <?= $shift['total_hours'] ?? '-' ?>h
                          </div>
                        <?php endif; ?>
                      <?php endforeach; ?>
                    <?php else: ?>-<?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</main>

<!-- Chart Script -->
<script>
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
  type: 'bar',
  data: {
    labels: [
      '<?= __('present_days') ?>',
      '<?= __('absent_days') ?>',
      '<?= __('total_hours') ?>',
      '<?= __('extra_hours') ?>'
    ],
    datasets: [{
      label: '<?= __('attendance_summary') ?>',
      data: [<?= $presentCount ?>, <?= $absentCount ?>, <?= $totalHours ?>, <?= $extraHours ?>],
      backgroundColor: ['#27ae60','#e74c3c','#3498db','#f39c12'],
      borderRadius: 10,
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>
</body>
</html>
