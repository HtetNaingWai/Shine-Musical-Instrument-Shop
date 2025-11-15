<?php include("user_header.php"); ?>
<?php
$err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($name==='' || $email==='' || $pass==='') {
        $err = "All fields are required.";
    } else {
        // check existing
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->fetch_assoc()) {
            $err = "Email already registered.";
        } else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $stmt = $conn->prepare("INSERT INTO users (user_name, email, password) VALUES (?,?,?)");
            $stmt->bind_param("sss", $name, $email, $hash);
            if ($stmt->execute()) {
                $_SESSION['user'] = ['user_id'=>$stmt->insert_id, 'user_name'=>$name, 'email'=>$email];
                header("Location: index.php");
                exit;
            } else $err = "Register failed.";
        }
    }
}
?>
<div class="container py-5" style="max-width:480px">
  <h4 class="mb-3">Create Account</h4>
  <?php if ($err): ?><div class="alert alert-danger"><?php echo $err;?></div><?php endif; ?>
  <form method="post">
    <div class="mb-3"><label class="form-label">Name</label><input name="name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
    <div class="mb-3"><label class="form-label">Password</label><input type="password" name="password" class="form-control" required></div>
    <button class="btn btn-primary w-100">Sign Up</button>
    <p class="mt-3 small">Already have an account? <a href="login.php">Login</a></p>
  </form>
</div>
<?php include("footer.php"); ?>
</body></html>
