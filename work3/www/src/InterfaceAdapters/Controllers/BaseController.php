<?php

namespace InterfaceAdapters\Controllers;

use Infrastructure\Services\SessionManager;
use Infrastructure\Services\CookieManager;

abstract class BaseController {
    protected $sessionManager;
    protected $cookieManager;
    protected $theme;
    protected $language;
    
    public function __construct(SessionManager $sessionManager, CookieManager $cookieManager) {
        $this->sessionManager = $sessionManager;
        $this->cookieManager = $cookieManager;
        
        $this->theme = $this->cookieManager->getTheme();
        $this->language = $this->cookieManager->getLanguage();
    }
    
    protected function render(string $view, array $data = []): string {
        $data['theme'] = $this->theme;
        $data['language'] = $this->language;
        $data['texts'] = $this->getTexts();
        
        extract($data);
        
        ob_start();
        include "views/{$view}.php";
        return ob_get_clean();
    }
    
    protected function redirect(string $url): void {
        header("Location: $url");
        exit;
    }
    
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
    
    private function getTexts(): array {
        $texts = [
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
        
        return $texts[$this->language] ?? $texts['ru'];
    }
}