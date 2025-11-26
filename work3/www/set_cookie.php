<?php
session_start();

// Получаем данные из формы
$user  = $_POST['user'] ?? '';
$theme = $_POST['theme'] ?? 'light';
$lang  = $_POST['lang'] ?? 'ru';

// Сохраняем имя пользователя в сессии и cookie
if ($user) {
    $_SESSION['name'] = $user;
    setcookie('user', $user, time() + 3600*24*30, '/'); // 30 дней
}

// Сохраняем тему и язык в cookie
setcookie('theme', $theme, time() + 3600*24*30, '/');
setcookie('lang', $lang, time() + 3600*24*30, '/');

// Определяем страницу возврата (реферер)
$redirect = $_SERVER['HTTP_REFERER'] ?? '/index.php';

// Перенаправляем пользователя обратно
header("Location: $redirect");
exit;
