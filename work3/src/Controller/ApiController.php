<?php

namespace App\Controller;

use Application\UseCases\GetProductsUseCase;
use Application\UseCases\CreateProductUseCase;
use Domain\Repositories\ProductRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiController extends AbstractController 
{
    private GetProductsUseCase $getProductsUseCase;
    private CreateProductUseCase $createProductUseCase;
    private ProductRepositoryInterface $productRepository;
    private UserRepositoryInterface $userRepository;
    
    public function __construct(
        GetProductsUseCase $getProductsUseCase,
        CreateProductUseCase $createProductUseCase,
        ProductRepositoryInterface $productRepository,
        UserRepositoryInterface $userRepository
    ) {
        $this->getProductsUseCase = $getProductsUseCase;
        $this->createProductUseCase = $createProductUseCase;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }
    
    public function products(): JsonResponse 
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($method) {
                case 'GET':
                    $products = $this->getProductsUseCase->execute();
                    return $this->json($products);
                    
                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $product = $this->createProductUseCase->execute(
                        $data['name'], 
                        $data['description'], 
                        (float)$data['price']
                    );
                    return $this->json(["status" => "created", "id" => $product->getId()]);
                    
                case 'PUT':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $product = $this->productRepository->findById($data['id']);
                    if ($product) {
                        $product->updateInfo($data['name'], $data['description']);
                        $product->updatePrice((float)$data['price']);
                        $result = $this->productRepository->save($product);
                        return $this->json(["status" => $result ? "updated" : "error"]);
                    } else {
                        return $this->json(["status" => "not_found"], 404);
                    }
                    
                case 'DELETE':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $result = $this->productRepository->delete($data['id']);
                    return $this->json(["status" => $result ? "deleted" : "error"]);
                    
                default:
                    return $this->json(["error" => "Method not allowed"], 405);
            }
        } catch (\Exception $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
    
    public function users(): JsonResponse 
    {
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($method) {
                case 'GET':
                    $users = $this->userRepository->findAll();
                    $userData = array_map(function($user) {
                        return $user->toArray(false);
                    }, $users);
                    return $this->json($userData);
                    
                case 'POST':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $user = $this->userRepository->create(
                        $data['username'],
                        $data['password'],
                        $data['name'],
                        $data['surname']
                    );
                    return $this->json(["status" => "created", "id" => $user->getId()]);
                    
                case 'PUT':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $user = $this->userRepository->findById($data['id']);
                    if ($user) {
                        $user->updateProfile($data['name'], $data['surname']);
                        $result = $this->userRepository->save($user);
                        return $this->json(["status" => $result ? "updated" : "error"]);
                    } else {
                        return $this->json(["status" => "not_found"], 404);
                    }
                    
                case 'DELETE':
                    $data = json_decode(file_get_contents('php://input'), true);
                    $result = $this->userRepository->delete($data['id']);
                    return $this->json(["status" => $result ? "deleted" : "error"]);
                    
                default:
                    return $this->json(["error" => "Method not allowed"], 405);
            }
        } catch (\Exception $e) {
            return $this->json(["error" => $e->getMessage()], 400);
        }
    }
}