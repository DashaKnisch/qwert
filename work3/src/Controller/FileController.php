<?php

namespace App\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends AbstractController 
{
    public function download(int $id): Response 
    {
        $db = \Database::getInstance();
        $stmt = $db->prepare("SELECT filename, filedata FROM pdf_files WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $file = $result->fetch_assoc();

        if (!$file) {
            http_response_code(404);
            return new Response('Файл не найден', 404);
        }

        $response = new Response($file['filedata'], 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $file['filename'] . '"',
            'Content-Length' => strlen($file['filedata'])
        ]);

        return $response;
    }
}