<?php
$title = $texts['store'];
$content = ob_start();
?>

<h2><?= $texts['hello'] ?>, <?= htmlspecialchars($username) ?>!</h2>

<form method="post" action="index.php">
    <p>
        <?= $texts['theme_label'] ?>:
        <select name="theme">
            <option value="light" <?= $theme==='light'?'selected':'' ?>>Светлая</option>
            <option value="dark" <?= $theme==='dark'?'selected':'' ?>>Тёмная</option>
            <option value="colorblind" <?= $theme==='colorblind'?'selected':'' ?>>Для дальтоников</option>
        </select>

        <?= $texts['lang_label'] ?>:
        <select name="lang">
            <option value="ru" <?= $language==='ru'?'selected':'' ?>>Русский</option>
            <option value="en" <?= $language==='en'?'selected':'' ?>>English</option>
        </select>

        <input type="text" name="user" placeholder="<?= $texts['register_label'] ?>" 
               value="<?= htmlspecialchars($username!=='Гость'?$username:'') ?>">

        <button type="submit"><?= $texts['save_btn'] ?></button>
    </p>
</form>

<h1><?= $texts['toy_list'] ?></h1>
<table border="1" cellpadding="6">
    <tr>
        <th><?= $texts['id'] ?></th>
        <th><?= $texts['name'] ?></th>
        <th><?= $texts['desc'] ?></th>
        <th><?= $texts['price'] ?></th>
    </tr>
    <?php foreach ($products as $product): ?>
    <tr>
        <td><?= htmlspecialchars($product['id']) ?></td>
        <td><?= htmlspecialchars($product['name']) ?></td>
        <td><?= htmlspecialchars($product['description']) ?></td>
        <td>$<?= htmlspecialchars($product['price']) ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>