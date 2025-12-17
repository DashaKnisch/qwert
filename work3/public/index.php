<?php

// Определяем пути для Docker и локального окружения
$isDocker = file_exists('/var/www/www/autoload.php');
$wwwPath = $isDocker ? '/var/www/www' : __DIR__ . '/../www';
$srcPath = $isDocker ? '/var/www/src' : __DIR__ . '/../src';
$templatesPath = $isDocker ? '/var/www/templates' : __DIR__ . '/../templates';

// Подключаем автозагрузку существующего проекта
require_once $wwwPath . '/autoload.php';

// Простая автозагрузка для наших Symfony-подобных классов
spl_autoload_register(function ($class) use ($srcPath, $wwwPath) {
    // App классы (наши новые контроллеры)
    if (strpos($class, 'App\\') === 0) {
        $file = $srcPath . '/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
    
    // Symfony классы (наши заглушки)
    if (strpos($class, 'Symfony\\Component\\HttpFoundation\\') === 0) {
        // Все классы HttpFoundation в одном файле
        $file = $srcPath . '/Symfony/HttpFoundation/Response.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
    
    // Существующие классы проекта (старые namespace)
    if (strpos($class, 'Infrastructure\\') === 0 || 
        strpos($class, 'Domain\\') === 0 || 
        strpos($class, 'Application\\') === 0 || 
        strpos($class, 'InterfaceAdapters\\') === 0) {
        $file = $wwwPath . '/src/' . str_replace(['\\'], ['/'], $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
    
    // Database класс
    if ($class === 'Database') {
        $file = $wwwPath . '/config/Database.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});

// Простой роутер
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Убираем query string
$path = parse_url($requestUri, PHP_URL_PATH);

try {
    // Создаем простой контейнер сервисов
    $sessionManager = new \App\Service\SessionManager();
    $cookieManager = new \App\Service\CookieManager();
    $container = new \Infrastructure\DI\Container();
    
    // Создаем Request объект
    $request = new \Symfony\Component\HttpFoundation\Request();

    switch ($path) {
        case '/':
            $getProductsUseCase = $container->get(\Application\UseCases\GetProductsUseCase::class);
            $managePreferencesUseCase = $container->get(\Application\UseCases\ManageUserPreferencesUseCase::class);
            $controller = new \App\Controller\HomeController($getProductsUseCase, $managePreferencesUseCase, $sessionManager, $cookieManager);
            
            if ($requestMethod === 'POST') {
                $response = $controller->setPreferences();
            } else {
                $response = $controller->index();
            }
            break;
            
        case '/set_cookie':
            if ($requestMethod === 'POST') {
                $getProductsUseCase = $container->get(\Application\UseCases\GetProductsUseCase::class);
                $managePreferencesUseCase = $container->get(\Application\UseCases\ManageUserPreferencesUseCase::class);
                $controller = new \App\Controller\HomeController($getProductsUseCase, $managePreferencesUseCase, $sessionManager, $cookieManager);
                $response = $controller->setPreferences();
            } else {
                header('Location: /');
                exit;
            }
            break;
            
        case '/admin':
            $authUseCase = $container->get(\Application\UseCases\AuthenticateUserUseCase::class);
            $controller = new \App\Controller\AdminController($authUseCase, $sessionManager, $cookieManager);
            $response = $controller->index();
            break;
            
        case '/admin/login':
            if ($requestMethod === 'POST') {
                $authUseCase = $container->get(\Application\UseCases\AuthenticateUserUseCase::class);
                $controller = new \App\Controller\AdminController($authUseCase, $sessionManager, $cookieManager);
                $response = $controller->login();
            } else {
                header('Location: /admin');
                exit;
            }
            break;
            
        case '/admin/logout':
            $authUseCase = $container->get(\Application\UseCases\AuthenticateUserUseCase::class);
            $controller = new \App\Controller\AdminController($authUseCase, $sessionManager, $cookieManager);
            $response = $controller->logout();
            break;
            
        case '/api/products':
            $getProductsUseCase = $container->get(\Application\UseCases\GetProductsUseCase::class);
            $createProductUseCase = $container->get(\Application\UseCases\CreateProductUseCase::class);
            $productRepo = $container->get(\Domain\Repositories\ProductRepositoryInterface::class);
            $userRepo = $container->get(\Domain\Repositories\UserRepositoryInterface::class);
            $controller = new \App\Controller\ApiController($getProductsUseCase, $createProductUseCase, $productRepo, $userRepo);
            $response = $controller->products();
            break;
            
        case '/stats':
            $controller = new \App\Controller\StatsController();
            $response = $controller->index();
            break;
            
        case '/charts/chart1.php':
            $controller = new \App\Controller\ChartController();
            $response = $controller->chart1();
            break;
            
        case '/charts/chart2.php':
            $controller = new \App\Controller\ChartController();
            $response = $controller->chart2();
            break;
            
        case '/charts/chart3.php':
            $controller = new \App\Controller\ChartController();
            $response = $controller->chart3();
            break;
            
        default:
            // Проверяем маршрут для скачивания файлов
            if (preg_match('/^\/download\/(\d+)$/', $path, $matches)) {
                $controller = new \App\Controller\FileController();
                $response = $controller->download((int)$matches[1]);
                break;
            }
            
            // Проверяем, есть ли файл в www
            $filePath = $wwwPath . $path;
            if (file_exists($filePath) && is_file($filePath)) {
                // Отдаем статический файл
                $mimeType = mime_content_type($filePath);
                header('Content-Type: ' . $mimeType);
                readfile($filePath);
                exit;
            }
            
            // Fallback к старой системе
            $legacyFile = $wwwPath . $path;
            if (file_exists($legacyFile)) {
                include $legacyFile;
                exit;
            }
            
            http_response_code(404);
            echo "Page not found";
            exit;
    }
    
    // Отправляем ответ
    if (is_object($response)) {
        if (method_exists($response, 'send')) {
            $response->send();
        } else {
            echo $response;
        }
    } else {
        echo $response;
    }
    
} catch (Exception $e) {
    // В случае ошибки, fallback к старой системе
    $container = new \Infrastructure\DI\Container();
    $controller = $container->get(\InterfaceAdapters\Controllers\HomeController::class);
    $controller->index();
}
