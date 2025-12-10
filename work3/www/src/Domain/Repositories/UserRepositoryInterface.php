<?php

namespace Domain\Repositories;

use Domain\Entities\User;

interface UserRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?User;
    public function findByUsername(string $username): ?User;
    public function save(User $user): bool;
    public function delete(int $id): bool;
    public function create(string $username, string $password, string $name, string $surname): User;
}