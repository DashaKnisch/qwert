<?php

namespace InterfaceAdapters\Controllers;

use Application\UseCases\GetProductsUseCase;
use Application\UseCases\ManageUserPreferencesUseCase;
use Infrastructure\Services\SessionManager;
use Infrastructure\Services\CookieManager;

class HomeController extends BaseController {
    private $getProductsUseCase;
    private $managePreferencesUseCase;
    
    public function __construct(
        SessionManager $sessionManager,
        CookieManager $cookieManager,
        GetProductsUseCase $getProductsUseCase,
        ManageUserPreferencesUseCase $managePreferencesUseCase
    ) {
        parent::__construct($sessionManager, $cookieManager);
        $this->getProductsUseCase = $getProductsUseCase;
        $this->managePreferencesUseCase = $managePreferencesUseCase;
    }
    
    public function index(): void {
        // Обрабатываем POST запрос для настроек
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->setPreferences();
            return;
        }
        
        $username = $this->cookieManager->getUsername($this->sessionManager);
        $products = $this->getProductsUseCase->execute();
        
        echo $this->render('home/index', [
            'username' => $username,
            'products' => $products
        ]);
    }
    
    public function setPreferences(): void {
        try {
            $preferences = $this->managePreferencesUseCase->execute($_POST);
            
            if (!empty($preferences['username'])) {
                $this->sessionManager->set('name', $preferences['username']);
                $this->cookieManager->set('user', $preferences['username']);
            }
            
            $this->cookieManager->set('theme', $preferences['theme']);
            $this->cookieManager->set('lang', $preferences['language']);
            
            $this->redirect('index.php');
        } catch (\InvalidArgumentException $e) {
            // В реальном приложении здесь была бы обработка ошибок
            $this->redirect('index.php?error=' . urlencode($e->getMessage()));
        }
    }
}