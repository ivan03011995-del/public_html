<?php
require 'db.php';
// functions.php
// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

function файл(...$files) {
    foreach ($files as $file) {
        $path = __DIR__ . '/' . $file; // <-- гарантированно относительный путь
        if (file_exists($path)) {
            require $path;
           // echo "Файл $file найден!<br>";
        } else {
            echo "Файл $file не найден!<br>";
        }
    }
}
function сессия() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}
function переход($страница) {
    header("Location: $страница");
    exit; // Важно после редиректа завершить выполнение скрипта
}
?>