<?php

namespace Domain\Entities;

class Product {
    private $id;
    private $name;
    private $description;
    private $price;
    
    public function __construct($id, $name, $description, $price) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->price = $price;
        
        $this->validatePrice($price);
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getPrice() {
        return $this->price;
    }
    
    public function updatePrice($newPrice) {
        $this->validatePrice($newPrice);
        $this->price = $newPrice;
    }
    
    public function updateInfo($name, $description) {
        if (empty($name)) {
            throw new \InvalidArgumentException("Product name cannot be empty");
        }
        $this->name = $name;
        $this->description = $description;
    }
    
    private function validatePrice($price) {
        if ($price < 0) {
            throw new \InvalidArgumentException("Price cannot be negative");
        }
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price
        ];
    }
}