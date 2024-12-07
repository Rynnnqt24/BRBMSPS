<?php
// database.php - Database connection file
include '../config/config.php';

// Fetch amenities data from the database
$query = "SELECT * FROM amenities";
$stmt = $pdo->prepare($query);
$stmt->execute();
$amenities = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Amenities Cards</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJY2fZf8smzZqCmBKN7iB7j3pWthM1+O5IYKK4KyyTXe35XkLzKpr0K8lSR7" crossorigin="anonymous">
    <style>
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4">Available Amenities</h2>
    <div class="row">
        <?php
        // Loop through the fetched amenities and display them in cards
        foreach ($amenities as $amenity) {
            echo '
            <div class="col-md-4">
                <div class="card">
                    <img src="' . $amenity['image_url'] . '" class="card-img-top" alt="' . $amenity['name'] . '">
                    <div class="card-body">
                        <h5 class="card-title">' . $amenity['name'] . '</h5>
                        <p class="card-text">' . $amenity['description'] . '</p>
                        <p class="card-text"><strong>Price: $' . $amenity['price'] . ' per night</strong></p>
                        <a href="reserve.php?amenity_id=' . $amenity['amenity_id'] . '" class="btn btn-primary">Reserve Now</a>
                    </div>
                </div>
            </div>
            ';
        }
        ?>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9gybF1lX4Tg23zS2l+O/7lFf6f0JG5go9gfvY4zRHymVXlfsuS6" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js" integrity="sha384-cn7l7gDp0eyZgF2p3jLhfiOBT1gU/6q63dV8v6enXY/I8t6UboZsY5W7czczvkB4" crossorigin="anonymous"></script>

</body>
</html>
