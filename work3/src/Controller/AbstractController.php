<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

abstract class AbstractController 
{
    protected function render(string $template, array $parameters = []): Response
    {
        // Определяем путь к шаблонам для Docker и локального окружения
        $isDocker = file_exists('/var/www/templates');
        $templatesPath = $isDocker ? '/var/www/templates' : __DIR__ . '/../../templates';
        
        $templatePath = $templatesPath . '/' . $template;
        
        if (!file_exists($templatePath)) {
            throw new \Exception("Template not found: $template at $templatePath");
        }
        
        // Извлекаем переменные для шаблона
        extract($parameters);
        
        ob_start();
        include $templatePath;
        $content = ob_get_clean();
        
        return new Response($content);
    }
    
    protected function json(array $data, int $status = 200): JsonResponse
    {
        return new JsonResponse($data, $status);
    }
    
    protected function redirectToRoute(string $route, array $parameters = []): RedirectResponse
    {
        // Простая реализация редиректов
        $routes = [
            'home' => '/',
            'admin' => '/admin',
            'admin_logout' => '/admin/logout',
            'set_preferences' => '/set_cookie',
            'stats' => '/stats'
        ];
        
        $url = $routes[$route] ?? '/';
        
        if (!empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }
        
        return new RedirectResponse($url);
    }
    
    protected function createNotFoundException(string $message = 'Not Found'): \Exception
    {
        return new \Exception($message);
    }
}