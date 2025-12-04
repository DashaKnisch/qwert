<?php
session_start();

// Данные пользователя
$username = $_SESSION['name'] ?? $_COOKIE['user'] ?? 'Гость';

// Тема и язык
$theme = $_COOKIE['theme'] ?? 'light';
$lang  = $_COOKIE['lang'] ?? 'ru';

// Тексты интерфейса
$texts = [
    'ru' => [
        'hello' => 'Привет',
        'store' => 'Магазин игрушек',
        'about' => 'О магазине',
        'promo' => 'Акции',
        'admin' => 'Админка',
        'theme_label' => 'Тема',
        'lang_label' => 'Язык',
        'save_btn' => 'Сохранить',
        'toy_list' => 'Список игрушек',
        'id' => 'ID',
        'name' => 'Название',
        'desc' => 'Описание',
        'price' => 'Цена',
        'register_label' => 'Введите имя',
        'register_btn' => 'Зарегистрироваться'
    ],
    'en' => [
        'hello' => 'Hello',
        'store' => 'Toy Shop',
        'about' => 'About',
        'promo' => 'Promotions',
        'admin' => 'Admin Panel',
        'theme_label' => 'Theme',
        'lang_label' => 'Language',
        'save_btn' => 'Save',
        'toy_list' => 'Toy List',
        'id' => 'ID',
        'name' => 'Name',
        'desc' => 'Description',
        'price' => 'Price',
        'register_label' => 'Enter your name',
        'register_btn' => 'Register'
    ]
];

$txt = $texts[$lang];
?>
<!doctype html>
<html lang="<?= htmlspecialchars($lang) ?>">
<head>
    <meta charset="utf-8">
    <title><?= $txt['store'] ?></title>
    <!-- Подключаем динамический CSS -->
    <link rel="stylesheet" href="theme.php?<?= time() ?>">

</head>
<body>
<nav>
    <a href="/static/info.html">О магазине</a> |
    <a href="/stats.php">Статистика</a> |
    <a href="/static/promo.html">Акции</a> |
    <a href="admin.php"><?= $txt['admin'] ?></a>
</nav>


<h2><?= $txt['hello'] ?>, <?= htmlspecialchars($username) ?>!</h2>

<form method="post" action="set_cookie.php">
    <p>
        <?= $txt['theme_label'] ?>:
        <select name="theme">
            <option value="light" <?= $theme==='light'?'selected':'' ?>>Светлая</option>
            <option value="dark" <?= $theme==='dark'?'selected':'' ?>>Тёмная</option>
            <option value="colorblind" <?= $theme==='colorblind'?'selected':'' ?>>Для дальтоников</option>
        </select>

        <?= $txt['lang_label'] ?>:
        <select name="lang">
            <option value="ru" <?= $lang==='ru'?'selected':'' ?>>Русский</option>
            <option value="en" <?= $lang==='en'?'selected':'' ?>>English</option>
        </select>

        <input type="text" name="user" placeholder="<?= $txt['register_label'] ?>" value="<?= htmlspecialchars($username!=='Гость'?$username:'') ?>">

        <button type="submit"><?= $txt['save_btn'] ?></button>
    </p>
</form>

<h1><?= $txt['toy_list'] ?></h1>
<table border="1" cellpadding="6">
    <tr>
        <th><?= $txt['id'] ?></th>
        <th><?= $txt['name'] ?></th>
        <th><?= $txt['desc'] ?></th>
        <th><?= $txt['price'] ?></th>
    </tr>
    <?php
    $mysqli = new mysqli("db", "appuser", "apppass", "appdb");
    $res = $mysqli->query("SELECT id, name, description, price FROM products");
    if ($res) {
        while ($p = $res->fetch_assoc()) {
            echo "<tr>
                    <td>".htmlspecialchars($p['id'])."</td>
                    <td>".htmlspecialchars($p['name'])."</td>
                    <td>".htmlspecialchars($p['description'])."</td>
                    <td>$".htmlspecialchars($p['price'])."</td>
                  </tr>";
        }
        $res->free();
    } else {
        echo "<tr><td colspan='4'>Ошибка запроса</td></tr>";
    }
    ?>
</table>
</body>
</html>
