<?php

namespace Domain\Repositories;

use Domain\Entities\Product;

interface ProductRepositoryInterface {
    public function findAll(): array;
    public function findById(int $id): ?Product;
    public function save(Product $product): bool;
    public function delete(int $id): bool;
    public function create(string $name, string $description, float $price): Product;
}