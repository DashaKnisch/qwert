<?php
$mysqli = new mysqli("db", "appuser", "apppass", "appdb");
if ($mysqli->connect_error) {
    die("Ошибка подключения к БД: " . $mysqli->connect_error);
}
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Магазин игрушек</title>
<link rel="stylesheet" href="/static/style.css">
</head>
<body>
<nav>
  <a href="/static/info.html">О магазине</a> |
  <a href="/static/promo.html">Акции</a> |
  <a href="/admin.php">Админка</a>
</nav>

<h1>Список игрушек</h1>
<table border="1" cellpadding="6">
    <tr><th>ID</th><th>Название</th><th>Описание</th><th>Цена</th></tr>
<?php
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
    echo "<tr><td colspan='4'>Нет продуктов / ошибка запроса</td></tr>";
}
?>
</table>

</body>
</html>
