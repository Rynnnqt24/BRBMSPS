<?php
// Include database configuration
include '../config/config.php';  // Make sure this path points to your database configuration

// Set headers for JSON response
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Origin: *");

// Get the input data from the request
$data = json_decode(file_get_contents("php://input"), true);

// Validate the input data
if (!empty($data['user_id']) && !empty($data['beach_id']) && !empty($data['amenity_id']) &&
    !empty($data['reservation_date']) && !empty($data['quantity']) && !empty($data['total_amount']) &&
    !empty($data['payment_method']) && !empty($data['customer_name']) && !empty($data['customer_address']) &&
    !empty($data['contact_number']) && !empty($data['checkin_date']) && !empty($data['checkout_date']) &&
    !empty($data['reference_number'])) {

    try {
        // Start a transaction to ensure that both reservation and transaction are inserted atomically
        $db->beginTransaction();

        // Insert into reservations table
        $stmt = $db->prepare("
            INSERT INTO reservations (user_id, beach_id, amenity_id, reservation_date, quantity, total_amount, 
                                      status, payment_status, payment_method, customer_name, customer_address, 
                                      contact_number, checkin_date, checkout_date, reference_number)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['user_id'],
            $data['beach_id'],
            $data['amenity_id'],
            $data['reservation_date'],
            $data['quantity'],
            $data['total_amount'],
            'pending',  // Default status is 'pending'
            'pending',  // Default payment status is 'pending'
            $data['payment_method'],
            $data['customer_name'],
            $data['customer_address'],
            $data['contact_number'],
            $data['checkin_date'],
            $data['checkout_date'],
            $data['reference_number']
        ]);

        // Get the last inserted reservation ID
        $reservation_id = $db->lastInsertId();

        // Insert into transactions table
        $stmt = $db->prepare("
            INSERT INTO transactions (reservation_id, user_id, amount, payment_method, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $reservation_id,
            $data['user_id'],
            $data['total_amount'],  // The total amount for the reservation
            $data['payment_method'],
            'pending' // Set the initial status of the transaction as 'pending'
        ]);

        // Commit the transaction
        $db->commit();

        // Return a success response
        echo json_encode(["success" => true, "reservation_id" => $reservation_id]);
    } catch (Exception $e) {
        // Rollback in case of an error
        $db->rollBack();
        echo json_encode(["error" => "Reservation failed: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["error" => "Missing required fields. Please check your input."]);
}
?>
