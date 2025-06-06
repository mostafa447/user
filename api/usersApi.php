<?php
header('Content-Type: application/json');
include_once "../config/database.php";

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Process based on request method
switch($method) {
    case 'GET':
        // Get all users or a specific user
        if(isset($_GET['id'])) {
            $id = $_GET['id'];
            try {
                // Use stored procedure to get user by ID
                $stmt = $db->prepare("CALL sp_get_user_by_id(?)");
                $stmt->execute([$id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if($user) {
                    echo json_encode(['status' => 'success', 'data' => $user]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'User not found']);
                }
            } catch(PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        } else {
            try {
                // Use stored procedure to get all users
                $stmt = $db->prepare("CALL sp_get_all_users()");
                $stmt->execute();
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                echo json_encode(['status' => 'success', 'data' => $users]);
            } catch(PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            }
        }
        break;
        
    case 'POST':
        // Add a new user
        $data = json_decode(file_get_contents("php://input"));
        
        if(!$data) {
            // If not JSON, try POST data
            $data = (object)$_POST;
        }
        
        if(isset($data->name) && isset($data->email)) {
            try {
                // Use stored procedure to add user
                $phone = isset($data->phone) ? $data->phone : null;
                $stmt = $db->prepare("CALL sp_add_user(?, ?, ?)");
                $stmt->execute([$data->name, $data->email, $phone]);
                
                // Get the ID of the newly inserted user
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $id = $result['id'];
                
                // Get the complete user data
                $stmt = $db->prepare("CALL sp_get_user_by_id(?)");
                $stmt->execute([$id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode(['status' => 'success', 'message' => 'User added successfully', 'data' => $user]);
            } catch(PDOException $e) {
                // Check if it's a duplicate email error
                if(strpos($e->getMessage(), 'Email already exists') !== false) {
                    echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to add user: ' . $e->getMessage()]);
                }
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Name and email are required']);
        }
        break;
        
    case 'PUT':
        // Update an existing user
        $data = json_decode(file_get_contents("php://input"));
        
        if(!$data) {
            // If not JSON, try POST data
            $data = (object)$_POST;
        }
        
        if(isset($data->id) && isset($data->name) && isset($data->email)) {
            try {
                // Use stored procedure to update user
                $phone = isset($data->phone) ? $data->phone : null;
                $stmt = $db->prepare("CALL sp_update_user(?, ?, ?, ?)");
                $stmt->execute([$data->id, $data->name, $data->email, $phone]);
                
                // Get the updated user data
                $stmt = $db->prepare("CALL sp_get_user_by_id(?)");
                $stmt->execute([$data->id]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                echo json_encode(['status' => 'success', 'message' => 'User updated successfully', 'data' => $user]);
            } catch(PDOException $e) {
                // Check if it's a duplicate email error
                if(strpos($e->getMessage(), 'Email already exists') !== false) {
                    echo json_encode(['status' => 'error', 'message' => 'Email already exists for another user']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update user: ' . $e->getMessage()]);
                }
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID, name, and email are required']);
        }
        break;
        
    case 'DELETE':
        // Delete a user
        $data = json_decode(file_get_contents("php://input"));
        
        if(!$data) {
            // If not JSON, try GET data
            $data = (object)$_GET;
        }
        
        if(isset($data->id)) {
            try {
                // Use stored procedure to delete user
                $stmt = $db->prepare("CALL sp_delete_user(?)");
                $stmt->execute([$data->id]);
                
                echo json_encode(['status' => 'success', 'message' => 'User deleted successfully']);
            } catch(PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete user: ' . $e->getMessage()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'User ID is required']);
        }
        break;
        
    default:
        echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
        break;
}