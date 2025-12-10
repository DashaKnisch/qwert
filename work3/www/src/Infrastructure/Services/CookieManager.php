<?php

namespace Infrastructure\Services;

class CookieManager {
    const COOKIE_LIFETIME = 3600 * 24 * 30; // 30 days
    
    public function set(string $name, string $value, int $lifetime = self::COOKIE_LIFETIME): void {
        setcookie($name, $value, time() + $lifetime, '/');
    }
    
    public function get(string $name, string $default = ''): string {
        return $_COOKIE[$name] ?? $default;
    }
    
    public function has(string $name): bool {
        return isset($_COOKIE[$name]);
    }
    
    public function delete(string $name): void {
        setcookie($name, "", time() - 3600, "/");
    }
    
    public function getTheme(): string {
        return $this->get('theme', 'light');
    }
    
    public function getLanguage(): string {
        return $this->get('lang', 'ru');
    }
    
    public function getUsername(SessionManager $sessionManager): string {
        $sessionUser = $sessionManager->get('name');
        if ($sessionUser) {
            return $sessionUser;
        }
        
        return $this->get('user', 'Гость');
    }
}