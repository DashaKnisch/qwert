<?php

namespace Application\UseCases;

use Domain\Repositories\ProductRepositoryInterface;
use Domain\Entities\Product;

class CreateProductUseCase {
    private $productRepository;
    
    public function __construct(ProductRepositoryInterface $productRepository) {
        $this->productRepository = $productRepository;
    }
    
    public function execute(string $name, string $description, float $price): Product {
        // Бизнес-правила валидации
        if (empty($name)) {
            throw new \InvalidArgumentException("Product name is required");
        }
        
        if ($price < 0) {
            throw new \InvalidArgumentException("Price cannot be negative");
        }
        
        return $this->productRepository->create($name, $description, $price);
    }
}