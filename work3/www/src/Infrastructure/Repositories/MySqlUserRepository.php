<?php

namespace Infrastructure\Repositories;

use Domain\Repositories\UserRepositoryInterface;
use Domain\Entities\User;

class MySqlUserRepository implements UserRepositoryInterface {
    private $db;
    
    public function __construct($database) {
        $this->db = $database;
    }
    
    public function findAll(): array {
        $result = $this->db->query("SELECT * FROM users ORDER BY username");
        $users = [];
        
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $users[] = new User(
                    $row['id'],
                    $row['username'],
                    $row['password'],
                    $row['name'],
                    $row['surname']
                );
            }
        }
        
        return $users;
    }
    
    public function findById(int $id): ?User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!$row) {
            return null;
        }
        
        return new User(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['name'],
            $row['surname']
        );
    }
    
    public function findByUsername(string $username): ?User {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        
        if (!$row) {
            return null;
        }
        
        return new User(
            $row['id'],
            $row['username'],
            $row['password'],
            $row['name'],
            $row['surname']
        );
    }
    
    public function save(User $user): bool {
        if ($user->getId()) {
            // Update existing
            $stmt = $this->db->prepare("UPDATE users SET name=?, surname=? WHERE id=?");
            $stmt->bind_param("ssi", 
                $user->getName(),
                $user->getSurname(),
                $user->getId()
            );
        } else {
            // Create new
            $stmt = $this->db->prepare("INSERT INTO users (username, password, name, surname) VALUES (?, ?, ?, ?)");
            $userData = $user->toArray(true);
            $stmt->bind_param("ssss",
                $userData['username'],
                $userData['password'],
                $userData['name'],
                $userData['surname']
            );
        }
        
        return $stmt->execute();
    }
    
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    public function create(string $username, string $password, string $name, string $surname): User {
        $stmt = $this->db->prepare("INSERT INTO users (username, password, name, surname) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $password, $name, $surname);
        
        if (!$stmt->execute()) {
            throw new \RuntimeException("Failed to create user");
        }
        
        $id = $this->db->getConnection()->insert_id;
        return new User($id, $username, $password, $name, $surname);
    }
}