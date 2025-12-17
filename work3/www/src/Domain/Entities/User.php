<?php

namespace Domain\Entities;

class User {
    private $id;
    private $username;
    private $password;
    private $name;
    private $surname;
    
    public function __construct($id, $username, $password, $name, $surname) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->name = $name;
        $this->surname = $surname;
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getPassword() {
        return $this->password;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getSurname() {
        return $this->surname;
    }
    
    public function updateProfile($name, $surname) {
        $this->name = $name;
        $this->surname = $surname;
    }
    
    public function verifyPassword($password) {
        // Сначала пробуем хешированный пароль
        if (password_verify($password, $this->password)) {
            return true;
        }
        
        // Если не работает, проверяем обычный текст (для совместимости)
        return $password === $this->password;
    }
    
    public function toArray($includePassword = false) {
        $data = [
            'id' => $this->id,
            'username' => $this->username,
            'name' => $this->name,
            'surname' => $this->surname
        ];
        
        if ($includePassword) {
            $data['password'] = $this->password;
        }
        
        return $data;
    }
}