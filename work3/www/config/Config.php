<?php

class Config {
    const THEMES = [
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
    
    const LANGUAGES = [
        'ru' => [
            'hello' => 'Привет',
            'store' => 'Магазин игрушек',
            'about' => 'О магазине',
            'promo' => 'Акции',
            'admin' => 'Админка',
            'theme_label' => 'Тема',
            'lang_label' => 'Язык',
            'save_btn' => 'Сохранить',
            'toy_list' => 'Список игрушек',
            'id' => 'ID',
            'name' => 'Название',
            'desc' => 'Описание',
            'price' => 'Цена',
            'register_label' => 'Введите имя',
            'register_btn' => 'Зарегистрироваться',
            'home' => 'Главная',
            'login_title' => 'Вход в админку',
            'login_btn' => 'Войти',
            'upload_pdf' => 'Загрузить PDF',
            'uploaded_pdf' => 'Загруженные файлы PDF',
            'logout' => 'Выйти'
        ],
        'en' => [
            'hello' => 'Hello',
            'store' => 'Toy Shop',
            'about' => 'About',
            'promo' => 'Promotions',
            'admin' => 'Admin Panel',
            'theme_label' => 'Theme',
            'lang_label' => 'Language',
            'save_btn' => 'Save',
            'toy_list' => 'Toy List',
            'id' => 'ID',
            'name' => 'Name',
            'desc' => 'Description',
            'price' => 'Price',
            'register_label' => 'Enter your name',
            'register_btn' => 'Register',
            'home' => 'Home',
            'login_title' => 'Admin Login',
            'login_btn' => 'Login',
            'upload_pdf' => 'Upload PDF',
            'uploaded_pdf' => 'Uploaded PDFs',
            'logout' => 'Logout'
        ]
    ];
    
    const SESSION_CONFIG = [
        'save_handler' => 'redis',
        'save_path' => 'tcp://redis:6379'
    ];
}