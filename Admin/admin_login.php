
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login - Shine Musical Instrument Shop</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <body class="bg-secondary text-white">
    <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 shadow-lg" style="width: 100%; max-width: 400px;">
      <div class="card-body">
        <h2 class="card-title text-center text-dark mb-4"> Admin Login</h2>
     <?php 
          if (isset($_GET['error'])){
          echo '<div class="alert alert-danger">Invalid username or password.</div>';}
      ?>
        <form method="POST" action="admin_login_process.php">
          <div class="mb-3">
          <label for="username" class="form-label">👤Username</label>
          <input type="text" name="username" class="form-control" required placeholder="Enter username"/>

          </div>

          <div class="mb-3">
            <label for="password" class="form-label">🔒Password</label>
            <input type="password" name="password" class="form-control" required placeholder="Enter password"/>
          </div>

          <button type="submit" class="btn btn-dark w-100 fw-bold" name="btnlogin">Login</button>
        </form>

      </div>
    </div>
  </div>
</body>
</html>