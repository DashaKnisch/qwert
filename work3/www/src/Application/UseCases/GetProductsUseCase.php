<?php

namespace Application\UseCases;

use Domain\Repositories\ProductRepositoryInterface;

class GetProductsUseCase {
    private $productRepository;
    
    public function __construct(ProductRepositoryInterface $productRepository) {
        $this->productRepository = $productRepository;
    }
    
    public function execute(): array {
        $products = $this->productRepository->findAll();
        
        // Бизнес-логика: сортировка по цене
        usort($products, function($a, $b) {
            return $a->getPrice() <=> $b->getPrice();
        });
        
        return array_map(function($product) {
            return $product->toArray();
        }, $products);
    }
}