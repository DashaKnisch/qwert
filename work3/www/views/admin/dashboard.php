<?php
$title = $texts['admin'];
$content = ob_start();
?>

<h2><?= $texts['admin'] ?></h2>
<p>Добро пожаловать, <?= htmlspecialchars($currentUser['name']) ?> (<?= htmlspecialchars($currentUser['username']) ?>)</p>

<nav>
    <a href="/index.php"><?= $texts['home'] ?></a> |
    <a href="/static/info.html"><?= $texts['about'] ?></a> |
    <a href="/static/promo.html"><?= $texts['promo'] ?></a> |
    <a href="?logout=1"><?= $texts['logout'] ?></a>
</nav>

<h3><?= $texts['upload_pdf'] ?></h3>
<?php if ($message): ?>
    <p style='color:green;'><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="pdf_file" accept="application/pdf" required>
    <button type="submit"><?= $texts['upload_pdf'] ?></button>
</form>

<h3><?= $texts['uploaded_pdf'] ?></h3>
<ul>
<?php foreach ($pdfFiles as $file): ?>
    <li>
        <a href='download.php?id=<?= $file['id'] ?>'>
            <?= htmlspecialchars($file['filename']) ?>
        </a>
    </li>
<?php endforeach; ?>
</ul>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>