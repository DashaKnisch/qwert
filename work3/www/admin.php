<?php
// Сессии через Redis
ini_set('session.save_handler', 'redis');
ini_set('session.save_path', 'tcp://redis:6379');
session_start();

// Подключение к базе
$mysqli = new mysqli("db", "appuser", "apppass", "appdb");
if ($mysqli->connect_error) {
    die("Ошибка подключения к БД: " . $mysqli->connect_error);
}

// Тема и язык
$theme = $_COOKIE['theme'] ?? 'light';
$lang  = $_COOKIE['lang'] ?? 'ru';

// Тексты интерфейса
$texts = [
    'ru'=>['home'=>'Главная','about'=>'О магазине','promo'=>'Акции','admin'=>'Админка','login_title'=>'Вход в админку','login_btn'=>'Войти','upload_pdf'=>'Загрузить PDF','uploaded_pdf'=>'Загруженные файлы PDF','logout'=>'Выйти'],
    'en'=>['home'=>'Home','about'=>'About','promo'=>'Promotions','admin'=>'Admin Panel','login_title'=>'Admin Login','login_btn'=>'Login','upload_pdf'=>'Upload PDF','uploaded_pdf'=>'Uploaded PDFs','logout'=>'Logout']
];
$txt = $texts[$lang];

// Выход
if (isset($_GET['logout'])) {
    session_destroy();
    setcookie("username", "", time() - 3600, "/");
    header("Location: index.php");
    exit;
}

// Обработка логина
$error = '';
if (isset($_POST['login'], $_POST['password'])) {
    $user = $_POST['login'];
    $pass = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT password, name FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $stmt->bind_result($dbpass, $name);
    $found = $stmt->fetch();
    $stmt->close();

    if (!$found) {
        $error = "Пользователь не найден в базе";
    } elseif ($pass === $dbpass) {
        $_SESSION['user'] = $user;
        $_SESSION['name'] = $name;
        setcookie("username", $name, time() + 3600*24*30, "/");
    } else {
        $error = "Неверный логин или пароль";
    }
}

// Если не залогинен, показываем форму
if (!isset($_SESSION['user'])):
?>
<!doctype html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="utf-8">
    <title><?= $txt['login_title'] ?></title>
    <link rel="stylesheet" href="theme.php?<?= time() ?>">
</head>
<body class="<?= htmlspecialchars($theme) ?>">
<h2><?= $txt['login_title'] ?></h2>
<?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
<form method="post">
    Логин: <input type="text" name="login" required><br>
    Пароль: <input type="password" name="password" required><br>
    <button type="submit"><?= $txt['login_btn'] ?></button>
</form>
</body>
</html>
<?php exit; endif; ?>

<?php
// Обработка загрузки PDF
$msg = '';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_FILES['pdf_file'])) {
    if ($_FILES['pdf_file']['error']===UPLOAD_ERR_OK) {
        $filename = $_FILES['pdf_file']['name'];
        $data = file_get_contents($_FILES['pdf_file']['tmp_name']);

        $stmt = $mysqli->prepare("INSERT INTO pdf_files (filename, filedata) VALUES (?, ?)");
        $null = null;
        $stmt->bind_param("sb", $filename, $null);
        $stmt->send_long_data(1, $data);
        $stmt->execute();
        $stmt->close();

        $msg = "PDF успешно загружен!";
    } else {
        $msg = "Ошибка загрузки. Проверьте размер файла и формат.";
    }
}
?>

<!doctype html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="utf-8">
    <title><?= $txt['admin'] ?></title>
    <link rel="stylesheet" href="theme.php?<?= time() ?>">
</head>
<body class="<?= htmlspecialchars($theme) ?>">

<nav>
    <a href="/index.php"><?= $txt['home'] ?></a> |
    <a href="/static/info.html"><?= $txt['about'] ?></a> |
    <a href="/static/promo.html"><?= $txt['promo'] ?></a> |
    <a href="?logout=1"><?= $txt['logout'] ?></a>
</nav>

<h2><?= $txt['admin'] ?></h2>
<p>Добро пожаловать, <?= htmlspecialchars($_SESSION['name']) ?> (<?= htmlspecialchars($_SESSION['user']) ?>)</p>

<h3><?= $txt['upload_pdf'] ?></h3>
<?php if ($msg) echo "<p style='color:green;'>$msg</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <input type="file" name="pdf_file" accept="application/pdf" required>
    <button type="submit"><?= $txt['upload_pdf'] ?></button>
</form>

<h3><?= $txt['uploaded_pdf'] ?></h3>
<ul>
<?php
$res = $mysqli->query("SELECT id, filename FROM pdf_files");
while ($row = $res->fetch_assoc()) {
    echo "<li><a href='download.php?id={$row['id']}'>".htmlspecialchars($row['filename'])."</a></li>";
}
?>
</ul>

</body>
</html>
