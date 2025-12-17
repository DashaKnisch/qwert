<?php

namespace App\Controller;

use Application\UseCases\GetProductsUseCase;
use Application\UseCases\ManageUserPreferencesUseCase;
use App\Service\SessionManager;
use App\Service\CookieManager;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class HomeController extends AbstractController 
{
    private GetProductsUseCase $getProductsUseCase;
    private ManageUserPreferencesUseCase $managePreferencesUseCase;
    private SessionManager $sessionManager;
    private CookieManager $cookieManager;
    
    public function __construct(
        GetProductsUseCase $getProductsUseCase,
        ManageUserPreferencesUseCase $managePreferencesUseCase,
        SessionManager $sessionManager,
        CookieManager $cookieManager
    ) {
        $this->getProductsUseCase = $getProductsUseCase;
        $this->managePreferencesUseCase = $managePreferencesUseCase;
        $this->sessionManager = $sessionManager;
        $this->cookieManager = $cookieManager;
    }
    
    public function index(): Response 
    {
        $username = $this->cookieManager->getUsername($this->sessionManager);
        $products = $this->getProductsUseCase->execute();
        
        return $this->render('home/index.html.twig', [
            'username' => $username,
            'products' => $products,
            'theme' => $this->cookieManager->getTheme(),
            'language' => $this->cookieManager->getLanguage(),
            'texts' => $this->getTexts()
        ]);
    }
    
    public function setPreferences(): RedirectResponse 
    {
        try {
            $preferences = $this->managePreferencesUseCase->execute($_POST);
            
            if (!empty($preferences['username'])) {
                $this->sessionManager->set('name', $preferences['username']);
                $this->cookieManager->set('user', $preferences['username']);
            }
            
            $this->cookieManager->set('theme', $preferences['theme']);
            $this->cookieManager->set('lang', $preferences['language']);
            
            return $this->redirectToRoute('home');
        } catch (\InvalidArgumentException $e) {
            return $this->redirectToRoute('home', ['error' => $e->getMessage()]);
        }
    }
    
    private function getTexts(): array 
    {
        $language = $this->cookieManager->getLanguage();
        
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
                'home' => 'Главная'
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
                'home' => 'Home'
            ]
        ];
        
        return $texts[$language] ?? $texts['ru'];
    }
}