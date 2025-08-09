<?php
session_start();
require_once __DIR__ . '/classes/UserManager.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['username'])) {
    exit("❌ Invalid request. Username is required.");
}

$username = trim($_POST['username']);
$manager = new UserManager();
$user = $manager->getUser($username);

if (!$user) {
    exit("❌ User not found.");
}

$manager->deleteUser($username);

$_SESSION['deleted'] = true;
header('Location: list.php');
exit;
