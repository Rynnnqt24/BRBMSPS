<?php
// Fetch data from the amenities API (replace with your actual URL)
$api_url = 'http://localhost/BRBMSP/assets/php/amenities.php'; // Replace with the actual API URL
$response = file_get_contents($api_url);
$amenities = json_decode($response, true); // Decode the JSON response into an associative array
$_SESSION['beach_id'] ;

?>

<h2 class="text-center">Explore Amenities</h2>
<div class="container mt-5">
    <div class="row g-4">
        <?php
        // Loop through each amenity and display it in a card
        foreach ($amenities as $amenity) {
            ?>
            <div class="col-md-6">
                <div class="card border-0 shadow d-flex flex-row">
                    <img src="<?= $amenity['image'] ?>" 
                        class="img-fluid rounded-start" 
                        alt="<?= $amenity['name'] ?>" 
                        style="width: 150px; height: 150px; object-fit: cover;">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title fw-bold"><?= $amenity['name'] ?></h5>
                            <p class="card-text text-secondary"><?= $amenity['description'] ?></p>
                            <p class="text-success fw-semibold"><?= $amenity['availability_status'] ?></p>
                            <p class="fw-bold text-dark">$<?= $amenity['price'] ?>/<?= $amenity['price_type'] ?></p>
                        </div>
                        <!-- Reserve Button -->
                        <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#reserveModal<?= $amenity['amenity_id'] ?>">Reserve</button>
                    </div>
                </div>
            </div>

           <!-- Modal for the reservation -->
<div class="modal fade" id="reserveModal<?= $amenity['amenity_id'] ?>" tabindex="-1" aria-labelledby="reserveModalLabel<?= $amenity['amenity_id'] ?>" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reserveModalLabel<?= $amenity['amenity_id'] ?>">Reserve <?= $amenity['name'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="submit_reservation.php" method="POST">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Customer Name</label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="amenity_id" class="form-label">Amenity ID</label>
                        <input type="hidden" class="form-control" id="amenity_id" name="amenity_id" value="<?= $amenity['amenity_id'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="reservation_date" class="form-label">Reservation Date</label>
                        <input type="date" class="form-control" id="reservation_date" name="reservation_date" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="total_amount" class="form-label">Total Amount</label>
                        <input type="number" class="form-control" id="total_amount" name="total_amount" value="<?= $amenity['price'] ?>" required readonly>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                        </select>
                    </div>

                    <!-- GCash Payment Fields -->
                    <div id="gcash_fields" class="d-none">
                                    <div class="mb-3">
                                        <label for="gcash_name" class="form-label">GCash Name</label>
                                        <input type="text" class="form-control" id="gcash_name" name="gcash_name" value="<?= $amenity['gcash_name'] ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gcash_number" class="form-label">GCash Number</label>
                                        <input type="text" class="form-control" id="gcash_number" name="gcash_number" value="<?= $amenity['gcash_number'] ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label for="gcash_qr_code" class="form-label">GCash QR Code</label>
                                        <img src="<?= $amenity['gcash_qr_code'] ?>" alt="GCash QR" class="img-fluid" style="max-width: 200px;">
                                    </div>
                                    <div class="mb-3">
                                        <label for="gcash_reference" class="form-label">GCash Reference Number</label>
                                        <input type="text" class="form-control" id="gcash_reference" name="gcash_reference" placeholder="Enter Reference Number" required>
                                    </div>
                                    <button type="button" class="btn btn-success" id="done_payment">Done Paying</button>
                                </div><br>

                    <!-- Cash Payment Note -->
                    <div id="cash_note" class="d-none">
                        <p class="text-danger">Please pay the total amount within 24 hours to confirm your reservation.</p>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitReservationBtn">Submit Reservation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Show/hide GCash fields based on the payment method selected
document.getElementById('payment_method').addEventListener('change', function () {
    var paymentMethod = this.value;
    if (paymentMethod === 'gcash') {
        document.getElementById('gcash_fields').classList.remove('d-none');
        document.getElementById('cash_note').classList.add('d-none');
    } else {
        document.getElementById('gcash_fields').classList.add('d-none');
        document.getElementById('cash_note').classList.remove('d-none');
    }
});

// Auto-calculate total amount based on quantity
document.getElementById('quantity').addEventListener('input', function () {
    var quantity = parseInt(this.value);
    var price = <?= $amenity['price'] ?>;  // PHP variable in JavaScript
    if (!isNaN(quantity) && quantity > 0) {
        var totalAmount = price * quantity;
        document.getElementById('total_amount').value = totalAmount;
    } else {
        document.getElementById('total_amount').value = price;
    }
});

// GCash "Done Paying" button action
document.getElementById('donePayingBtn').addEventListener('click', function () {
    // Hide the GCash fields and show the cash note after payment
    document.getElementById('gcash_fields').classList.add('d-none');
    document.getElementById('cash_note').classList.remove('d-none');
    // Optionally, you can enable the "Submit Reservation" button here if required
    document.getElementById('submitReservationBtn').disabled = false;
});
</script>

            <?php
        }
        ?>
    </div> <!-- End of Row -->
</div> <!-- End of Container -->

<!-- Bootstrap JS and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<script>
// Show/hide GCash fields based on the payment method selected
document.getElementById('payment_method').addEventListener('change', function () {
    var paymentMethod = this.value;
    if (paymentMethod === 'gcash') {
        document.getElementById('gcash_fields').classList.remove('d-none');
        document.getElementById('cash_note').classList.add('d-none');
    } else {
        document.getElementById('gcash_fields').classList.add('d-none');
        document.getElementById('cash_note').classList.remove('d-none');
    }
});

// Auto-calculate total amount based on quantity
document.getElementById('quantity').addEventListener('input', function () {
    var quantity = parseInt(this.value);
    var price = <?= $amenity['price'] ?>;  // PHP variable in JavaScript
    if (!isNaN(quantity) && quantity > 0) {
        var totalAmount = price * quantity;
        document.getElementById('total_amount').value = totalAmount;
    } else {
        document.getElementById('total_amount').value = price;
    }
});
// Done Payment Button Logic
document.getElementById('done_payment').addEventListener('click', function () {
    alert('Payment done! You can now submit your reservation.');
    // Optionally, you can close the modal or do further actions like enabling the submit button
});
</script>
