<?php
// Include database configuration
include '../../config/config.php';

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
        if (isset($_GET['transaction_id'])) {
            // Fetch a single transaction by ID with additional details
            $stmt = $db->prepare("
                SELECT 
                    t.transaction_id,
                    b.beach_name,
                    a.name AS amenity_name,
                    r.checkin_date,
                    r.checkout_date,
                    t.payment_method,
                    t.payment_date,
                    t.status
                FROM transactions t
                INNER JOIN reservations r ON t.reservation_id = r.reservation_id
                INNER JOIN beaches b ON r.beach_id = b.beach_id
                INNER JOIN amenities a ON r.amenity_id = a.amenity_id
                WHERE t.transaction_id = ?
            ");
            $stmt->execute([$_GET['transaction_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC); // Single record
        } elseif (isset($_GET['user_id'])) {
            // Fetch all transactions for a specific user with additional details
            $stmt = $db->prepare("
                SELECT 
                    t.transaction_id,
                    b.beach_name,
                    a.name AS amenity_name,
                    r.checkin_date,
                    r.checkout_date,
                    t.payment_method,
                    t.payment_date,
                    t.status
                FROM transactions t
                INNER JOIN reservations r ON t.reservation_id = r.reservation_id
                INNER JOIN beaches b ON r.beach_id = b.beach_id
                INNER JOIN amenities a ON r.amenity_id = a.amenity_id
                WHERE t.user_id = ?
            ");
            $stmt->execute([$_GET['user_id']]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Multiple records
        } else {
            // Fetch all transactions without user filtering
            $stmt = $db->prepare("
                SELECT 
                    t.transaction_id,
                    b.beach_name,
                    a.name AS amenity_name,
                    r.checkin_date,
                    r.checkout_date,
                    t.payment_method,
                    t.payment_date,
                    t.status
                FROM transactions t
                INNER JOIN reservations r ON t.reservation_id = r.reservation_id
                INNER JOIN beaches b ON r.beach_id = b.beach_id
                INNER JOIN amenities a ON r.amenity_id = a.amenity_id
            ");
            $stmt->execute(); // Corrected to execute the statement
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC); // Multiple records
        }

        // Return the JSON-encoded result
        echo json_encode($result);
        break;
 
    

    case 'POST':
        if (!empty($data['reservation_id']) && !empty($data['user_id']) && !empty($data['amount'])) {
            // Insert a new transaction
            $stmt = $db->prepare("
                INSERT INTO transactions (reservation_id, user_id, amount, payment_method, status)
                VALUES (?, ?, ?, ?, ?)
            ");
            $success = $stmt->execute([
                $data['reservation_id'],
                $data['user_id'],
                $data['amount'],
                $data['payment_method'] ?? 'gcash',
                $data['status'] ?? 'pending'
            ]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

        case 'PUT':
            try {
                if (!empty($data['transaction_id']) && isset($data['status'])) {
                    // Retrieve the reservation details based on the reservation_id
                    $stmt = $db->prepare("
                        SELECT r.reservation_id, r.amount, r.payment_method, r.payment_date, r.status, r.user_id
                        FROM reservations r
                        WHERE r.reservation_id = ?
                    ");
                    $stmt->execute([$data['reservation_id']]);
                    $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        
                    if ($reservation) {
                        // Update the transaction with new details from the reservation
                        $stmt = $db->prepare("
                            UPDATE transactions
                            SET 
                                reservation_id = ?,
                                user_id = ?,
                                amount = ?,
                                payment_method = ?,
                                payment_date = ?,
                                status = ?
                            WHERE transaction_id = ?
                        ");
                        $success = $stmt->execute([
                            $reservation['reservation_id'],
                            $reservation['user_id'],
                            $reservation['amount'],
                            $reservation['payment_method'],
                            $reservation['payment_date'],
                            $data['status'], // Updated status
                            $data['transaction_id']
                        ]);
        
                        echo json_encode(["success" => $success]);
                    } else {
                        echo json_encode(["error" => "Reservation not found"]);
                    }
                } else {
                    echo json_encode(["error" => "Missing required fields"]);
                }
            } catch (PDOException $e) {
                // Handle errors
                echo json_encode(["error" => $e->getMessage()]);
            }
            break;
        

    case 'DELETE':
        if (isset($_GET['transaction_id'])) {
            // Delete a transaction
            $stmt = $db->prepare("DELETE FROM transactions WHERE transaction_id = ?");
            $success = $stmt->execute([$_GET['transaction_id']]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing transaction_id"]);
        }
        break;

    default:
        // Invalid method
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>
