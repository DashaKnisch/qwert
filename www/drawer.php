<?php
require_once __DIR__ . '/drawer_helper.php';

$num = isset($_GET['num']) && $_GET['num'] !== '' ? (int)$_GET['num'] : null;

?>
<!doctype html>
<head>
  <meta charset="utf-8">
  <title>Drawer</title>
</head>
<body>
<?php
if ($num === null) {
    echo "<p>Пример: ?num=123456</p>";
} else {
    echo render_svg_from_code($num);
}
?>
</body>
</html>
