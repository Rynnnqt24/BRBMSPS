<?php
// Include database configuration
include '../config/config.php';  // Update the path to your config file

// Get the search query from the URL
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Check if query is not empty
if (!empty($query)) {

    // Prepare SQL for searching beaches, amenities, and available amenities
    $stmt = $db->prepare("
        SELECT b.beach_name, a.amenity_name, aa.amenity_name AS available_amenity
        FROM beaches b
        LEFT JOIN amenities a ON a.beach_id = b.beach_id
        LEFT JOIN available_amenities aa ON aa.amenity_id = a.amenity_id
        WHERE b.beach_name LIKE :query
           OR a.amenity_name LIKE :query
           OR aa.amenity_name LIKE :query
    ");

    // Bind the query parameter for LIKE search (search query wrapped in % for partial matching)
    $stmt->bindValue(':query', '%' . $query . '%', PDO::PARAM_STR);
    $stmt->execute();

    // Fetch the results
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if results exist
    if (count($results) > 0) {
        foreach ($results as $result) {
            echo "<div class='result-item'>";
            if (!empty($result['beach_name'])) {
                echo "<p>Beach: " . htmlspecialchars($result['beach_name']) . "</p>";
            }
            if (!empty($result['amenity_name'])) {
                echo "<p>Amenity: " . htmlspecialchars($result['amenity_name']) . "</p>";
            }
            if (!empty($result['available_amenity'])) {
                echo "<p>Available Amenity: " . htmlspecialchars($result['available_amenity']) . "</p>";
            }
            echo "</div>";
        }
    } else {
        echo "<p>No results found.</p>";
    }

} else {
    echo "<p>Please enter a search query.</p>";
}
?>
