<?php

namespace InterfaceAdapters\Controllers;

use Application\UseCases\GetProductsUseCase;
use Application\UseCases\CreateProductUseCase;
use Domain\Repositories\ProductRepositoryInterface;
use Domain\Repositories\UserRepositoryInterface;
use Infrastructure\Services\SessionManager;
use Infrastructure\Services\CookieManager;

class ApiController extends BaseController {
    private $getProductsUseCase;
    private $createProductUseCase;
    private $productRepository;
    private $userRepository;
    
    public function __construct(
        SessionManager $sessionManager,
        CookieManager $cookieManager,
        GetProductsUseCase $getProductsUseCase,
        CreateProductUseCase $createProductUseCase,
        ProductRepositoryInterface $productRepository,
        UserRepositoryInterface $userRepository
    ) {
        parent::__construct($sessionManager, $cookieManager);
        $this->getProductsUseCase = $getProductsUseCase;
        $this->createProductUseCase = $createProductUseCase;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }
    
    public function products(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($method) {
                case 'GET':
                    $products = $this->getProductsUseCase->execute();
                    $this->json($products);
                    break;
                    
                case 'POST':
                    $data = json_decode(file_get_contents("php://input"), true);
                    $product = $this->createProductUseCase->execute(
                        $data['name'], 
                        $data['description'], 
                        (float)$data['price']
                    );
                    $this->json(["status" => "created", "id" => $product->getId()]);
                    break;
                    
                case 'PUT':
                    $data = json_decode(file_get_contents("php://input"), true);
                    $product = $this->productRepository->findById($data['id']);
                    if ($product) {
                        $product->updateInfo($data['name'], $data['description']);
                        $product->updatePrice((float)$data['price']);
                        $result = $this->productRepository->save($product);
                        $this->json(["status" => $result ? "updated" : "error"]);
                    } else {
                        $this->json(["status" => "not_found"], 404);
                    }
                    break;
                    
                case 'DELETE':
                    $data = json_decode(file_get_contents("php://input"), true);
                    $result = $this->productRepository->delete($data['id']);
                    $this->json(["status" => $result ? "deleted" : "error"]);
                    break;
                    
                default:
                    $this->json(["error" => "Method not allowed"], 405);
            }
        } catch (\Exception $e) {
            $this->json(["error" => $e->getMessage()], 400);
        }
    }
    
    public function users(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        
        try {
            switch ($method) {
                case 'GET':
                    $users = $this->userRepository->findAll();
                    $userData = array_map(function($user) {
                        return $user->toArray(false); // без пароля
                    }, $users);
                    $this->json($userData);
                    break;
                    
                case 'POST':
                    $data = json_decode(file_get_contents("php://input"), true);
                    $user = $this->userRepository->create(
                        $data['username'],
                        $data['password'],
                        $data['name'],
                        $data['surname']
                    );
                    $this->json(["status" => "created", "id" => $user->getId()]);
                    break;
                    
                case 'PUT':
                    $data = json_decode(file_get_contents("php://input"), true);
                    $user = $this->userRepository->findById($data['id']);
                    if ($user) {
                        $user->updateProfile($data['name'], $data['surname']);
                        $result = $this->userRepository->save($user);
                        $this->json(["status" => $result ? "updated" : "error"]);
                    } else {
                        $this->json(["status" => "not_found"], 404);
                    }
                    break;
                    
                case 'DELETE':
                    $data = json_decode(file_get_contents("php://input"), true);
                    $result = $this->userRepository->delete($data['id']);
                    $this->json(["status" => $result ? "deleted" : "error"]);
                    break;
                    
                default:
                    $this->json(["error" => "Method not allowed"], 405);
            }
        } catch (\Exception $e) {
            $this->json(["error" => $e->getMessage()], 400);
        }
    }
}