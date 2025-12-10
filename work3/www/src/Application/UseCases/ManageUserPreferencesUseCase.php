<?php

namespace Application\UseCases;

class ManageUserPreferencesUseCase {
    public function execute(array $preferences): array {
        // Бизнес-логика валидации настроек
        $validThemes = ['light', 'dark', 'colorblind'];
        $validLanguages = ['ru', 'en'];
        
        $theme = $preferences['theme'] ?? 'light';
        $language = $preferences['lang'] ?? 'ru';
        $username = $preferences['user'] ?? '';
        
        if (!in_array($theme, $validThemes)) {
            $theme = 'light';
        }
        
        if (!in_array($language, $validLanguages)) {
            $language = 'ru';
        }
        
        // Валидация имени пользователя
        if (!empty($username) && strlen($username) < 2) {
            throw new \InvalidArgumentException("Username must be at least 2 characters");
        }
        
        return [
            'theme' => $theme,
            'language' => $language,
            'username' => $username
        ];
    }
}