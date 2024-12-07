<?php
// Include the database configuration
include '../../config/config.php';  // Change this to your actual config file path

// Set headers to allow JSON response and access
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Origin: *");

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Get input data from JSON body
$data = json_decode(file_get_contents("php://input"), true);

// Handle the request based on the HTTP method
switch ($method) {
    case 'GET':
        if (isset($_GET['user_id'])) {
            // Fetch a single user by ID
            $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->execute([$_GET['user_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode(["message" => "User not found"]);
            }
        } else {
            // Fetch all users
            $stmt = $db->query("SELECT * FROM users");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        }
        break;

    case 'POST':
        if (!empty($data['username']) && !empty($data['password']) && !empty($data['full_name']) && !empty($data['email'])) {
            // Insert a new user
            $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT); // Hash the password
            $stmt = $db->prepare("
                INSERT INTO users (username, password, full_name, email, contact_number, gender, user_role)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $success = $stmt->execute([
                $data['username'],
                $passwordHash,
                $data['full_name'],
                $data['email'],
                $data['contact_number'] ?? null,
                $data['gender'],
                $data['user_role']
            ]);

            if ($success) {
                echo json_encode(["message" => "User created successfully"]);
            } else {
                echo json_encode(["message" => "Failed to create user"]);
            }
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'PUT':
        if (!empty($data['user_id']) && !empty($data['username'])) {
            // Update an existing user
            $passwordHash = isset($data['password']) ? password_hash($data['password'], PASSWORD_DEFAULT) : null;
            $stmt = $db->prepare("
                UPDATE users
                SET username = ?, password = ?, full_name = ?, email = ?, contact_number = ?, gender = ?, user_role = ?
                WHERE user_id = ?
            ");
            $success = $stmt->execute([
                $data['username'],
                $passwordHash ?: null, // Only update password if provided
                $data['full_name'],
                $data['email'],
                $data['contact_number'] ?? null,
                $data['gender'],
                $data['user_role'],
                $data['user_id']
            ]);

            if ($success) {
                echo json_encode(["message" => "User updated successfully"]);
            } else {
                echo json_encode(["message" => "Failed to update user"]);
            }
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

    case 'DELETE':
        if (isset($_GET['user_id'])) {
            // Delete a user by ID
            $stmt = $db->prepare("DELETE FROM users WHERE user_id = ?");
            $success = $stmt->execute([$_GET['user_id']]);

            if ($success) {
                echo json_encode(["message" => "User deleted successfully"]);
            } else {
                echo json_encode(["message" => "Failed to delete user"]);
            }
        } else {
            echo json_encode(["error" => "Missing user_id"]);
        }
        break;

    default:
        // Method not allowed
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>
