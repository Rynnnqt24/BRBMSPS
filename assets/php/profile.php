// profile.php
<?php
session_start();

// Fetch user details from the session
if (isset($_SESSION['user_id'])) {
    echo "Username: " . $_SESSION['username'] . "<br>";
    echo "Role: " . $_SESSION['role'] . "<br>";
} else {
    echo "You need to log in first.";
}
?>
