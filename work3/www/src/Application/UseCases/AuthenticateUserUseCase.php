<?php

namespace Application\UseCases;

use Domain\Repositories\UserRepositoryInterface;
use Domain\Entities\User;

class AuthenticateUserUseCase {
    private $userRepository;
    
    public function __construct(UserRepositoryInterface $userRepository) {
        $this->userRepository = $userRepository;
    }
    
    public function execute(string $username, string $password): ?User {
        $user = $this->userRepository->findByUsername($username);
        
        if (!$user) {
            return null;
        }
        
        if (!$user->verifyPassword($password)) {
            return null;
        }
        
        return $user;
    }
}