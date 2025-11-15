<?php include("user_header.php"); 

// Check if user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['user_id'];
$current_username = $_SESSION['user']['user_name'];
$current_email = $_SESSION['user']['email'];

$success_message = "";
$error_message = "";

// Update username
if (isset($_POST['update_username'])) {
    $new_username = trim($_POST['new_username']);
    
    if (empty($new_username)) {
        $error_message = "Username cannot be empty.";
    } elseif ($new_username === $current_username) {
        $error_message = "New username is the same as current username.";
    } else {
        // Check if username already exists
        $check_sql = "SELECT user_id FROM users WHERE user_name = ? AND user_id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("si", $new_username, $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error_message = "Username already exists. Please choose a different one.";
        } else {
            // Update username
            $update_sql = "UPDATE users SET user_name = ? WHERE user_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("si", $new_username, $user_id);
            
            if ($update_stmt->execute()) {
                $_SESSION['user']['user_name'] = $new_username;
                $current_username = $new_username;
                $success_message = "Username updated successfully!";
            } else {
                $error_message = "Error updating username. Please try again.";
            }
            $update_stmt->close();
        }
        $check_stmt->close();
    }
}

// Update password 
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "All password fields are required.";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } elseif (strlen($new_password) < 6) {
        $error_message = "New password must be at least 6 characters long.";
    } else {
        // First, verify current password
        $check_sql = "SELECT password FROM users WHERE user_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $user_data = $check_result->fetch_assoc();
            $stored_password = $user_data['password'];
            
            // Check if user has a password set (not NULL) and verify it
            if ($stored_password !== null && password_verify($current_password, $stored_password)) {
                // Current password is correct, update to new password
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("si", $hashed_password, $user_id);
                
                if ($update_stmt->execute()) {
                    $success_message = "Password updated successfully!";
                } else {
                    $error_message = "Error updating password. Please try again.";
                }
                $update_stmt->close();
            } else {
                // First time setting password or current password is wrong
                if ($stored_password === null) {
                    // First time setting password (no current password required)
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $update_sql = "UPDATE users SET password = ? WHERE user_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("si", $hashed_password, $user_id);
                    
                    if ($update_stmt->execute()) {
                        $success_message = "Password set successfully!";
                    } else {
                        $error_message = "Error setting password. Please try again.";
                    }
                    $update_stmt->close();
                } else {
                    $error_message = "Current password is incorrect.";
                }
            }
        } else {
            $error_message = "User not found.";
        }
        $check_stmt->close();
    }
}
?>

<!-- Settings Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Alert Messages -->
                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-check-circle fa-2x me-3 text-success"></i>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-1">Success!</h5>
                            <p class="mb-0"><?php echo $success_message; ?></p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-exclamation-triangle fa-2x me-3 text-danger"></i>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-1">Error</h5>
                            <p class="mb-0"><?php echo $error_message; ?></p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Current Account Info -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light py-3">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-user me-2 text-dark"></i>Current Account Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">User ID</label>
                                <p class="fs-5 fw-bold text-dark">#<?php echo $user_id; ?></p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted">Current Username</label>
                                <p class="fs-5 fw-bold text-dark"><?php echo htmlspecialchars($current_username); ?></p>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-muted">Email Address</label>
                                <p class="fs-5 fw-bold text-dark"><?php echo htmlspecialchars($current_email); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Username Form -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-light text-dark py-3">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-edit me-2"></i>Change Username
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="new_username" class="form-label fw-bold">New Username</label>
                                    <input type="text" class="form-control form-control-lg" id="new_username" 
                                           name="new_username" value="<?php echo htmlspecialchars($current_username); ?>" 
                                           required placeholder="Enter new username">
                                    <div class="form-text">Choose a unique username that you'll use to login.</div>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update_username" class="btn btn-dark btn-lg px-4">
                                        <i class="fa fa-save me-2"></i>Update Username
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light text-dark py-3">
                        <h5 class="card-title mb-0">
                            <i class="fa fa-lock me-2"></i>Change Password
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="current_password" class="form-label fw-bold">Current Password</label>
                                    <input type="password" class="form-control form-control-lg" id="current_password" 
                                           name="current_password" placeholder="Enter current password">
                                    <div class="form-text">
                                        <?php
                                        // Check if user has a password set
                                        $check_pass_sql = "SELECT password FROM users WHERE user_id = ?";
                                        $check_pass_stmt = $conn->prepare($check_pass_sql);
                                        $check_pass_stmt->bind_param("i", $user_id);
                                        $check_pass_stmt->execute();
                                        $check_pass_result = $check_pass_stmt->get_result();
                                        $user_pass_data = $check_pass_result->fetch_assoc();
                                        
                                        if ($user_pass_data['password'] === null) {
                                            echo "You haven't set a password yet. Leave current password empty to set your first password.";
                                        } else {
                                            echo "Enter your current password to set a new one.";
                                        }
                                        $check_pass_stmt->close();
                                        ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label for="new_password" class="form-label fw-bold">New Password</label>
                                    <input type="password" class="form-control form-control-lg" id="new_password" 
                                           name="new_password" required placeholder="Enter new password">
                                    <div class="form-text">Password must be at least 6 characters long.</div>
                                </div>
                                <div class="col-12">
                                    <label for="confirm_password" class="form-label fw-bold">Confirm New Password</label>
                                    <input type="password" class="form-control form-control-lg" id="confirm_password" 
                                           name="confirm_password" required placeholder="Confirm new password">
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="update_password" class="btn btn-dark btn-lg px-4">
                                        <i class="fa fa-key me-2"></i>Update Password
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
</section>

<style>
.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.alert {
    border-radius: 10px;
    border: none;
}

.list-unstyled li {
    padding: 5px 0;
}

@media (max-width: 768px) {
    .display-6 {
        font-size: 2rem;
    }
}
</style>

<?php include("footer.php"); ?>
</body></html>