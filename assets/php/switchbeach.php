<?php
session_start();
include 'config/config.php';  // Include the database configuration

// Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected beach_id from the POST request
    $beach_id = $_POST['beach_id'];

    // Ensure the user is logged in and has a valid session
    if (!isset($_SESSION['user_id'])) {
        echo "You must be logged in to switch beaches.";
        exit();
    }

    // Check if the selected beach belongs to the logged-in owner (user_id)
    $stmt = $db->prepare("SELECT beach_id FROM beaches WHERE beach_id = :beach_id AND user_id = :user_id");
    $stmt->bindParam(':beach_id', $beach_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Set the active beach in the session
        $_SESSION['active_beach'] = $beach_id;

        // Redirect to the owner dashboard with a success message
        echo "Beach switched successfully.";
        header("Location: owner_dashboard.php"); // You might want to replace this with your actual redirect page
        exit();
    } else {
        // Error: The logged-in user doesn't own the selected beach
        echo "Error: You do not own this beach.";
    }
}
?>
