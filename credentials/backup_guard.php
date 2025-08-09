<?php
// credentials/backup_guard.php
function can_run_backup(int $cooldownHours = 24): bool {
    $lockFile = __DIR__ . '/../storage/last_backup.txt';
    if (!is_dir(dirname($lockFile))) {
        mkdir(dirname($lockFile), 0777, true);
    }
    if (!file_exists($lockFile)) {
        return true; // أول مرة
    }
    $last = (int)trim(@file_get_contents($lockFile));
    $elapsedHours = (time() - $last) / 3600;
    return $elapsedHours >= $cooldownHours;
}

function mark_backup_ran(): void {
    $lockFile = __DIR__ . '/../storage/last_backup.txt';
    if (!is_dir(dirname($lockFile))) {
        mkdir(dirname($lockFile), 0777, true);
    }
    file_put_contents($lockFile, (string)time());
}