<?php
require_once 'autoload.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo "ID файла не указан";
    exit;
}

$db = \Database::getInstance();
$stmt = $db->prepare("SELECT filename, filedata FROM pdf_files WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$file = $result->fetch_assoc();

if (!$file) {
    http_response_code(404);
    echo "Файл не найден";
    exit;
}

header('Content-Description: File Transfer');
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $file['filename'] . '"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . strlen($file['filedata']));

echo $file['filedata'];
exit;
