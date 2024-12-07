<?php
// Include database connection
include '../../config/config.php'; // Change this to the correct path for your config file

header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');

// Parse the incoming request method
$method = $_SERVER['REQUEST_METHOD'];

// Function to parse raw input (used for PUT/DELETE)
function parseRawInput()
{
    parse_str(file_get_contents("php://input"), $data);
    return $data;
}

// Function to handle file uploads
function handleFileUpload($file)
{
    $uploadDirectory = "../../uploads/"; // Directory to save uploaded files
    $uploadFile = $uploadDirectory . basename($file['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
    
    // Check if file is an image
    if (!getimagesize($file["tmp_name"])) {
        return ['error' => 'File is not an image.'];
    }

    // Check file size (limit to 5MB for example)
    if ($file["size"] > 5000000) {
        return ['error' => 'File is too large.'];
    }

    // Allow certain file formats (e.g., JPG, PNG, GIF)
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        return ['error' => 'Only JPG, JPEG, PNG & GIF files are allowed.'];
    }

    // Try to upload the file
    if (move_uploaded_file($file["tmp_name"], $uploadFile)) {
        return ['success' => $uploadFile]; // Return the path of the uploaded file
    } else {
        return ['error' => 'Error uploading file.'];
    }
}

// Handle different request methods
switch ($method) {
    case 'POST': // Create
        if (isset($_POST['beach_id'], $_POST['amenity_type'], $_POST['name'], $_POST['description'], $_POST['price'], $_POST['price_type'], $_POST['capacity'], $_FILES['image'], $_POST['availability_status'])) {
            // Collect the form data
            $beach_id = filter_var($_POST['beach_id'], FILTER_SANITIZE_NUMBER_INT);
            $amenity_type = filter_var($_POST['amenity_type'], FILTER_SANITIZE_STRING);
            $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
            $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
            $price_type = filter_var($_POST['price_type'], FILTER_SANITIZE_STRING);
            $capacity = filter_var($_POST['capacity'], FILTER_SANITIZE_NUMBER_INT);
            $availability_status = filter_var($_POST['availability_status'], FILTER_SANITIZE_STRING);

            // Handle image upload
            $imageUpload = handleFileUpload($_FILES['image']);
            if (isset($imageUpload['error'])) {
                echo json_encode(['error' => $imageUpload['error']]);
                exit;
            }
            $image = $imageUpload['success'];

            // SQL query to insert a new amenity
            $query = "INSERT INTO amenities (beach_id, amenity_type, name, description, price, price_type, capacity, image, availability_status) 
                      VALUES (:beach_id, :amenity_type, :name, :description, :price, :price_type, :capacity, :image, :availability_status)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':beach_id', $beach_id, PDO::PARAM_INT);
            $stmt->bindParam(':amenity_type', $amenity_type, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':price_type', $price_type, PDO::PARAM_STR);
            $stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
            $stmt->bindParam(':availability_status', $availability_status, PDO::PARAM_STR);

            // Execute the query and return appropriate message
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Amenity created successfully.']);
            } else {
                echo json_encode(['error' => 'Failed to create amenity.']);
            }
        } else {
            echo json_encode(['error' => 'Missing parameters.']);
        }
        break;

    case 'GET': // Read
        if (isset($_GET['id'])) {
            // Fetch a specific amenity by id
            $id = intval($_GET['id']);
            $query = "SELECT * FROM amenities WHERE amenity_id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'Amenity not found.']);
            }
        } else {
            // Fetch all amenities if no specific ID is provided
            $query = "SELECT * FROM amenities";
            $stmt = $db->query($query);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode($result);
        }
        break;

    case 'PUT': // Update
        $data = parseRawInput();
        if (isset($data['id'], $data['beach_id'], $data['amenity_type'], $data['name'], $data['description'], $data['price'], $data['price_type'], $data['capacity'], $data['image'], $data['availability_status'])) {
            // Collect the PUT data
            $id = intval($data['id']);
            $beach_id = filter_var($data['beach_id'], FILTER_SANITIZE_NUMBER_INT);
            $amenity_type = filter_var($data['amenity_type'], FILTER_SANITIZE_STRING);
            $name = filter_var($data['name'], FILTER_SANITIZE_STRING);
            $description = filter_var($data['description'], FILTER_SANITIZE_STRING);
            $price = filter_var($data['price'], FILTER_SANITIZE_STRING);
            $price_type = filter_var($data['price_type'], FILTER_SANITIZE_STRING);
            $capacity = filter_var($data['capacity'], FILTER_SANITIZE_NUMBER_INT);
            $availability_status = filter_var($data['availability_status'], FILTER_SANITIZE_STRING);

            // Handle image upload (optional update)
            if (isset($data['image'])) {
                $imageUpload = handleFileUpload($_FILES['image']);
                if (isset($imageUpload['error'])) {
                    echo json_encode(['error' => $imageUpload['error']]);
                    exit;
                }
                $image = $imageUpload['success'];
            } else {
                $image = $data['image']; // Keep old image if not updating
            }

            // SQL query to update the amenity
            $query = "UPDATE amenities 
                      SET beach_id = :beach_id, amenity_type = :amenity_type, name = :name, description = :description, price = :price, 
                          price_type = :price_type, capacity = :capacity, image = :image, availability_status = :availability_status 
                      WHERE amenity_id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':beach_id', $beach_id, PDO::PARAM_INT);
            $stmt->bindParam(':amenity_type', $amenity_type, PDO::PARAM_STR);
            $stmt->bindParam(':name', $name, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':price_type', $price_type, PDO::PARAM_STR);
            $stmt->bindParam(':capacity', $capacity, PDO::PARAM_INT);
            $stmt->bindParam(':image', $image, PDO::PARAM_STR);
            $stmt->bindParam(':availability_status', $availability_status, PDO::PARAM_STR);

            // Execute the query and return appropriate message
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Amenity updated successfully.']);
            } else {
                echo json_encode(['error' => 'Failed to update amenity.']);
            }
        } else {
            echo json_encode(['error' => 'Missing parameters.']);
        }
        break;

    case 'DELETE': // Delete
        $data = parseRawInput();
        if (isset($data['id'])) {
            // Collect the DELETE data
            $id = intval($data['id']);
            // SQL query to delete the amenity
            $query = "DELETE FROM amenities WHERE amenity_id = :id";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Execute the query and return appropriate message
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Amenity deleted successfully.']);
            } else {
                echo json_encode(['error' => 'Failed to delete amenity.']);
            }
        } else {
            echo json_encode(['error' => 'Missing parameters.']);
        }
        break;

    default:
        // Handle unsupported request methods
        echo json_encode(['error' => 'Invalid request method.']);
        break;
}
?>
