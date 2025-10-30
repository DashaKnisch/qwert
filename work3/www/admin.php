<?php
session_start();

$mysqli = new mysqli("db", "appuser", "apppass", "appdb");
if ($mysqli->connect_error) {
    die("Ошибка подключения к БД: " . $mysqli->connect_error);
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php"); 
    exit;
}


if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

if (isset($_POST['login'], $_POST['password'])) {
    $user = $_POST['login'];
    $pass = $_POST['password'];
    $stmt = $mysqli->prepare("SELECT password, name FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($dbpass, $name);
    $found = $stmt->fetch();
    $stmt->close();

    if ($found && $pass === $dbpass) {
        $_SESSION['user'] = $user;
        $_SESSION['name'] = $name;
    } else {
        $error = "Неверный логин или пароль";
    }
}

if (!isset($_SESSION['user'])):
?>
<!doctype html>
<html lang="ru">
<head><meta charset="utf-8"><title>Вход в админку</title></head>
<body>
<h2>Вход в админку</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
  Логин: <input type="text" name="login"><br>
  Пароль: <input type="password" name="password"><br>
  <button type="submit">Войти</button>
</form>
</body>
</html>
<?php
exit;
endif;
?>

<!doctype html>
<html lang="ru">
<head><meta charset="utf-8"><title>Админка</title></head>
<body>
<h2>Админка</h2>

<?php


echo "<p>Добро пожаловать, " 
     . htmlspecialchars($_SESSION['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') 
     . " (" 
     . htmlspecialchars($_SESSION['user'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') 
     . ")</p>";
?>

<h3>Доступные команды</h3>
<ul>
<?php
$allowed = [
    'whoami' => 'whoami',
    'id'     => 'id',
    'ps'     => 'ps aux | head -n 30',
    'ls'     => 'ls -la /var/www/app'
];

foreach ($allowed as $key => $cmd) {
    echo '<li><a href="?cmd=' . urlencode($key) . '">' . htmlspecialchars($key, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</a></li>';
}

if (isset($_GET['cmd']) && isset($allowed[$_GET['cmd']])) {
    echo "<h4>Результат команды: " . htmlspecialchars($_GET['cmd'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . "</h4>";
    echo "<pre>" . shell_exec($allowed[$_GET['cmd']] . " 2>&1") . "</pre>";
}
?>
</ul>

<p><a href="?phpinfo=1">Показать phpinfo()</a></p>
<p><a href="?logout=1">Выйти на главную</a></p>

<?php
if (isset($_GET['phpinfo'])) {
    phpinfo();
}
?>
</body>
</html>
