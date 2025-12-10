<?php

namespace Infrastructure\DI;

use Domain\Repositories\ProductRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use Infrastructure\Repositories\MySqlProductRepository;
use Infrastructure\Repositories\MySqlUserRepository;
use Infrastructure\Services\SessionManager;
use Infrastructure\Services\CookieManager;
use Application\UseCases\GetProductsUseCase;
use Application\UseCases\CreateProductUseCase;
use Application\UseCases\AuthenticateUserUseCase;
use Application\UseCases\ManageUserPreferencesUseCase;
use InterfaceAdapters\Controllers\HomeController;
use InterfaceAdapters\Controllers\AdminController;
use InterfaceAdapters\Controllers\ApiController;

class Container {
    private $services = [];
    private $singletons = [];
    
    public function __construct() {
        $this->registerServices();
    }
    
    private function registerServices(): void {
        // Database (Singleton)
        $this->services['database'] = function() {
            return \Database::getInstance();
        };
        
        // Repositories
        $this->services[ProductRepositoryInterface::class] = function() {
            return new MySqlProductRepository($this->get('database'));
        };
        
        $this->services[UserRepositoryInterface::class] = function() {
            return new MySqlUserRepository($this->get('database'));
        };
        
        // Services
        $this->services[SessionManager::class] = function() {
            return new SessionManager();
        };
        
        $this->services[CookieManager::class] = function() {
            return new CookieManager();
        };
        
        // Use Cases
        $this->services[GetProductsUseCase::class] = function() {
            return new GetProductsUseCase(
                $this->get(ProductRepositoryInterface::class)
            );
        };
        
        $this->services[CreateProductUseCase::class] = function() {
            return new CreateProductUseCase(
                $this->get(ProductRepositoryInterface::class)
            );
        };
        
        $this->services[AuthenticateUserUseCase::class] = function() {
            return new AuthenticateUserUseCase(
                $this->get(UserRepositoryInterface::class)
            );
        };
        
        $this->services[ManageUserPreferencesUseCase::class] = function() {
            return new ManageUserPreferencesUseCase();
        };
        
        // Controllers
        $this->services[HomeController::class] = function() {
            return new HomeController(
                $this->get(SessionManager::class),
                $this->get(CookieManager::class),
                $this->get(GetProductsUseCase::class),
                $this->get(ManageUserPreferencesUseCase::class)
            );
        };
        
        $this->services[AdminController::class] = function() {
            return new AdminController(
                $this->get(SessionManager::class),
                $this->get(CookieManager::class),
                $this->get(AuthenticateUserUseCase::class)
            );
        };
        
        $this->services[ApiController::class] = function() {
            return new ApiController(
                $this->get(SessionManager::class),
                $this->get(CookieManager::class),
                $this->get(GetProductsUseCase::class),
                $this->get(CreateProductUseCase::class),
                $this->get(ProductRepositoryInterface::class),
                $this->get(UserRepositoryInterface::class)
            );
        };
    }
    
    public function get(string $id) {
        if (isset($this->singletons[$id])) {
            return $this->singletons[$id];
        }
        
        if (!isset($this->services[$id])) {
            throw new \Exception("Service {$id} not found");
        }
        
        $service = $this->services[$id]();
        
        // Сохраняем как singleton для повторного использования
        $this->singletons[$id] = $service;
        
        return $service;
    }
}