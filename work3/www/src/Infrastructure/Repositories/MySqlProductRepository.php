<?php

namespace Infrastructure\Repositories;

use Domain\Repositories\ProductRepositoryInterface;
use Domain\Entities\Product;

class MySqlProductRepository implements ProductRepositoryInterface {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function findAll(): array {
        $result = $this->db->query("SELECT * FROM products ORDER BY name");
        $products = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = new Product(
                    $row['id'],
                    $row['name'],
                    $row['description'],
                    (float)$row['price']
                );
            }
        }
        
        return $products;
    }
    
    public function findById(int $id): ?Product {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!$row) {
            return null;
        }
        
        return new Product(
            $row['id'],
            $row['name'],
            $row['description'],
            (float)$row['price']
        );
    }
    
    public function save(Product $product): bool {
        if ($product->getId()) {
            // Update existing
            $stmt = $this->db->prepare("UPDATE products SET name=?, description=?, price=? WHERE id=?");
            $stmt->bind_param("ssdi", 
                $product->getName(),
                $product->getDescription(),
                $product->getPrice(),
                $product->getId()
            );
        } else {
            // Create new
            $stmt = $this->db->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd",
                $product->getName(),
                $product->getDescription(),
                $product->getPrice()
            );
        }
        
        return $stmt->execute();
    }
    
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function create(string $name, string $description, float $price): Product {
        $stmt = $this->db->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $description, $price);
        
        if (!$stmt->execute()) {
            throw new \RuntimeException("Failed to create product");
        }
        
        $id = $this->db->getConnection()->insert_id;
        return new Product($id, $name, $description, $price);
    }
}