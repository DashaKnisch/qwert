<!doctype html>
<html lang="<?= htmlspecialchars($language) ?>">
<head>
    <meta charset="utf-8">
    <title><?= $title ?? $texts['store'] ?></title>
    <link rel="stylesheet" href="theme.php?<?= time() ?>">
    <?= $additionalHead ?? '' ?>
</head>
<body class="<?= htmlspecialchars($theme) ?>">
    <nav>
        <a href="/index.php"><?= $texts['home'] ?></a> |
        <a href="/static/info.html"><?= $texts['about'] ?></a> |
        <a href="/stats.php">Статистика</a> |
        <a href="/static/promo.html"><?= $texts['promo'] ?></a> |
        <a href="/admin.php"><?= $texts['admin'] ?></a>
    </nav>
    
    <main>
        <?= $content ?>
    </main>
</body>
</html>