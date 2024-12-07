<?php
if (isset($_SESSION['active_beach'])) {
    $stmt = $db->prepare("SELECT beach_name FROM beaches WHERE beach_id = :beach_id");
    $stmt->bindParam(':beach_id', $_SESSION['active_beach'], PDO::PARAM_INT);
    $stmt->execute();
    $beach = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($beach) {
        echo "Active Beach: " . htmlspecialchars($beach['beach_name']);
    } else {
        echo "No active beach found. Please select one.";
    }
} else {
    echo "No active beach selected. Please select one.";
}
?>
