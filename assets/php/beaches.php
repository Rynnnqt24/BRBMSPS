<?php
// Include database configuration
include '../../config/config.php';

// Set headers for JSON response
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Origin: *");

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Get request data
$data = json_decode(file_get_contents("php://input"), true);

// Path for uploaded images
$upload_dir = "../../uploads/"; // Change this to your desired directory

// Switch based on HTTP method
switch ($method) {
    case 'GET':
        if (isset($_GET['beach_id'])) {
            // Fetch a single beach by ID
            $stmt = $db->prepare("SELECT * FROM beaches WHERE beach_id = ?");
            $stmt->execute([$_GET['beach_id']]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Fetch all beaches
            $stmt = $db->query("SELECT * FROM beaches");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode($result);
        break;

    case 'POST':
        // Check if required fields are provided
        if (!empty($data['beach_name']) && !empty($data['user_id']) && !empty($data['location'])) {
            $location = $data['location'];
            $latitude = $data['latitude'];
            $longitude = $data['longitude'];
            $image_path = null;

            // Path for uploaded images (relative to the project root)
                    $upload_dir = "assets/uploads/";  // Use relative path for web access

                    // Ensure the folder exists and is writable
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);  // Create the directory if it doesn't exist
                    }

                    // Handling image upload
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $image_tmp = $_FILES['image']['tmp_name'];
                        $image_name = basename($_FILES['image']['name']);
                        $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

                        // Validate image extension and size (max 2MB)
                        if (in_array($image_extension, $allowed_extensions) && $_FILES['image']['size'] <= 2097152) {
                            $image_new_name = uniqid('', true) . "." . $image_extension;
                            $image_path = $upload_dir . $image_new_name;

                            // Move the uploaded image to the uploads directory
                            if (move_uploaded_file($image_tmp, $image_path)) {
                                echo json_encode(["message" => "Image uploaded successfully"]);
                            } else {
                                echo json_encode(["error" => "Error uploading image"]);
                                exit();
                            }
                        } else {
                            echo json_encode(["error" => "Invalid file type or file too large"]);
                            exit();
                        }
                    }

            // Insert beach record into the database
            $stmt = $db->prepare("
                INSERT INTO beaches (beach_name, description, location, latitude, longitude, user_id, gcash_qr_code, gcash_name, gcash_phone_number, image)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $success = $stmt->execute([
                $data['beach_name'],
                $data['description'] ?? null,
                $location,
                $latitude,
                $longitude,
                $data['user_id'],
                $data['gcash_qr_code'] ?? null,
                $data['gcash_name'] ?? null,
                $data['gcash_phone_number'] ?? null,
                $image_path // Store the image path in the database
            ]);

            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing required fields"]);
        }
        break;

        case 'PUT':
            // Update an existing beach
            if (!empty($data['beach_id']) && !empty($data['beach_name'])) {
                // Set the relative path for image uploads
                $upload_dir = "assets/uploads/";  // Relative path for image storage
        
                // Fetch the existing image path (for later use if no new image is uploaded)
                $stmt = $db->prepare("SELECT image FROM beaches WHERE beach_id = ?");
                $stmt->execute([$data['beach_id']]);
                $existing_beach = $stmt->fetch(PDO::FETCH_ASSOC);
                $current_image_path = $existing_beach['image'] ?? null;
        
                // Image handling: Check if a new image is uploaded
                $image_path = $current_image_path; // Default to the current image if no new image is uploaded
        
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $image_tmp = $_FILES['image']['tmp_name'];
                    $image_name = basename($_FILES['image']['name']);
                    $image_extension = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
                    // Validate image extension and size
                    if (in_array($image_extension, $allowed_extensions) && $_FILES['image']['size'] <= 2097152) {
                        // Create a unique name for the image and store it in the uploads folder
                        $image_new_name = uniqid('', true) . "." . $image_extension;
                        $image_path = $upload_dir . $image_new_name;
        
                        // Move the uploaded image to the desired directory
                        if (!move_uploaded_file($image_tmp, $image_path)) {
                            echo json_encode(["error" => "Error uploading image"]);
                            exit();
                        }
        
                        // Optionally, delete the old image file if a new image is uploaded
                        if ($current_image_path && file_exists($current_image_path)) {
                            unlink($current_image_path);  // Delete old image file
                        }
                    } else {
                        echo json_encode(["error" => "Invalid file type or file too large"]);
                        exit();
                    }
                }
        
                // Update the beach details, including the image path
                $stmt = $db->prepare("
                    UPDATE beaches
                    SET 
                        beach_name = ?, 
                        description = ?, 
                        location = ?, 
                        latitude = ?, 
                        longitude = ?, 
                        gcash_qr_code = ?, 
                        gcash_name = ?, 
                        gcash_phone_number = ?, 
                        image = ?
                    WHERE beach_id = ?
                ");
        
                // Execute the update query
                $success = $stmt->execute([
                    $data['beach_name'],
                    $data['description'] ?? null,
                    $data['location'] ?? null,
                    $data['latitude'] ?? null,
                    $data['longitude'] ?? null,
                    $data['gcash_qr_code'] ?? null,
                    $data['gcash_name'] ?? null,
                    $data['gcash_phone_number'] ?? null,
                    $image_path, // Store the updated image path
                    $data['beach_id']
                ]);
        
                echo json_encode(["success" => $success]);
            } else {
                echo json_encode(["error" => "Missing required fields"]);
            }
            break;
        

    case 'DELETE':
        if (isset($_GET['beach_id'])) {
            // Delete a beach record
            $stmt = $db->prepare("DELETE FROM beaches WHERE beach_id = ?");
            $success = $stmt->execute([$_GET['beach_id']]);
            echo json_encode(["success" => $success]);
        } else {
            echo json_encode(["error" => "Missing beach_id"]);
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(["error" => "Method not allowed"]);
        break;
}
?>
