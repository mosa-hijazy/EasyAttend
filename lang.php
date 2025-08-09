<?php
if (!isset($_SESSION)) session_start();

$langCode = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
$_SESSION['lang'] = $langCode;

$langFile = __DIR__ . "/lang/$langCode.php";
if (!file_exists($langFile)) {
  $langFile = __DIR__ . "/lang/en.php";
}

$lang = require $langFile;

function __($key) {
  global $lang;
  return $lang[$key] ?? $key;
}
