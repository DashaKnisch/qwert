<?php
$title = $texts['login_title'];
$content = ob_start();
?>

<h2><?= $texts['login_title'] ?></h2>
<?php if ($error): ?>
    <p style='color:red;'><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="post">
    Логин: <input type="text" name="login" required><br><br>
    Пароль: <input type="password" name="password" required><br><br>
    <button type="submit"><?= $texts['login_btn'] ?></button>
</form>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>