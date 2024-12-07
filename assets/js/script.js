document.addEventListener("DOMContentLoaded", function () {
    // URL to your API
    const apiURL = "http://localhost/BRBMSP/assets/php/amenities.php";

    // Get the amenities container
    const amenitiesContainer = document.getElementById("amenities-container");

    // Fetch data from the API
    fetch(apiURL)
        .then((response) => {
            if (!response.ok) {
                throw new Error("Network response was not ok");
            }
            return response.json();
        })
        .then((data) => {
            // Loop through each amenity and create its card
            data.forEach((amenity) => {
                const card = `
                    <div class="col-md-6">
                        <div class="card border-0 shadow d-flex flex-row">
                            <img src="${amenity.image || 'default-image.jpg'}"
                                class="img-fluid rounded-start"
                                alt="Image of ${amenity.name}"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="card-body d-flex flex-column justify-content-between">
                                <div>
                                    <h5 class="card-title fw-bold">${amenity.name}</h5>
                                    <p class="card-text text-secondary">${amenity.description}</p>
                                    <p class="${amenity.availability_status === 'available' ? 'text-success' : 'text-danger'} fw-semibold">
                                        ${amenity.availability_status === 'available' ? 'Available' : 'Unavailable'}
                                    </p>
                                    <p class="fw-bold text-dark">$${amenity.price}/${amenity.price_type.replace('_', ' ')}</p>
                                </div>
                                <a href="#" class="btn btn-primary mt-2 ${amenity.availability_status === 'unavailable' ? 'disabled' : ''}">Reserve</a>
                            </div>
                        </div>
                    </div>
                `;
                // Append the card to the container
                amenitiesContainer.innerHTML += card;
            });
        })
        .catch((error) => {
            console.error("Error fetching amenities:", error);
            amenitiesContainer.innerHTML = `<p class="text-danger">Failed to load amenities. Please try again later.</p>`;
        });
});

document.getElementById('uploadBtn').addEventListener('click', function () {
    // Show modal
    const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
    uploadModal.show();
});

// Drag and drop zone functionality
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
let selectedFile = null;

dropZone.addEventListener('click', () => {
    fileInput.click(); // Trigger file selection
});

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-primary');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-primary');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-primary');
    const files = e.dataTransfer.files;
    if (files.length > 0) {
        selectedFile = files[0];
        dropZone.innerText = `Selected: ${selectedFile.name}`;
        previewImage(selectedFile);
    }
});

fileInput.addEventListener('change', (e) => {
    const files = e.target.files;
    if (files.length > 0) {
        selectedFile = files[0];
        dropZone.innerText = `Selected: ${selectedFile.name}`;
        previewImage(selectedFile);
    }
});

// Preview image before upload
function previewImage(file) {
    const previewImg = document.getElementById('previewImg');
    const reader = new FileReader();
    reader.onload = function (e) {
        previewImg.src = e.target.result;
        previewImg.classList.remove('d-none');
    };
    reader.readAsDataURL(file);
}

// Handle upload
document.getElementById('uploadSubmit').addEventListener('click', async function () {
    const beachId = 1;  // Replace with the actual beach ID from your form
    if (!selectedFile) {
        alert('Please select an image to upload.');
        return;
    }

    const formData = new FormData();
    formData.append('image', selectedFile);
    formData.append('beach_id', beachId);

    try {
        const response = await fetch('http://localhost/BRBMSP/assets/php/beach_gallery.php', {
            method: 'POST',
            body: formData,
            headers: {
                // Add any additional headers if needed
            }
        });

        const result = await response.json();
        if (response.ok) {
            alert(result.message); // Show success message
            const uploadModal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
            uploadModal.hide(); // Hide modal after successful upload
            fetchImages(beachId); // Reload the images in the gallery
        } else {
            alert(`Error: ${result.message}`); // Show error message
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while uploading the image.');
    }
});

// Fetch images for the beach
async function fetchImages(beachId = null) {
    try {
        // Construct the URL
        const url = beachId 
            ? `http://localhost/BRBMSP/assets/php/beach_gallery.php?beach_id=${beachId}`
            : `http://localhost/BRBMSP/assets/php/beach_gallery.php`;

        // Fetch images from the backend
        const response = await fetch(url);
        const images = await response.json();

        // Clear the gallery before adding images
        const imageGallery = document.getElementById('imageGallery');
        imageGallery.innerHTML = '';

        // Check if images exist
        if (images.length === 0) {
            imageGallery.innerHTML = '<p>No images uploaded yet.</p>';
            return;
        }

        // Loop through images and display them
        images.forEach((image) => {
            const col = document.createElement('div');
            col.className = 'col';

            col.innerHTML = `
                <div class="card h-150">
                    <img src="../${image.image_url}" class="card-img-top" alt="Beach Image">
                </div>
            `;
            imageGallery.appendChild(col);
        });
    } catch (error) {
        console.error('Error fetching images:', error);
    }
}

// Call the function to fetch images for a specific beach (replace '1' with the actual beach ID)
fetchImages(1);