<?php
require_once __DIR__ . '/classes/UserManager.php';
require_once __DIR__ . '/classes/AttendanceManager.php';

$userManager = new UserManager();
$attendanceManager = new AttendanceManager();

$users = $userManager->getAllUsers();
$attendance = $attendanceManager->getAllAttendance();

$selectedMonth = $_GET['month'] ?? date('Y-m'); // למשל: 2025-05
$chartData = [];

foreach ($users as $uid => $user) {
    $total = 0;
    foreach ($attendance[$uid] ?? [] as $date => $record) {
        if (strpos($date, $selectedMonth) === 0) {
            $total += floatval($record['total_hours'] ?? 0);
        }
    }
    $chartData[] = [
        'name' => $user['name'],
        'hours' => $total
    ];
}
?>

<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>📊 גרף שעות חודשי</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container">
    <h2 class="text-center mb-4">📊 גרף שעות עבודה לפי חודש</h2>

    <form method="get" class="row justify-content-center mb-4">
      <div class="col-md-4">
        <label class="form-label">בחר חודש:</label>
        <input type="month" name="month" value="<?= $selectedMonth ?>" class="form-control" required>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="submit" class="btn btn-primary w-100">הצג גרף</button>
      </div>
    </form>

    <div class="card shadow p-4">
      <canvas id="hoursChart"></canvas>
    </div>
  </div>

  <script>
    const chartData = <?= json_encode($chartData) ?>;
    const labels = chartData.map(u => u.name);
    const hours = chartData.map(u => u.hours);

    new Chart(document.getElementById('hoursChart'), {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'סה"כ שעות עבודה',
          data: hours,
          backgroundColor: 'rgba(54, 162, 235, 0.6)',
          borderColor: 'rgba(54, 162, 235, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'שעות'
            }
          }
        }
      }
    });
  </script>
</body>
</html>
