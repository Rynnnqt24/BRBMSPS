<?php
include '../../config/config.php';

// Set headers for JSON response
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Origin: *");

$method = $_SERVER['REQUEST_METHOD']; // Get the HTTP method
$input = json_decode(file_get_contents('php://input'), true); // Decode JSON input for POST/PUT

// Helper function for sending JSON responses
function sendResponse($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit();
}

switch ($method) {
    case 'GET': // Retrieve reservations
        $reservation_id = isset($_GET['id']) ? intval($_GET['id']) : null;
        $user_id = isset($_GET['user_id']) ? intval($_GET['user_id']) : null;
    
        try {
            if ($reservation_id) {
                // Fetch a single reservation with beach and amenity names
                $stmt = $db->prepare("
                    SELECT 
                        r.*, 
                        b.beach_name, 
                        a.name 
                    FROM 
                        reservations r
                    LEFT JOIN 
                        beaches b ON r.beach_id = b.beach_id
                    LEFT JOIN 
                        amenities a ON r.amenity_id = a.amenity_id
                    WHERE 
                        r.reservation_id = ?
                ");
                $stmt->execute([$reservation_id]);
            } elseif ($user_id) {
                // Fetch reservations for a user with beach and amenity names
                $stmt = $db->prepare("
                    SELECT 
                        r.*, 
                        b.beach_name, 
                        a.name 
                    FROM 
                        reservations r
                    LEFT JOIN 
                        beaches b ON r.beach_id = b.beach_id
                    LEFT JOIN 
                        amenities a ON r.amenity_id = a.amenity_id
                    WHERE 
                        r.user_id = ?
                ");
                $stmt->execute([$user_id]);
            } else {
                // Fetch all reservations with beach and amenity names
                $stmt = $db->query("
                    SELECT 
                        r.*, 
                        b.beach_name, 
                        a.name 
                    FROM 
                        reservations r
                    LEFT JOIN 
                        beaches b ON r.beach_id = b.beach_id
                    LEFT JOIN 
                        amenities a ON r.amenity_id = a.amenity_id
                ");
            }
    
            $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
            sendResponse($reservations);
        } catch (Exception $e) {
            sendResponse(["error" => $e->getMessage()], 500);
        }
        break;
    

    case 'POST': // Create a new reservation
        if (!$input) sendResponse(["error" => "Invalid input"], 400);

        try {
            $sql = "INSERT INTO reservations (
                        user_id, beach_id, amenity_id, reservation_date, quantity, 
                        total_amount, status, payment_status, payment_method, 
                        customer_name, customer_address, contact_number, 
                        checkin_date, checkout_date, reference_number, 
                        gcash_reference, gcash_name, gcash_number
                    ) VALUES (
                        :user_id, :beach_id, :amenity_id, NOW(), :quantity, 
                        :total_amount, :status, :payment_status, :payment_method, 
                        :customer_name, :customer_address, :contact_number, 
                        :checkin_date, :checkout_date, :reference_number, 
                        :gcash_reference, :gcash_name, :gcash_number
                    )";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':user_id' => $input['user_id'],
                ':beach_id' => $input['beach_id'],
                ':amenity_id' => $input['amenity_id'],
                ':quantity' => $input['quantity'],
                ':total_amount' => $input['total_amount'],
                ':status' => $input['status'],
                ':payment_status' => $input['payment_status'],
                ':payment_method' => $input['payment_method'],
                ':customer_name' => $input['customer_name'],
                ':customer_address' => $input['customer_address'],
                ':contact_number' => $input['contact_number'],
                ':checkin_date' => $input['checkin_date'],
                ':checkout_date' => $input['checkout_date'],
                ':reference_number' => $input['reference_number'],
                ':gcash_reference' => $input['gcash_reference'],
                ':gcash_name' => $input['gcash_name'],
                ':gcash_number' => $input['gcash_number']
            ]);

            sendResponse(["message" => "Reservation created successfully", "id" => $db->lastInsertId()], 201);
        } catch (Exception $e) {
            sendResponse(["error" => $e->getMessage()], 500);
        }
        break;

    case 'PUT': // Update an existing reservation
        if (!$input || !isset($_GET['id'])) sendResponse(["error" => "Invalid input or ID missing"], 400);

        $reservation_id = intval($_GET['id']);

        try {
            $sql = "UPDATE reservations SET 
                        status = :status, 
                        payment_status = :payment_status, 
                        payment_method = :payment_method, 
                        checkin_date = :checkin_date, 
                        checkout_date = :checkout_date 
                    WHERE reservation_id = :reservation_id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                ':status' => $input['status'],
                ':payment_status' => $input['payment_status'],
                ':payment_method' => $input['payment_method'],
                ':checkin_date' => $input['checkin_date'],
                ':checkout_date' => $input['checkout_date'],
                ':reservation_id' => $reservation_id
            ]);

            sendResponse(["message" => "Reservation updated successfully"]);
        } catch (Exception $e) {
            sendResponse(["error" => $e->getMessage()], 500);
        }
        break;

    case 'DELETE': // Delete a reservation
        if (!isset($_GET['id'])) sendResponse(["error" => "Reservation ID required"], 400);

        $reservation_id = intval($_GET['id']);

        try {
            $sql = "DELETE FROM reservations WHERE reservation_id = :reservation_id";
            $stmt = $db->prepare($sql);
            $stmt->execute([':reservation_id' => $reservation_id]);

            sendResponse(["message" => "Reservation deleted successfully"]);
        } catch (Exception $e) {
            sendResponse(["error" => $e->getMessage()], 500);
        }
        break;

    default: // Method not allowed
        sendResponse(["error" => "Method not allowed"], 405);
        break;
}
?>
