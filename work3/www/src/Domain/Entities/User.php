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
        
        $this->validateUsername($username);
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getUsername() {
        return $this->username;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getSurname() {
        return $this->surname;
    }
    
    public function getFullName() {
        return trim($this->name . ' ' . $this->surname);
    }
    
    public function verifyPassword($password) {
        return $this->password === $password;
    }
    
    public function updateProfile($name, $surname) {
        $this->name = $name;
        $this->surname = $surname;
    }
    
    private function validateUsername($username) {
        if (empty($username) || strlen($username) < 3) {
            throw new \InvalidArgumentException("Username must be at least 3 characters");
        }
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