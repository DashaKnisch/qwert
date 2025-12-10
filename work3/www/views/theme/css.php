body {
    background-color: <?= $theme['bg'] ?>;
    color: <?= $theme['text'] ?>;
    font-family: Arial, sans-serif;
    transition: background-color 0.3s, color 0.3s;
}

nav {
    background-color: <?= $theme['nav_bg'] ?>;
    padding: 10px;
}

nav a {
    color: <?= $theme['nav_text'] ?>;
    text-decoration: none;
    margin-right: 15px;
}

button {
    background-color: <?= $theme['btn_bg'] ?>;
    color: <?= $theme['btn_text'] ?>;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}

button:hover {
    opacity: 0.8;
}