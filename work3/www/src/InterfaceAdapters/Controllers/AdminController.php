<?php

namespace InterfaceAdapters\Controllers;

use Application\UseCases\AuthenticateUserUseCase;
use Infrastructure\Services\SessionManager;
use Infrastructure\Services\CookieManager;

class AdminController extends BaseController {
    private $authenticateUserUseCase;
    
    public function __construct(
        SessionManager $sessionManager,
        CookieManager $cookieManager,
        AuthenticateUserUseCase $authenticateUserUseCase
    ) {
        parent::__construct($sessionManager, $cookieManager);
        $this->authenticateUserUseCase = $authenticateUserUseCase;
    }
    
    public function index(): void {
        if (isset($_GET['logout'])) {
            $this->logout();
        }
        
        if (!$this->sessionManager->isLoggedIn()) {
            $this->showLogin();
            return;
        }
        
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
            $message = $this->handleFileUpload();
        }
        
        $currentUser = $this->sessionManager->getCurrentUser();
        $pdfFiles = $this->getPdfFiles(); // Упрощенная версия
        
        echo $this->render('admin/dashboard', [
            'currentUser' => $currentUser,
            'pdfFiles' => $pdfFiles,
            'message' => $message
        ]);
    }
    
    private function showLogin(): void {
        $error = '';
        
        if (isset($_POST['login'], $_POST['password'])) {
            $user = $this->authenticateUserUseCase->execute($_POST['login'], $_POST['password']);
            
            if ($user) {
                $this->sessionManager->set('user', $user->getUsername());
                $this->sessionManager->set('name', $user->getName());
                $this->cookieManager->set('username', $user->getName());
                $this->redirect('admin.php');
            } else {
                $error = "Неверный логин или пароль";
            }
        }
        
        echo $this->render('admin/login', ['error' => $error]);
    }
    
    private function logout(): void {
        $this->sessionManager->destroy();
        $this->cookieManager->delete('username');
        $this->redirect('index.php');
    }
    
    private function handleFileUpload(): string {
        if ($_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $filename = $_FILES['pdf_file']['name'];
            $data = file_get_contents($_FILES['pdf_file']['tmp_name']);
            
            // Здесь должен быть Use Case для загрузки файлов
            // Упрощенная версия для демонстрации
            $db = \Database::getInstance();
            $stmt = $db->prepare("INSERT INTO pdf_files (filename, filedata) VALUES (?, ?)");
            $null = null;
            $stmt->bind_param("sb", $filename, $null);
            $stmt->send_long_data(1, $data);
            
            if ($stmt->execute()) {
                return "PDF успешно загружен!";
            }
        }
        return "Ошибка загрузки. Проверьте размер файла и формат.";
    }
    
    private function getPdfFiles(): array {
        // Упрощенная версия - в реальности должен быть Use Case
        $db = \Database::getInstance();
        $result = $db->query("SELECT id, filename FROM pdf_files");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
}