<?php
header("Content-Type: application/json; charset=utf-8");
$mysqli = new mysqli("db", "appuser", "apppass", "appdb");

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "DB connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        $res = $mysqli->query("SELECT * FROM products");
        echo json_encode($res->fetch_all(MYSQLI_ASSOC));
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $mysqli->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $data['name'], $data['description'], $data['price']);
        $stmt->execute();
        echo json_encode(["status" => "created"]);
        break;


    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $mysqli->prepare("UPDATE products SET name=?, description=?, price=? WHERE id=?");
        $stmt->bind_param("ssdi", $data['name'], $data['description'], $data['price'], $data['id']);
        $stmt->execute();
        echo json_encode(["status" => "updated"]);
        break;

    case 'DELETE':
        $data = json_decode(file_get_contents("php://input"), true);
        $stmt = $mysqli->prepare("DELETE FROM products WHERE id=?");
        $stmt->bind_param("i", $data['id']);
        $stmt->execute();
        echo json_encode(["status" => "deleted"]);
        break;

    default:
        http_response_code(405);
}
?>
