<?php
include '../config/config.php';// Get the selected amenity ID from the URL
// Get form data (this would come from your HTML form)
$quantity = $_POST['quantity'];
$customer_name = $_POST['customer_name'];
$customer_address = $_POST['customer_address'];
$contact_number = $_POST['contact_number'];
$checkin_date = $_POST['checkin_date'];
$checkout_date = $_POST['checkout_date'];
$payment_method = $_POST['payment_method']; // GCash, PayPal, Bank Transfer, or Cash
$amenity_id = $_POST['amenity_id']; // The selected amenity ID

// For GCash, collect reference number, name, and number
$gcash_reference = ($payment_method == 'GCash') ? $_POST['gcash_reference'] : null;
$gcash_name = ($payment_method == 'GCash') ? $_POST['gcash_name'] : null;
$gcash_number = ($payment_method == 'GCash') ? $_POST['gcash_number'] : null;

// Calculate total amount
$total_amount = $quantity * $amenity['price']; // You can replace $amenity['price'] with your actual price calculation

// Assume user_id and beach_id are already available (e.g., from session or other logic)
$user_id = 1; // Replace with the actual logged-in user ID
$beach_id = 1; // Replace with the actual beach ID based on selection

// Generate a reference number (you can also generate this dynamically or via a different method)
$reference_number = uniqid("RES-");

// Prepare SQL statement for insertion
$reservation_query = "
    INSERT INTO reservations 
    (user_id, beach_id, amenity_id, reservation_date, quantity, total_amount, status, 
    payment_status, payment_method, customer_name, customer_address, contact_number, 
    checkin_date, checkout_date, reference_number, gcash_reference, gcash_name, gcash_number)
    VALUES 
    (:user_id, :beach_id, :amenity_id, NOW(), :quantity, :total_amount, 'pending', 
    'pending', :payment_method, :customer_name, :customer_address, :contact_number, 
    :checkin_date, :checkout_date, :reference_number, :gcash_reference, :gcash_name, :gcash_number)
";

// Prepare the statement
$stmt = $pdo->prepare($reservation_query);

// Bind the parameters
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':beach_id', $beach_id, PDO::PARAM_INT);
$stmt->bindParam(':amenity_id', $amenity_id, PDO::PARAM_INT);
$stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
$stmt->bindParam(':total_amount', $total_amount, PDO::PARAM_STR);
$stmt->bindParam(':payment_method', $payment_method);
$stmt->bindParam(':customer_name', $customer_name);
$stmt->bindParam(':customer_address', $customer_address);
$stmt->bindParam(':contact_number', $contact_number);
$stmt->bindParam(':checkin_date', $checkin_date);
$stmt->bindParam(':checkout_date', $checkout_date);
$stmt->bindParam(':reference_number', $reference_number);
$stmt->bindParam(':gcash_reference', $gcash_reference);
$stmt->bindParam(':gcash_name', $gcash_name);
$stmt->bindParam(':gcash_number', $gcash_number);

// Execute the query
if ($stmt->execute()) {
    echo "Reservation successfully created!";
} else {
    echo "Error: Unable to create reservation.";
}

?>
