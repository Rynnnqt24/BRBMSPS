<?php
session_start();
include '../config/config.php';

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Query the database to check if the user exists
    $stmt = $db->prepare("SELECT user_id FROM users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    // If a row is returned, the user is valid
    if ($stmt->rowCount() > 0) {
        $valid_user = true;
    } else {
        $valid_user = false;
    }
} else {
    $valid_user = false;
}

if ($valid_user) {
    echo "User is logged in and valid.";
} else {
    echo "User is not logged in or is invalid.";
    header("Location: index.php");
    exit();
}
?>
