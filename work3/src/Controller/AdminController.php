<?php

namespace App\Controller;

use Application\UseCases\AuthenticateUserUseCase;
use App\Service\SessionManager;
use App\Service\CookieManager;
use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController 
{
    private AuthenticateUserUseCase $authenticateUserUseCase;
    private SessionManager $sessionManager;
    private CookieManager $cookieManager;
    
    public function __construct(
        AuthenticateUserUseCase $authenticateUserUseCase,
        SessionManager $sessionManager,
        CookieManager $cookieManager
    ) {
        $this->authenticateUserUseCase = $authenticateUserUseCase;
        $this->sessionManager = $sessionManager;
        $this->cookieManager = $cookieManager;
    }
    
    public function index(): Response 
    {
        if (isset($_GET['logout'])) {
            return $this->logout();
        }
        
        if (!$this->sessionManager->isLoggedIn()) {
            return $this->showLogin();
        }
        
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf_file'])) {
            $message = $this->handleFileUpload();
        }
        
        $currentUser = $this->sessionManager->getCurrentUser();
        $pdfFiles = $this->getPdfFiles();
        
        return $this->render('admin/dashboard.html.twig', [
            'currentUser' => $currentUser,
            'pdfFiles' => $pdfFiles,
            'message' => $message,
            'theme' => $this->cookieManager->getTheme(),
            'language' => $this->cookieManager->getLanguage(),
            'texts' => $this->getTexts()
        ]);
    }
    
    public function login(): Response 
    {
        $user = $this->authenticateUserUseCase->execute(
            $_POST['login'] ?? '',
            $_POST['password'] ?? ''
        );
        
        if ($user) {
            $this->sessionManager->set('user', $user->getUsername());
            $this->sessionManager->set('name', $user->getName());
            $this->cookieManager->set('username', $user->getName());
            return $this->redirectToRoute('admin');
        }
        
        return $this->render('admin/login.html.twig', [
            'error' => 'Неверный логин или пароль',
            'theme' => $this->cookieManager->getTheme(),
            'texts' => $this->getTexts()
        ]);
    }
    
    public function logout(): RedirectResponse 
    {
        $this->sessionManager->destroy();
        $this->cookieManager->delete('username');
        return $this->redirectToRoute('home');
    }
    
    private function showLogin(): Response 
    {
        return $this->render('admin/login.html.twig', [
            'error' => '',
            'theme' => $this->cookieManager->getTheme(),
            'texts' => $this->getTexts()
        ]);
    }
    
    private function handleFileUpload(): string 
    {
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $filename = $_FILES['pdf_file']['name'];
            $data = file_get_contents($_FILES['pdf_file']['tmp_name']);
            
            // Используем старый Database класс
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
    
    private function getPdfFiles(): array 
    {
        $db = \Database::getInstance();
        $result = $db->query("SELECT id, filename FROM pdf_files");
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    private function getTexts(): array 
    {
        $language = $this->cookieManager->getLanguage();
        
        $texts = [
            'ru' => [
                'login_title' => 'Вход в админку',
                'login_btn' => 'Войти',
                'upload_pdf' => 'Загрузить PDF',
                'uploaded_pdf' => 'Загруженные файлы PDF',
                'logout' => 'Выйти'
            ],
            'en' => [
                'login_title' => 'Admin Login',
                'login_btn' => 'Login',
                'upload_pdf' => 'Upload PDF',
                'uploaded_pdf' => 'Uploaded PDFs',
                'logout' => 'Logout'
            ]
        ];
        
        return $texts[$language] ?? $texts['ru'];
    }
}