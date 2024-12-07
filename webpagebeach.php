<?php
// Only customers should be able to access this page
include 'assets/php/checkuser.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BRBMS - Beaches</title>
    <!-- Bootstrap and other CSS links -->
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/Nunito.css">
    <link rel="stylesheet" href="assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="assets/css/Articles-Cards-images.css">
    <link rel="stylesheet" href="assets/css/Navbar-Right-Links-icons.css">
    <link rel="stylesheet" href="assets/css/Pricing-Clean-badges.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.2.0/dist/leaflet.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    <script src="https://unpkg.com/leaflet@1.2.0/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.js"></script>
</head>
<style>
   body {
            font-family: 'Roboto', sans-serif;
        }
        .card-img-top {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
  </style>

<body id="page-top">
    <div id="wrapper">
        <div class="d-flex flex-column" id="content-wrapper">
            <div id="content">
                <nav class="navbar navbar-expand sticky-sm-top bg-white shadow mb-0 topbar">
                    <div class="container-fluid"><a class="navbar-brand d-flex align-items-center" href="index.php"><span>BRBMS</span></a>
                        <form class="d-none d-sm-inline-block me-auto ms-md-3 my-2 my-md-0 mw-100 navbar-search">
                            <div class="input-group"><input class="bg-light form-control border-0 small" type="text" placeholder="Search amenities..."><button class="btn btn-primary py-0" type="button"><i class="fas fa-search"></i></button></div>
                        </form>
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item"><a class="nav-link active" href="customer/customer_dashboard.php"><br><span style="color: rgb(62, 74, 89); background-color: initial;">Home</span><br><br></a></li>
                            <li class="nav-item"><a class="nav-link" href="#beach"><br><span style="color: rgb(62, 74, 89); background-color: initial;">Beaches</span><br><br><br></a></li>
                            <li class="nav-item"><a class="nav-link" href="gallery.php?id=' . urlencode($beach['beach_id'])"><br><span style="color: rgb(62, 74, 89); background-color: initial;">Gallery</span><br><br><br></a></li>

                        </ul>
                        <ul class="navbar-nav flex-nowrap ms-auto">
                            <li class="nav-item dropdown d-sm-none no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><i class="fas fa-search"></i></a>
                                <div class="dropdown-menu dropdown-menu-end p-3 animated--grow-in" aria-labelledby="searchDropdown">
                                    <form class="me-auto navbar-search w-100">
                                        <div class="input-group"><input class="bg-light border-0 form-control small" type="text" placeholder="Search for ..."><button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button></div>
                                    </form>
                                </div>
                            </li>
                         
                        <div class="d-none d-sm-block topbar-divider"></div>
                            <li class="nav-item dropdown no-arrow">
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small">Valerie Luna</span><img class="border rounded-circle img-profile" src="assets/img/avatars/avatar1.jpeg"></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in"><a class="dropdown-item" href="customer/profile.php?id=<?php echo urlencode($users['user_id']); ?>"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a><a class="dropdown-item" href="#"><i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Settings</a><a class="dropdown-item" href="#"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Activity log</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
        
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
                        foreach ($beaches as $beach) {
                            ?>
                            <div class="container mt-5">
                                 <div class="card border rounded-lg overflow-hidden">
                                 <img src="<?= $beach['image'] ?>" class="card-img-top" alt="<?= $beach['beach_name'] ?>">
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
                                                    <span class="ms-2 text-muted"><?= $rating ?> (<?= $beach['reviews_count'] ?> reviews)</span>
                                                </div>
                                                </div>
                                                <a href="#details" class="btn btn-primary mt-4 w-100">
                                                    Show All Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
        <div class=" mt-4">
                
                <!-- DIRI IBUTANG IMUHA MAPA  -->
                <div id="map" style="width: 100%; height: 500px;"></div>
  
                <!-- DIRI IBUTANG IMUHA MAPA  -->
  
            </div>

                                <div class="mt-4">
                                    <h2 class="text-center">Explore Amenities</h2>
                                    <div class="container mt-5">
                                        <div class="row g-4">
                                            <?php
                                            // Fetch amenities data
                                            $amenities_url = 'http://localhost/BRBMSP/assets/php/amenities.php'; // Replace with the actual API URL
                                            $amenities_response = file_get_contents($amenities_url);
                                            $amenities = json_decode($amenities_response, true);

                                            foreach ($amenities as $amenity) {
                                                ?>
                                                <div class="col-md-6">
                                                    <div class="card border-0 shadow d-flex flex-row">
                                                        <img src="<?= $amenity['image'] ?>" class="img-fluid rounded-start" alt="<?= $amenity['name'] ?>" style="width: 150px; height: 150px; object-fit: cover;">
                                                        <div class="card-body d-flex flex-column justify-content-between">
                                                            <h5 class="card-title fw-bold"><?= $amenity['name'] ?></h5>
                                                            <p class="card-text text-secondary"><?= $amenity['description'] ?></p>
                                                            <p class="text-success fw-semibold"><?= $amenity['availability_status'] ?></p>
                                                            <p class="fw-bold text-dark">$<?= $amenity['price'] ?>/<?= $amenity['price_type'] ?></p>
                                                            <a href="#" class="btn btn-primary mt-2">Reserve</a>
                                                        </div>
                                                    </div>
                                            </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <?php
                        }
                    } else {
                        echo "No beach data available.";
                    }
                }
            }
            ?>
        </div>
        <br>
        </div>
    </div>
</div>

            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © <?= $beach['beach_name'] ?></span></div>
                </div>
            </footer>


<script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
<script>
    // Initialize the map and set the default view
    let map = L.map('map').setView([<?= htmlspecialchars($beach['latitude']) ?>, <?= htmlspecialchars($beach['longitude']) ?>], 13);

    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add a marker with a popup for the beach location
    let beachMarker = L.marker([<?= htmlspecialchars($beach['latitude']) ?>, <?= htmlspecialchars($beach['longitude']) ?>])
        .addTo(map)
        .bindPopup('<b><?= htmlspecialchars($beach['beach_name']) ?></b><br><?= htmlspecialchars($beach['location']) ?>')
        .openPopup();

    // User location marker and routing control
    let userMarker;
    let routingControl;

    // Function to track user's current location
    function trackUser() {
        if (navigator.geolocation) {
            navigator.geolocation.watchPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;

                    // Add or update the user's location marker
                    if (!userMarker) {
                        userMarker = L.marker([latitude, longitude])
                            .addTo(map)
                            .bindPopup("You are here!");
                    } else {
                        userMarker.setLatLng([latitude, longitude]);
                    }

                    // Adjust the map view to fit both the user and destination markers
                    let bounds = L.latLngBounds([
                        [latitude, longitude], // User's location
                        [<?= htmlspecialchars($beach['latitude']) ?>, <?= htmlspecialchars($beach['longitude']) ?>] // Beach location
                    ]);
                    map.fitBounds(bounds);

                    // Add routing from the user's location to the beach
                    if (routingControl) {
                        routingControl.remove();
                    }
                    routingControl = L.Routing.control({
                        waypoints: [
                            L.latLng(latitude, longitude), // User's location
                            L.latLng(<?= htmlspecialchars($beach['latitude']) ?>, <?= htmlspecialchars($beach['longitude']) ?>) // Beach location
                        ],
                        routeWhileDragging: true,
                        lineOptions: {
                            styles: [{ color: 'red', weight: 5 }]

                        }
                    }).addTo(map);
                },
                (error) => console.error(error),
                { enableHighAccuracy: true }
            );
        } else {
            alert("Geolocation is not supported by your browser.");
        }
    }

    // Call the function to track the user
    trackUser();
</script>