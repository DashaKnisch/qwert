<?php
header('Content-Type: text/css; charset=utf-8');

$theme = $_COOKIE['theme'] ?? 'light';

$themes = [
    'light' => [
        'bg' => '#ffffff',
        'text' => '#000',
        'nav_bg' => '#f0f0f0',
        'nav_text' => '#000',
        'btn_bg' => '#e0e0e0',
        'btn_text' => '#000'
    ],
    'dark' => [
        'bg' => '#1e1e1e',
        'text' => '#f0f0f0',
        'nav_bg' => '#2a2a2a',
        'nav_text' => '#fff',
        'btn_bg' => '#333',
        'btn_text' => '#fff'
    ],
    'colorblind' => [
        'bg' => '#ffffe0',
        'text' => '#0000ff',
        'nav_bg' => '#ffffc0',
        'nav_text' => '#0000ff',
        'btn_bg' => '#fffc80',
        'btn_text' => '#0000ff'
    ]
];

$c = $themes[$theme] ?? $themes['light'];
?>

body {
    background-color: <?= $c['bg'] ?>;
    color: <?= $c['text'] ?>;
    font-family: Arial, sans-serif;
    transition: background-color 0.3s, color 0.3s;
}

nav {
    background-color: <?= $c['nav_bg'] ?>;
    padding: 10px;
}

nav a {
    color: <?= $c['nav_text'] ?>;
    text-decoration: none;
    margin-right: 15px;
}

button {
    background-color: <?= $c['btn_bg'] ?>;
    color: <?= $c['btn_text'] ?>;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
}

button:hover {
    opacity: 0.8;
}
