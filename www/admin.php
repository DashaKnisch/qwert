<?php
require_once __DIR__ . '/admin_utils.php';
$allowed = get_allowed_commands();
$cmd = isset($_GET['cmd']) ? $_GET['cmd'] : null;
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Админка сервера</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f9; color: #333; padding: 20px; }
    h2 { color: #4a76a8; text-align: center; }
    form { text-align: center; margin-bottom: 20px; }
    select, button { font-size: 16px; padding: 5px 10px; margin: 0 5px; }
    .output { background: #fff; padding: 15px; border-radius: 8px; max-width: 800px; margin: 0 auto 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); white-space: pre-wrap; word-wrap: break-word; }
    a { text-decoration: none; color: #4a76a8; display: block; text-align: center; margin-top: 10px; }
  </style>
</head>
<body>
  <h2>Админка сервера</h2>
  <form method="get">
    <select name="cmd">
      <?php foreach($allowed as $key=>$info): ?>
        <option value="<?=htmlspecialchars($key)?>" <?= $cmd===$key ? 'selected':'' ?>><?=htmlspecialchars($info['label'])?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Выполнить</button>
  </form>
  <?php
  if ($cmd && array_key_exists($cmd, $allowed)) {
      echo '<div class="output"><strong>Команда: '.htmlspecialchars($allowed[$cmd]['label']).'</strong>' . PHP_EOL;
      $out = run_allowed_command($cmd);
      echo htmlspecialchars($out) . '</div>';
  }
  echo '<a href="?phpinfo=1">Показать phpinfo()</a>';
  if (isset($_GET['phpinfo'])) { ob_start(); phpinfo(); echo '<div class="output">'.ob_get_clean().'</div>'; }
  ?>
</body>
</html>
