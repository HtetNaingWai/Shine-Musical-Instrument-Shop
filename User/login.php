<?php include("user_header.php"); ?>
<?php
$err = "";
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    
    $stmt = $conn->prepare("SELECT user_id, user_name, email, password, status FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    
    if ($u = $stmt->get_result()->fetch_assoc()) {
        // Check if account is banned
        if (isset($u['status']) && $u['status'] == 'banned') {
            $err = "Your account has been banned. Please contact customer support.";
        }
        // Check password if not banned
        elseif (!empty($u['password']) && password_verify($pass, $u['password'])) {
            $_SESSION['user'] = [
                'user_id' => $u['user_id'], 
                'user_name' => $u['user_name'], 
                'email' => $u['email']
            ];
            $next = $_GET['next'] ?? 'index.php';
            header("Location: " . $next);
            exit;
        } else {
            $err = "Invalid email or password.";
        }
    } else {
        $err = "Invalid email or password.";
    }
}

// Check for ban error from URL (if user was automatically logged out)
if (isset($_GET['error']) && $_GET['error'] == 'banned') {
    $err = "Your account has been banned. Please contact customer support.";
}
?>
<div class="container py-5" style="max-width:420px">
  <h4 class="mb-3">Login</h4>
  <?php if ($err): ?>
    <div class="alert alert-danger">
      <i class="fa fa-exclamation-triangle me-2"></i>
      <?php echo $err; ?>
    </div>
  <?php endif; ?>
  
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-primary w-100">Login</button>
    <p class="mt-3 small">No account? <a href="register.php">Create one</a></p>
  </form>
</div>
<?php include("footer.php"); ?>
</body></html>