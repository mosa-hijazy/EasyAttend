<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'en';
}

$supportedLanguages = ['en', 'ar', 'he'];
$lang = in_array($_SESSION['lang'], $supportedLanguages) ? $_SESSION['lang'] : 'en';

$basePath = __DIR__ . "/lang";
$mainFile = "$basePath/{$lang}.php";
$adminFile = "$basePath/{$lang}_admin.php";

// تحميل الترجمة العامة
$translations = file_exists($mainFile) ? include $mainFile : [];

// تحميل الترجمة الإضافية (مثلاً للأدمن)
if (file_exists($adminFile)) {
    $adminTranslations = include $adminFile;
    if (is_array($adminTranslations)) {
        $translations = array_merge($translations, $adminTranslations);
    }
}

// دالة الترجمة
function __($key, $replacements = []) {
    global $translations;
    $text = $translations[$key] ?? $key;
    foreach ($replacements as $k => $v) {
        $text = str_replace("{" . $k . "}", $v, $text);
    }
    return $text;
}
