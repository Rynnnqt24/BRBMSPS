<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
  <title>My Transaction</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
</head>
<body class="font-roboto bg-light">
  <div class="container mt-4">
    <div class="bg-white p-4 rounded shadow-sm">
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
        <table id="transactionTable" class="table table-bordered table-striped ">
          <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Beach Name</th>
                <th>Amenity Name</th>
                <th>Check-in Date</th>
                <th>Check-out Date</th>
                <th>Payment Method</th>
                <th>Payment Date</th>
                <th>Status</th>
                
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
       // Fetch transaction for the logged-in customer
const userId = <?= json_encode($_SESSION['user_id'] ?? null); ?>; // Replace with session value
let transactionData = []; // Store the reservation data

async function fetchTransaction() {
    try {
        if (!userId) {
            console.error("User ID not found in session.");
            alert("Please log in to view your transactions.");
            return;
        }

        const response = await fetch(`http://localhost/BRBMSP/assets/php/transactions.php?user_id=${userId}`);
        if (!response.ok) {
            throw new Error(`Error fetching transaction: ${response.statusText}`);
        }
        const data = await response.json();
        transactionData = data; // Store the fetched data

        displayTransaction(data); // Display all transaction
    } catch (error) {
        console.error("Error:", error);
        alert("An error occurred while fetching transactions.");
    }
}

// Display transactions
function displayTransaction(data) {
    const tbody = document.querySelector("#transactionTable tbody");
    tbody.innerHTML = ""; // Clear existing rows

    if (data.length > 0) {
        data.forEach(transaction => {
            const row = document.createElement("tr");

            row.innerHTML = `
                <td>${transaction.transaction_id}</td>
                <td>${transaction.beach_name}</td>
                <td>${transaction.amenity_name}</td>
                <td>${transaction.checkin_date}</td>
                <td>${transaction.checkout_date}</td>
                <td>${transaction.payment_method}</td>
                <td>${transaction.payment_date}</td>
                <td>${transaction.status}</td>
                <td>
                    <button class="btn btn-success">
                        <i class="fas fa-download"></i> Download
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    } else {
        tbody.innerHTML = `<tr><td colspan="8" class="text-center">No transactions found</td></tr>`;
    }
}

// Live Search Filter
document.getElementById("searchInput").addEventListener("input", filterTransaction);
document.getElementById("statusFilter").addEventListener("change", filterTransaction);

// Filter function
function filterTransaction() {
    const searchQuery = document.getElementById("searchInput").value.trim().toLowerCase();
    const selectedStatus = document.getElementById("statusFilter").value.toLowerCase();

    const filteredTransaction = transactionData.filter(transaction => {
        return (
            (transaction.beach_name.toLowerCase().includes(searchQuery) ||
             transaction.amenity_name.toLowerCase().includes(searchQuery) ||
             transaction.status.toLowerCase().includes(searchQuery)) &&
            (selectedStatus === "all" || transaction.status.toLowerCase() === selectedStatus)
        );
    });

    // Display the filtered transactions
    displayTransaction(filteredTransaction);
}

// Call the fetch function when the page loads
window.addEventListener("DOMContentLoaded", fetchTransaction);

    </script>
</body>
</html>
