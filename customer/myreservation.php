<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>Reservation Layout</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
</head>
<body class="font-roboto bg-light">
  <div class="container mt-4">
    <div class="bg-white p-4 rounded shadow-sm">
    <div class="d-flex align-items-center space-x-2">
                <a href="customer_dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>
                </a>
            </div><br>
      
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mb-4">
        <div class="w-95 mb-3 mb-md-0">
          <input id="searchInput" class="form-control" placeholder="Search..." type="text"/>
        </div>
        <div class="d-flex justify-content-end">
          <select id="statusFilter" class="form-select" style="width: auto;">
            <option value="all">All Status</option>
            <option value="confirmed">Confirmed</option>
            <option value="pending">Pending</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="table-responsive">
        <table id="reservationsTable" class="table table-bordered table-striped ">
          <thead>
            <tr>
                <th>Reservation ID</th>
                <th>Beach Name</th>
                <th>Amenity Name</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Status</th>
                <th>Total Amount</th>
            </tr>
          </thead>
          <tbody>
            <!-- Dynamic content will be inserted here -->
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
        // Fetch Reservations for the logged-in customer
        const userId = <?= json_encode($_SESSION['user_id']); ?>; // Replace with session value
        let reservationsData = []; // Store the reservation data

        async function fetchReservations() {
            try {
                const response = await fetch(`http://localhost/BRBMSP/assets/php/reservation.php?user_id=${userId}`);
                if (!response.ok) {
                    throw new Error(`Error fetching reservations: ${response.statusText}`);
                }
                const data = await response.json();
                reservationsData = data; // Store the fetched data

                displayReservations(data); // Display all reservations
            } catch (error) {
                console.error("Error:", error);
                alert("An error occurred while fetching reservations.");
            }
        }

        // Display Reservations
        function displayReservations(data) {
            const tbody = document.querySelector("#reservationsTable tbody");
            tbody.innerHTML = ""; // Clear existing rows

            if (data.length > 0) {
                data.forEach(reservation => {
                    const row = document.createElement("tr");

                    row.innerHTML = `
                        <td>${reservation.reservation_id}</td>
                        <td>${reservation.beach_name}</td>
                        <td>${reservation.name}</td>
                        <td>${reservation.checkin_date}</td>
                        <td>${reservation.checkout_date}</td>
                        <td>${reservation.status}</td>
                        <td>${reservation.total_amount}</td>
                        
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align: center;">No reservations found</td></tr>`;
            }
        }

        // Live Search Filter
        document.getElementById("searchInput").addEventListener("input", filterReservations);
        document.getElementById("statusFilter").addEventListener("change", filterReservations);

        // Filter function
        function filterReservations() {
            const searchQuery = document.getElementById("searchInput").value.trim().toLowerCase();
            const selectedStatus = document.getElementById("statusFilter").value.toLowerCase();

            const filteredReservations = reservationsData.filter(reservation => {
                return (
                    (reservation.beach_name.toLowerCase().includes(searchQuery) ||
                     reservation.amenity_name.toLowerCase().includes(searchQuery) ||
                     reservation.status.toLowerCase().includes(searchQuery)) &&
                    (selectedStatus === "all" || reservation.status.toLowerCase() === selectedStatus)
                );
            });

            // Display the filtered reservations
            displayReservations(filteredReservations);
        }

        // Call the fetch function when the page loads
        window.addEventListener("DOMContentLoaded", fetchReservations);
    </script>
</body>
</html>
