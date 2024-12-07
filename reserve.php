<?php
// PHP script to handle database connections and reservation processing (similar to the previous code)
// Assuming the necessary connection and query logic is in place
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reserve Now</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4">Reserve <?php echo $amenity['name']; ?></h2>

    <form method="POST" action="../assets/php/amenityreserve.php?amenity_id=<?php echo $amenity['amenity_id']; ?>" id="reserveForm">
        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="quantity" name="quantity" value="1" min="1" required>
        </div>

        <div class="mb-3">
            <label for="customer_name" class="form-label">Customer Name</label>
            <input type="text" class="form-control" id="customer_name" name="customer_name" required>
        </div>

        <div class="mb-3">
            <label for="customer_address" class="form-label">Customer Address</label>
            <textarea class="form-control" id="customer_address" name="customer_address" required></textarea>
        </div>

        <div class="mb-3">
            <label for="contact_number" class="form-label">Contact Number</label>
            <input type="text" class="form-control" id="contact_number" name="contact_number" required>
        </div>

        <div class="mb-3">
            <label for="checkin_date" class="form-label">Check-in Date</label>
            <input type="date" class="form-control" id="checkin_date" name="checkin_date" required>
        </div>

        <div class="mb-3">
            <label for="checkout_date" class="form-label">Checkout Date</label>
            <input type="date" class="form-control" id="checkout_date" name="checkout_date" required>
        </div>

        <div class="mb-3">
            <label for="payment_method" class="form-label">Payment Method</label>
            <select class="form-control" id="payment_method" name="payment_method" required>
                <option value="GCash">GCash</option>
                <option value="PayPal">PayPal</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Cash">Cash</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" id="reserveNowBtn">Confirm Reservation</button>
    </form>
</div>

<!-- Modal for GCash Payment -->
<div class="modal fade" id="gcashModal" tabindex="-1" aria-labelledby="gcashModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="gcashModalLabel">GCash Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>GCash Name:</strong> John Doe</p>
                <p><strong>GCash Number:</strong> 09123456789</p>
                <div class="mb-3">
                    <img src="gcash-qr-code.png" alt="GCash QR Code" class="img-fluid mb-3">
                    <p>Please scan the QR code above to complete the payment.</p>
                </div>
                <div class="mb-3">
                    <label for="gcash_reference" class="form-label">Reference Number</label>
                    <input type="text" class="form-control" id="gcash_reference" name="gcash_reference" placeholder="Enter your reference number" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Complete Reservation</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Cash Payment Warning -->
<div class="modal fade" id="cashModal" tabindex="-1" aria-labelledby="cashModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cashModalLabel">Important Payment Information</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Note:</strong> You need to pay in the physical store within 24 hours to fully reserve the amenity. If payment is not made, your reservation will be canceled.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

<!-- JavaScript to show the modal when "GCash" is selected -->
<script>
    document.getElementById('payment_method').addEventListener('change', function() {
        if (this.value === 'GCash') {
            // Show the GCash modal if 'GCash' is selected
            var myModal = new bootstrap.Modal(document.getElementById('gcashModal'), {
                keyboard: false
            });
            myModal.show();
        } else if (this.value === 'Cash') {
            // Show the Cash modal if 'Cash' is selected
            var cashModal = new bootstrap.Modal(document.getElementById('cashModal'), {
                keyboard: false
            });
            cashModal.show();
        }
    });
</script>

</body>
</html>
