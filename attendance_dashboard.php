<?php
session_start();
require_once __DIR__ . '/lang_loader.php';
require_once __DIR__ . '/classes/UserManager.php';
require_once __DIR__ . '/classes/AttendanceManager.php';
include 'navbar.php';
date_default_timezone_set('Asia/Jerusalem');
$userManager       = new UserManager();
$attendanceManager = new AttendanceManager();

$users      = $userManager->getAllUsers();
$attendance = $attendanceManager->getAllAttendance();

$selectedUser     = $_POST['filter_user']      ?? '';
$selectedStatus   = $_POST['filter_status']    ?? '';
$selectedFromDate = $_POST['filter_from_date'] ?? date('Y-m-01');
$selectedToDate   = $_POST['filter_to_date']   ?? date('Y-m-d');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_shift'])) {
    $attendanceManager->updateShift(
        $_POST['edit_uid'],
        $_POST['edit_date'],
        intval($_POST['shift_index']),
        $_POST['edit_checkin'],
        $_POST['edit_checkout'],
        $_POST['edit_status']
    );
    echo '<script>window.location.href="attendance_dashboard.php";</script>';
    exit;
}

$chartLabels = [];
$chartData   = [];
foreach ($users as $uid => $user) {
    if (($user['role'] ?? '') === 'admin') continue;
    if ($selectedUser && $uid !== $selectedUser) continue;

    $records = $attendance[$uid] ?? [];
    $presentCount = 0;
    foreach ($records as $date => $rec) {
        if ($date < $selectedFromDate || $date > $selectedToDate) continue;
        foreach ($rec['shifts'] ?? [] as $s) {
            if (($s['status'] ?? '') === 'present') {
                $presentCount++;
                break;
            }
        }
    }

    $chartLabels[] = $user['name'];
    $chartData[]   = $presentCount;
}
?>

<!DOCTYPE html>
<html lang="<?= $_SESSION['lang'] ?? 'en' ?>">
<head>
  <meta charset="UTF-8">
  <title><?= __('admin_present_days_chart') ?> | EasyAttend</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .container { max-width: 95%; }
    .table-responsive { max-height: 70vh; overflow-y: auto; }
    thead { position: sticky; top: 0; z-index: 10; }
    .table thead th { background-color: #343a40; color: white; }
    .total-row { background-color: #e9ecef !important; font-weight: bold; }
  </style>
</head>
<body>

<div class="container py-4">
  <h2 class="fw-bold text-primary text-center mb-4"><?= __('admin_present_days_chart') ?></h2>
  <div class="card p-4 mb-4">
    <h5><?= __('admin_present_days_chart') ?></h5>
    <canvas id="presentChart" height="100"></canvas>
  </div>
  <div class="card p-4 mb-4">
    <form method="post" class="row g-3 align-items-end">
      <div class="col-md-3">
        <label class="form-label"><?= __('admin_filters_user') ?></label>
        <select name="filter_user" class="form-select">
          <option value=""><?= __('admin_filters_user') ?>: All</option>
          <?php foreach ($users as $id => $u): if (($u['role'] ?? '') !== 'admin'): ?>
            <option value="<?= $id ?>" <?= $selectedUser === $id ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['name']) ?>
            </option>
          <?php endif; endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label"><?= __('admin_filters_status') ?></label>
        <select name="filter_status" class="form-select">
          <option value=""><?= __('admin_filters_status') ?>: All</option>
          <option value="present" <?= $selectedStatus === 'present' ? 'selected' : '' ?>>Present</option>
          <option value="absent"  <?= $selectedStatus === 'absent'  ? 'selected' : '' ?>>Absent</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label"><?= __('admin_filters_from') ?></label>
        <input type="date" name="filter_from_date" class="form-control" value="<?= $selectedFromDate ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label"><?= __('admin_filters_to') ?></label>
        <input type="date" name="filter_to_date" class="form-control" value="<?= $selectedToDate ?>">
      </div>
      <div class="col-md-1">
        <button class="btn btn-primary w-100"><?= __('admin_filters_apply') ?></button>
      </div>
    </form>
  </div>
  <div class="table-responsive rounded shadow-sm mb-4">
    <table class="table table-bordered table-hover mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th><?= __('admin_table_fullname') ?></th>
          <th><?= __('admin_table_username') ?></th>
          <th><?= __('admin_table_date') ?></th>
          <th><?= __('admin_table_status') ?></th>
          <th><?= __('admin_table_checkin') ?></th>
          <th><?= __('admin_table_checkout') ?></th>
          <th><?= __('admin_table_hours') ?></th>
          <th><?= __('admin_table_action') ?></th>
        </tr>
      </thead>
      <tbody>
        <?php
        $i = 1;
        foreach ($attendance as $uid => $recs) {
            $u = $users[$uid] ?? null;
            if (!$u || ($u['role'] ?? '') === 'admin') continue;
            if ($selectedUser && $uid !== $selectedUser) continue;

            foreach ($recs as $date => $rec) {
                if ($date < $selectedFromDate || $date > $selectedToDate) continue;
                if ($selectedStatus && ($rec['status'] ?? '') !== $selectedStatus) continue;

                $dayTotal = 0;
                foreach ($rec['shifts'] ?? [] as $s) {
                    if (($s['status'] ?? '') === 'present') {
                        $dayTotal += floatval($s['total_hours'] ?? 0);
                    }
                }
                $extra = max(0, $dayTotal - 8);

                echo "<tr class='total-row'><td colspan='7'>" .
                    str_replace('{date}', $date, __('admin_total_for_date')) .
                    "</td><td>$dayTotal";
                if ($extra > 0) {
                    echo " <span class='text-danger'>" . str_replace('{extra}', $extra, __('admin_extra_hours_note')) . "</span>";
                }
                echo "</td><td></td></tr>";

                if (!empty($rec['shifts'])) {
                    foreach ($rec['shifts'] as $idx => $s) {
                        $status = $s['status']   ?? '';
                        $in     = $s['check_in'] ?? '-';
                        $out    = $s['check_out']?? '-';
                        $hrs    = floatval($s['total_hours'] ?? 0);
                        echo "<form method='post'><tr>" .
                             "<td>" . ($i++) . "</td>" .
                             "<td>" . htmlspecialchars($u['name']) . "</td>" .
                             "<td>" . htmlspecialchars($u['username']) . "</td>" .
                             "<td>$date</td>" .
                             "<td><select name='edit_status' class='form-select form-select-sm'>" .
                             "<option value='present'" . ($status==='present'?' selected':'') . ">Present</option>" .
                             "<option value='absent'"  . ($status==='absent' ?' selected':'') . ">Absent</option></select></td>" .
                             "<td><input type='time' name='edit_checkin'  value='$in'  class='form-control form-control-sm'></td>" .
                             "<td><input type='time' name='edit_checkout' value='$out' class='form-control form-control-sm'></td>" .
                             "<td>$hrs</td>" .
                             "<td><input type='hidden' name='edit_uid' value='$uid'>" .
                             "<input type='hidden' name='edit_date' value='$date'>" .
                             "<input type='hidden' name='shift_index' value='$idx'>" .
                             "<button name='update_shift' class='btn btn-sm btn-primary w-100'>" . __('admin_edit_shift_save') . "</button></td></tr></form>";
                    }
                }
            }
        }
        if ($i === 1) {
            echo "<tr><td colspan='9' class='text-muted text-center py-4'>" . __('admin_no_records_found') . "</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
  <div class="export-section">
    <form action="export_pdf.php" method="post" target="_blank" class="row g-3">
      <div class="col-md-4">
        <label class="form-label"><?= __('admin_filters_user') ?></label>
        <select name="user_id" class="form-select">
          <?php foreach ($users as $id => $user): if (($user['role'] ?? '') !== 'admin'): ?>
            <option value="<?= $id ?>"><?= htmlspecialchars($user['name']) ?></option>
          <?php endif; endforeach; ?>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Month</label>
        <input type="month" name="month" class="form-control" required>
      </div>
      <div class="col-md-4 d-flex align-items-end">
        <button type="submit" class="btn btn-outline-primary w-100">
          <i class="bi bi-file-earmark-pdf"></i> <?= __('admin_export_pdf') ?>
        </button>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const ctx = document.getElementById('presentChart').getContext('2d');
  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: <?= json_encode($chartLabels) ?>,
      datasets: [{
        label: '<?= __('admin_present_days_chart') ?>',
        data: <?= json_encode($chartData) ?>,
        backgroundColor: '#3498db',
        borderRadius: 5
      }]
    },
    options: {
      responsive: true,
      scales: { y: { beginAtZero: true } },
      plugins: { legend: { display: false } }
    }
  });
</script>
</body>
</html>