<?php
// Ensure you have your user array defined before using it in the href
// For testing purposes, we'll create a mock user array:
include 'assets/php/checkuser.php';
$_SESSION['user_id'] ;
$_SESSION['beach_id'] ;
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome -->

</head>
<style>
   body {
            font-family: 'Roboto', sans-serif;
        }

        .drag-area {
      border: 2px dashed #007bff;
      border-radius: 5px;
      padding: 20px;
      text-align: center;
      transition: background-color 0.3s;
    }

    .drag-area.dragover {
      background-color: #e9f5ff;
    }
  </style>
  </head>
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
                            <li class="nav-item"><a class="nav-link" href="customer/webpagebeach.php"><br><span style="color: rgb(62, 74, 89); background-color: initial;">Beach</span><br><br><br></a></li>
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
  <body class="bg-light">


<main class="container py-5">
    
 <!-- Button to trigger modal -->
<div class="d-flex justify-content-end mb-4">
    <button class="btn btn-primary" id="uploadBtn">
        <i class="fas fa-upload me-2"></i>Upload Image
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadModalLabel">Upload Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div 
          id="dropZone" 
          class="border border-dashed p-4 text-center" 
          style="cursor: pointer;">
          Drag and drop your image here or click to select.
        </div>
        <input type="file" id="fileInput" accept="image/*" style="display: none;">
        <input type="hidden" id="beachId" value="1">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" id="uploadSubmit" class="btn btn-primary">Upload</button>
      </div>
    </div>
  </div>
</div>


<div class="container mt-5">
    <h2>Image Gallery</h2>
    <div id="imageGallery" class="row row-cols-1 row-cols-md-3 g-4"></div>
</div>

  <!-- Drag-and-Drop Modal -->
  <div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="uploadModalLabel">Upload Image</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Drag-and-Drop Area -->
          <div class="drag-area" id="dragArea">
            <p>Drag and drop an image here or <strong>click to upload</strong>.</p>
            <input type="file" id="imageInput" class="form-control" name="image" hidden accept="image/*">
          </div>
          <div class="mt-3">
            <label for="beach_id" class="form-label">Beach ID</label>
            <input type="number" class="form-control" id="beach_id" name="beach_id" required>
          </div>
          <button type="button" class="btn btn-primary mt-3" id="uploadSubmit">Upload</button>
        </div>
      </div>
    </div>
  </div>
</main>
<!-- Footer -->
    <footer class="bg-dark text-white py-4 text-center">
      <p>&copy; 2024 Gallery. All Rights Reserved.</p>
    </footer>

    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/js/theme.js"></script>
    <script src="assets/js/script.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>


    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
