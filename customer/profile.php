<?php

include '../assets/php/checkuser.php';
$_SESSION['user_id'] ; // For example, after the user logs in
    
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <title>Customer Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" />
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
    </style>
</head>

<body class="bg-light">
    <header class="bg-transparent shadow-sm">
        <div class="container d-flex justify-content-between align-items-center py-4">
            <div class="d-flex align-items-center space-x-2">
                <a href="customer_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                </a>
            </div>
            <h1 class="h2 text-dark">Profile</h1>
            <button class="btn btn-danger" id="logout-btn">
                <i class="fas fa-sign-out-alt me-2"></i>Logout
            </button>
        </div>
    </header>

    <main class="container py-5">
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-center mb-4">
                <img id="profile-pic" alt="Profile picture of the customer" class="rounded-circle mb-3"
                    style="width: 128px; height: 128px; object-fit: cover;" src="../../BRBMSP/assets/img/profile.png" />
                <h2 class="h4 text-dark" id="username"></h2>
                <p class="text-muted" id="email"></p>
            </div>

            <div class="d-flex justify-content-end mb-4">
                <button class="btn btn-primary" id="edit-profile-btn">
                    <i class="fas fa-edit me-2"></i> Edit Profile
                </button>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="h5 text-dark mb-3">Personal Information</h3>
                    <p><strong>Full Name:</strong> <span id="full_name"></span></p>
                    <p><strong>Phone:</strong> <span id="contact_number"></span></p>
                    <p><strong>Gender:</strong> <span id="gender"></span></p>
                </div>
                <div class="col-md-6">
                    <h3 class="h5 text-dark mb-3">Account Details</h3>
                    <p><strong>Member Since:</strong> <span id="date_registered"></span></p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white shadow-sm mt-5">
        <div class="container text-center py-4">
            <p class="text-muted mb-0">© 2023 Customer Profile. All rights reserved.</p>
        </div>
    </footer>

    <script>
    // Fetch the userId from the PHP session (assuming session is already started)
    const userId = <?php echo $_SESSION['user_id']; ?>;

    // Now you can use the dynamic userId in your fetch request
    fetchProfileData(userId);

    // Fetch Profile Data
    function fetchProfileData(userId) {
        fetch(`http://localhost/BRBMSP/assets/php/users.php?user_id=${userId}`)
            .then(response => response.json())
            .then(data => {
                if (data.message) {
                    alert(data.message);
                } else {
                    document.getElementById('username').textContent = data.username;
                    document.getElementById('email').textContent = data.email;
                    document.getElementById('full_name').textContent = data.full_name;
                    document.getElementById('contact_number').textContent = data.contact_number;
                    document.getElementById('address').textContent = data.address;
                    document.getElementById('gender').textContent = data.gender;

                    document.getElementById('date_registered').textContent = new Date(data.date_registered).toLocaleDateString();
                    
                }
            })
            .catch(error => console.error('Error fetching profile data:', error));
    }
</script>

</body>

</html>
