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
        if (isset($_GET['id'])) {
            // Fetch a single message by ID
            $stmt = $db->prepare("SELECT * FROM messages WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Fetch all messages
            $stmt = $db->query("SELECT * FROM messages");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode($result);
        break;

    case 'POST':
        if (!empty($data['sender_id']) && !empty($data['receiver_id']) && !empty($data['beach_id']) && !empty($data['message'])) {
            // Insert a new message
            $stmt = $db->prepare("
                INSERT INTO messages (sender_id, receiver_id, beach_id, message)
                VALUES (?, ?, ?, ?)
            ");
            $success = $stmt->execute([
                $data['sender_id'],
                $data['receiver_id'],
                $data['beach_id'],
                $data['message']
            ]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        if (!empty($data['id']) && isset($data['is_read'])) {
            // Update the 'is_read' status of a message
            $stmt = $db->prepare("
                UPDATE messages
                SET is_read = ?
                WHERE id = ?
            ");
            $success = $stmt->execute([
                $data['is_read'],
                $data['id']
            ]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            // Delete a message
            $stmt = $db->prepare("DELETE FROM messages WHERE id = ?");
            $success = $stmt->execute([$_GET['id']]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing message ID"]);
        }
        break;

    default:
        // Invalid method
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>
