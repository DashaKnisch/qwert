<?php

namespace Symfony\Component\HttpFoundation;

class Response 
{
    protected $content;
    protected $statusCode;
    protected $headers;
    
    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $status;
        $this->headers = $headers;
    }
    
    public function send(): void
    {
        http_response_code($this->statusCode);
        
        if ($this->headers) {
            foreach ($this->headers as $name => $value) {
                header("$name: $value");
            }
        }
        
        echo $this->content;
    }
    
    public function setContent(string $content): void
    {
        $this->content = $content;
    }
}

class JsonResponse extends Response
{
    public function __construct(array $data, int $status = 200)
    {
        parent::__construct(json_encode($data), $status, ['Content-Type' => 'application/json']);
    }
}

class RedirectResponse extends Response
{
    private $url;
    
    public function __construct(string $url, int $status = 302)
    {
        $this->url = $url;
        parent::__construct('', $status, ['Location' => $url]);
    }
    
    public function send(): void
    {
        header('Location: ' . $this->url);
        exit;
    }
}

class Request
{
    public $request;
    public $query;
    public $files;
    
    public function __construct()
    {
        $this->request = new ParameterBag($_POST);
        $this->query = new ParameterBag($_GET);
        $this->files = new FileBag($_FILES);
    }
    
    public static function createFromGlobals(): self
    {
        return new self();
    }
    
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }
    
    public function getContent(): string
    {
        return file_get_contents('php://input');
    }
    
    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtoupper($method);
    }
}

class ParameterBag
{
    private $parameters;
    
    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }
    
    public function get(string $key, $default = null)
    {
        return $this->parameters[$key] ?? $default;
    }
    
    public function all(): array
    {
        return $this->parameters;
    }
}

class FileBag extends ParameterBag
{
    public function get(string $key, $default = null)
    {
        $file = parent::get($key, $default);
        if ($file && is_array($file)) {
            return new UploadedFile($file);
        }
        return $file;
    }
    
    public function has(string $key): bool
    {
        return isset($this->parameters[$key]);
    }
}

class UploadedFile
{
    private $file;
    
    public function __construct(array $file)
    {
        $this->file = $file;
    }
    
    public function getError(): int
    {
        return $this->file['error'];
    }
    
    public function getClientOriginalName(): string
    {
        return $this->file['name'];
    }
    
    public function getPathname(): string
    {
        return $this->file['tmp_name'];
    }
}