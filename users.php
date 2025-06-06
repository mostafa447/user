<?php
include_once "includes/header.php";
include_once "config/database.php";

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Fetch all users using stored procedure
try {
    $stmt = $db->prepare("CALL sp_get_all_users()");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    $users = [];
}
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-12">
            <!-- Page Title with Icon -->
            <div class="d-flex align-items-center mb-4">
                <i class="bi bi-people-fill fs-1 me-3 text-primary"></i>
                <h2 class="mb-0">User Management</h2>
            </div>
            
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Users List</h4>
                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-plus-circle me-2"></i>Add New User
                    </button>
                </div>
                <div class="card-body">
                    <div id="message-container"></div>
                    <div class="table-responsive user-table">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="users-table-body">
                                <?php if(count($users) > 0): ?>
                                    <?php foreach($users as $user): ?>
                                    <tr id="user-row-<?php echo $user['id']; ?>">
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ?? ''); ?></td>
                                        <td><?php echo $user['created_at']; ?></td>
                                        <td class="action-buttons">
                                            <button class="btn btn-sm btn-info edit-user" data-id="<?php echo $user['id']; ?>" 
                                                    data-name="<?php echo htmlspecialchars($user['name']); ?>" 
                                                    data-email="<?php echo htmlspecialchars($user['email']); ?>" 
                                                    data-phone="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" 
                                                    data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                <i class="bi bi-pencil-square"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-user" data-id="<?php echo $user['id']; ?>">
                                                <i class="bi bi-trash"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Add New User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="add-user-form">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>
                    <div id="add-form-message" class="form-message"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit User</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="edit-user-form">
                    <input type="hidden" id="edit-id" name="id">
                    <div class="mb-3">
                        <label for="edit-name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit-name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="edit-phone" name="phone">
                    </div>
                    <div id="edit-form-message" class="form-message"></div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-info text-white"><i class="bi bi-check-circle me-2"></i>Update User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include_once "includes/footer.php";
?>