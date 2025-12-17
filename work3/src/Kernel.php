<?php

namespace App;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class Kernel 
{
    private $env;
    private $debug;
    private $routes;
    
    public function __construct(string $env, bool $debug)
    {
        $this->env = $env;
        $this->debug = $debug;
        $this->initializeRoutes();
    }
    
    private function initializeRoutes(): void
    {
        $this->routes = new RouteCollection();
        
        // Главная страница
        $this->routes->add('home', new Route('/', [
            '_controller' => 'App\Controller\HomeController::index'
        ]));
        
        // Админ панель
        $this->routes->add('admin', new Route('/admin', [
            '_controller' => 'App\Controller\AdminController::index'
        ]));
        
        $this->routes->add('admin_login', new Route('/admin/login', [
            '_controller' => 'App\Controller\AdminController::login'
        ], [], [], '', [], ['POST']));
        
        $this->routes->add('admin_logout', new Route('/admin/logout', [
            '_controller' => 'App\Controller\AdminController::logout'
        ]));
        
        // Настройки
        $this->routes->add('set_preferences', new Route('/set_cookie', [
            '_controller' => 'App\Controller\HomeController::setPreferences'
        ], [], [], '', [], ['POST']));
        
        // API
        $this->routes->add('api_products', new Route('/api/products', [
            '_controller' => 'App\Controller\ApiController::products'
        ]));
        
        // Файлы
        $this->routes->add('file_download', new Route('/download/{id}', [
            '_controller' => 'App\Controller\FileController::download'
        ], ['id' => '\d+']));
        
        // Статистика
        $this->routes->add('stats', new Route('/stats', [
            '_controller' => 'App\Controller\StatsController::index'
        ]));
        
        // Графики
        $this->routes->add('chart1', new Route('/charts/chart1.php', [
            '_controller' => 'App\Controller\ChartController::chart1'
        ]));
        
        $this->routes->add('chart2', new Route('/charts/chart2.php', [
            '_controller' => 'App\Controller\ChartController::chart2'
        ]));
        
        $this->routes->add('chart3', new Route('/charts/chart3.php', [
            '_controller' => 'App\Controller\ChartController::chart3'
        ]));
    }
    
    public function handle(Request $request): Response
    {
        // Инициализируем сессию
        if (!$request->hasSession()) {
            $session = new Session(new NativeSessionStorage());
            $session->start();
            $request->setSession($session);
        }
        
        $context = new RequestContext();
        $context->fromRequest($request);
        
        $matcher = new UrlMatcher($this->routes, $context);
        
        try {
            $parameters = $matcher->match($request->getPathInfo());
            $controller = $parameters['_controller'];
            
            // Создаем контроллер и вызываем метод
            list($controllerClass, $method) = explode('::', $controller);
            
            $controllerInstance = $this->createController($controllerClass, $request);
            
            if (method_exists($controllerInstance, $method)) {
                $response = $controllerInstance->$method($request);
                
                if (!$response instanceof Response) {
                    $response = new Response($response);
                }
                
                return $response;
            }
            
        } catch (\Exception $e) {
            // Fallback к старой системе
            return $this->handleLegacyRequest($request);
        }
        
        return new Response('Not Found', 404);
    }
    
    private function createController(string $controllerClass, Request $request)
    {
        // Простая фабрика контроллеров
        $sessionManager = new \App\Service\SessionManager();
        $cookieManager = new \App\Service\CookieManager();
        
        switch ($controllerClass) {
            case 'App\Controller\HomeController':
                $container = new \Infrastructure\DI\Container();
                $getProductsUseCase = $container->get(\Application\UseCases\GetProductsUseCase::class);
                $managePreferencesUseCase = $container->get(\Application\UseCases\ManageUserPreferencesUseCase::class);
                return new \App\Controller\HomeController($getProductsUseCase, $managePreferencesUseCase, $sessionManager, $cookieManager);
                
            case 'App\Controller\AdminController':
                $container = new \Infrastructure\DI\Container();
                $authUseCase = $container->get(\Application\UseCases\AuthenticateUserUseCase::class);
                return new \App\Controller\AdminController($authUseCase, $sessionManager, $cookieManager);
                
            case 'App\Controller\ApiController':
                $container = new \Infrastructure\DI\Container();
                $getProductsUseCase = $container->get(\Application\UseCases\GetProductsUseCase::class);
                $createProductUseCase = $container->get(\Application\UseCases\CreateProductUseCase::class);
                $productRepo = $container->get(\Domain\Repositories\ProductRepositoryInterface::class);
                $userRepo = $container->get(\Domain\Repositories\UserRepositoryInterface::class);
                return new \App\Controller\ApiController($getProductsUseCase, $createProductUseCase, $productRepo, $userRepo);
                
            default:
                return new $controllerClass();
        }
    }
    
    private function handleLegacyRequest(Request $request): Response
    {
        $path = $request->getPathInfo();
        
        // Перенаправляем на старые файлы если нужно
        if ($path === '/') {
            $container = new \Infrastructure\DI\Container();
            $controller = $container->get(\InterfaceAdapters\Controllers\HomeController::class);
            ob_start();
            $controller->index();
            $content = ob_get_clean();
            return new Response($content);
        }
        
        return new Response('Not Found', 404);
    }
    
    public function terminate(Request $request, Response $response): void
    {
        // Cleanup if needed
    }
}