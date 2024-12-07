
<?php
// Only customers should be able to access this page
include '../assets/php/checkuser.php';

// Mock user session data for testing
$_SESSION['user_id'] ; // For example, after the user logs in

?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Dashboard - BRBMS</title>
    <meta name="description" content="Beach Resort Bazaar Management System">
    <link rel="stylesheet" href="../assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/Nunito.css">
    <link rel="stylesheet" href="../assets/fonts/fontawesome-all.min.css">
    <link rel="stylesheet" href="../assets/css/Articles-Cards-images.css">
    <link rel="stylesheet" href="../assets/css/Navbar-Right-Links-icons.css">
    <link rel="stylesheet" href="../assets/css/Pricing-Clean-badges.css">
 
</head>
 <style>
   body {
            font-family: 'Roboto', sans-serif;
        }
        .card-img-left {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .card-img-top {
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
                            <li class="nav-item"><a class="nav-link active" href="../customer/customer_dashboard.php"><br><span style="color: rgb(62, 74, 89); background-color: initial;">Home</span><br><br></a></li>
                            <li class="nav-item"><a class="nav-link" href="#beach"><br><span style="color: rgb(62, 74, 89); background-color: initial;">Beaches</span><br><br><br></a></li>
                      
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
                                <div class="nav-item dropdown no-arrow"><a class="dropdown-toggle nav-link" aria-expanded="false" data-bs-toggle="dropdown" href="#"><span class="d-none d-lg-inline me-2 text-gray-600 small">Valerie Luna</span><img class="border rounded-circle img-profile" src="../assets/img/avatars/avatar1.jpeg"></a>
                                    <div class="dropdown-menu shadow dropdown-menu-end animated--grow-in">
                                        <a class="dropdown-item" href="profile.php?id=<?php echo urlencode($_SESSION['user_id']); ?>"><i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Profile</a>
                                        <a class="dropdown-item" href="myreservation.php?id=<?php echo urlencode($_SESSION['user_id']); ?>"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;My Reservations</a>
                                        <a class="dropdown-item" href="mytransaction.php?id=<?php echo urlencode($_SESSION['user_id']); ?>"><i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;My Transactions</a>
                                        <div class="dropdown-divider"></div><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>&nbsp;Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
                <section id="beach">
              <?php
              include '../beach.php';
              include '../amenities.php';


              ?>
              </section>

            <footer class="bg-white sticky-footer">
                <div class="container my-auto">
                    <div class="text-center my-auto copyright"><span>Copyright © BRBMS 2024</span></div>
                </div>
            </footer>

            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script src="../assets/js/jquery.min.js"></script>
            <script src="../assets/bootstrap/js/bootstrap.min.js"></script>
            <script src="../assets/js/main.js"></script>
            <script src="../assets/js/theme.js"></script>
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
            <script src="https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.js"></script>
     

    </body>
</html>
