<?php
// Include database configuration
include '../config/config.php';

// Set headers for JSON response
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Origin: *");

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Get request data
$data = json_decode(file_get_contents("php://input"), true);

// Switch based on HTTP method
switch ($method) {
    case 'GET':
        if (isset($_GET['review_id'])) {
            // Fetch a single review by ID
            $stmt = $db->prepare("SELECT * FROM reviews WHERE review_id = ?");
            $stmt->execute([$_GET['review_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Fetch all reviews
            $stmt = $db->query("SELECT * FROM reviews");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode($result);
        break;

    case 'POST':
        if (!empty($data['user_id']) && !empty($data['beach_id']) && isset($data['rating'])) {
            // Insert a new review
            $stmt = $db->prepare("
                INSERT INTO reviews (user_id, beach_id, rating, review_text)
                VALUES (?, ?, ?, ?)
            ");
            $success = $stmt->execute([
                $data['user_id'],
                $data['beach_id'],
                $data['rating'],
                $data['review_text'] ?? null
            ]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        if (!empty($data['review_id']) && isset($data['rating'])) {
            // Update an existing review
            $stmt = $db->prepare("
                UPDATE reviews
                SET rating = ?, review_text = ?
                WHERE review_id = ?
            ");
            $success = $stmt->execute([
                $data['rating'],
                $data['review_text'] ?? null,
                $data['review_id']
            ]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['review_id'])) {
            // Delete a review
            $stmt = $db->prepare("DELETE FROM reviews WHERE review_id = ?");
            $success = $stmt->execute([$_GET['review_id']]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing review_id"]);
        }
        break;

    default:
        // Invalid method
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>
