<?php
// Fetch data from the beach API
$api_url = 'http://localhost/BRBMSP/assets/php/beaches.php'; // Replace with the actual API URL
$response = file_get_contents($api_url);

// Check if the response is not empty or false
if ($response === false) {
    echo "Error: Unable to fetch data from the API.";
} else {
    // Decode the JSON response
    $beaches = json_decode($response, true);

    // Check for JSON decode errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Error: Failed to decode JSON - " . json_last_error_msg();
    } else {
        // Check if beaches data is valid
        if (is_array($beaches) && !empty($beaches)) {
            ?>
            <div class="container mt-5">
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                    <?php
                    // Loop through each beach and display a card
                    foreach ($beaches as $beach) {
                        // Create a URL for the details page, passing the beach ID
                        $beach_details_url = '../customer/webpagebeach.php?id=' . urlencode($beach['beach_id']);
                        ?>
                        <div class="col">
                            <!-- Wrap the entire card in an anchor tag to make it clickable for beach details -->
                            <a href="<?= $beach_details_url ?>" class="text-decoration-none">
                                <div class="card h-100">
                                    <!-- Beach Image -->
                                    <img src="<?= $beach['image'] ?>" class="card-img-top" alt="<?= $beach['beach_name'] ?>" height="200">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $beach['beach_name'] ?></h5>
                                        <p class="card-text"><?= $beach['description'] ?></p>
                                        <p class="text-success fw-bold">Available</p>
                                        <p class="text-muted">Location: <?= $beach['location'] ?></p>
                                        <div class="d-flex align-items-center">
                                            <span class="text-warning">
                                                <!-- Display stars based on the rating -->
                                                <?php
                                                $rating = $beach['rating']; // Assuming rating is in the response (e.g., 4.5)
                                                for ($i = 0; $i < floor($rating); $i++) {
                                                    echo '<i class="fas fa-star"></i>';
                                                }
                                                if ($rating - floor($rating) >= 0.5) {
                                                    echo '<i class="fas fa-star-half-alt"></i>';
                                                }
                                                for ($i = 0; $i < (5 - ceil($rating)); $i++) {
                                                    echo '<i class="far fa-star"></i>';
                                                }
                                                ?>
                                            </span>
                                            <span class="ms-2 text-muted"><?= $rating ?> (<?= $beach['reviews_count'] ?> reviews)</span> <!-- Assuming review count is in the response -->
                                        </div>
                                    </div>
                                </div>
                            </a>
                            
                            <!-- "Reserve" Button (Separate from card, still clickable for reservation action) -->
                            <a href="reserve.php?beach_id=<?= urlencode($beach['id']) ?>" class="btn btn-primary mt-3 d-block">Reserve</a>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        } else {
            echo "No beach data available.";
        }
    }
}
?>
