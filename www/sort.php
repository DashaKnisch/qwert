<?php
require_once __DIR__ . '/sort_funcs.php';

$raw = isset($_GET['arr']) ? trim($_GET['arr']) : '';
$parts = array_filter(array_map('trim', explode(',', $raw)));
$nums = array_map('intval', $parts);
$sorted = quicksort($nums);
?>
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <title>Сортировка массива</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f4f9; color: #333; padding: 20px; }
    h1 { text-align: center; color: #4a76a8; }
    .array-box { background: #fff; padding: 15px; margin: 20px auto; border-radius: 8px; max-width: 600px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { color: #555; }
    p { font-size: 18px; word-wrap: break-word; }
  </style>
</head>
<body>
  <h1>Сортировка массива</h1>
  <div class="array-box">
    <h2>Исходный массив:</h2>
    <p><?= implode(', ', $nums) ?></p>
  </div>
  <div class="array-box">
    <h2>Отсортированный массив:</h2>
    <p><?= implode(', ', $sorted) ?></p>
  </div>
</body>
</html>
