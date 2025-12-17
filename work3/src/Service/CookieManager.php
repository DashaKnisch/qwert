<?php

namespace App\Service;

class CookieManager 
{
    public function get(string $name, mixed $default = null): mixed 
    {
        return $_COOKIE[$name] ?? $default;
    }
    
    public function set(string $name, mixed $value, int $expire = 0): void 
    {
        $expire = $expire ?: time() + 3600 * 24 * 30; // 30 дней по умолчанию
        setcookie($name, (string)$value, $expire, '/');
        $_COOKIE[$name] = $value; // Для немедленного доступа
    }
    
    public function delete(string $name): void 
    {
        setcookie($name, '', time() - 3600, '/');
        unset($_COOKIE[$name]);
    }
    
    public function getTheme(): string 
    {
        return $this->get('theme', 'light');
    }
    
    public function getLanguage(): string 
    {
        return $this->get('lang', 'ru');
    }
    
    public function getUsername(SessionManager $sessionManager): ?string 
    {
        return $sessionManager->get('name') ?: $this->get('user');
    }
}