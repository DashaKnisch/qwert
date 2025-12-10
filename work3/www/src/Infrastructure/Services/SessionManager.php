<?php

namespace Infrastructure\Services;

class SessionManager {
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.save_handler', 'redis');
            ini_set('session.save_path', 'tcp://redis:6379');
            session_start();
        }
    }
    
    public function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }
    
    public function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }
    
    public function has(string $key): bool {
        return isset($_SESSION[$key]);
    }
    
    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }
    
    public function destroy(): void {
        session_destroy();
    }
    
    public function isLoggedIn(): bool {
        return $this->has('user');
    }
    
    public function getCurrentUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'username' => $this->get('user'),
            'name' => $this->get('name')
        ];
    }
}