<?php
include_once "includes/header.php";
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-people-fill me-2"></i>User Management System</h4>
                </div>
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="bi bi-database-fill-gear text-primary" style="font-size: 5rem;"></i>
                    </div>
                    <h2 class="mb-4">Welcome to User Management</h2>
                    <p class="lead mb-4">A complete system for managing users with MySQL/MariaDB database integration</p>
                    <a href="users.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-people me-2"></i>Manage Users
                    </a>
                </div>
                <div class="card-footer bg-light text-center">
                    <p class="mb-0">Built with PHP, MySQL, and Bootstrap</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include_once "includes/footer.php";
?>