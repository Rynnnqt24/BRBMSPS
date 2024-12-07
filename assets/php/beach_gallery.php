<?php

include '../../config/config.php';
// Set headers for JSON response
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Origin: *");

// Handle POST, GET, PUT, DELETE requests
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Handle image upload
        if (isset($_FILES['image']) && isset($_POST['beach_id'])) {
            $image = $_FILES['image'];
            $beach_id = $_POST['beach_id'];

            // Validate the uploaded file
            $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($image['type'], $allowed_types)) {
                echo json_encode(['message' => 'Invalid image type. Only JPG, JPEG, PNG allowed.']);
                exit();
            }

            // Set file upload path
            $upload_dir = 'C:/xampp/htdocs/BRBMSP/assets/uploads/';  // Update to your upload directory path
            $file_name = time() . '_' . basename($image['name']);
            $file_path = $upload_dir . $file_name;

            // Move uploaded file to the server's directory
            if (move_uploaded_file($image['tmp_name'], $file_path)) {
                // Insert image URL into database
                $image_url = 'assets/uploads/' . $file_name;  // Relative URL to store in the database
                $stmt = $db->prepare("INSERT INTO beach_gallery (beach_id, image_url) VALUES (:beach_id, :image_url)");
                $stmt->bindParam(':beach_id', $beach_id);
                $stmt->bindParam(':image_url', $image_url);

                if ($stmt->execute()) {
                    echo json_encode(['message' => 'Image uploaded and added to database successfully']);
                } else {
                    echo json_encode(['message' => 'Failed to insert image URL into database']);
                }
            } else {
                echo json_encode(['message' => 'Failed to upload image']);
            }
        } else {
            echo json_encode(['message' => 'Image file or beach ID is missing']);
        }
        break;

    case 'GET':
        // Retrieve images (by beach_id or all)
        if (isset($_GET['beach_id'])) {
            $beach_id = $_GET['beach_id'];
            $stmt = $db->prepare("SELECT * FROM beach_gallery WHERE beach_id = :beach_id");
            $stmt->bindParam(':beach_id', $beach_id);
        } else {
            $stmt = $db->prepare("SELECT * FROM beach_gallery");
        }

        $stmt->execute();
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($images);
        break;

    case 'PUT':
        // Update an image record
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id']) && isset($data['image_url'])) {
            $id = $data['id'];
            $image_url = $data['image_url'];
            $stmt = $db->prepare("UPDATE beach_gallery SET image_url = :image_url WHERE id = :id");
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':image_url', $image_url);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Image updated successfully']);
            } else {
                echo json_encode(['message' => 'Failed to update image']);
            }
        } else {
            echo json_encode(['message' => 'Invalid data']);
        }
        break;

    case 'DELETE':
        // Delete an image record
        $data = json_decode(file_get_contents("php://input"), true);
        if (isset($data['id'])) {
            $id = $data['id'];
            $stmt = $db->prepare("DELETE FROM beach_gallery WHERE id = :id");
            $stmt->bindParam(':id', $id);

            if ($stmt->execute()) {
                echo json_encode(['message' => 'Image deleted successfully']);
            } else {
                echo json_encode(['message' => 'Failed to delete image']);
            }
        } else {
            echo json_encode(['message' => 'Invalid data']);
        }
        break;

    default:
        echo json_encode(['message' => 'Method not allowed']);
        break;
}
?>
