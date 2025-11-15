<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include_once("../Database/database.php");
include_once("utils.php");

// Check if user is logged in and not banned
if (isset($_SESSION['user'])) {
    $user_id = $_SESSION['user']['user_id'];
    $check_ban = $conn->query("SELECT status FROM users WHERE user_id = $user_id AND status = 'banned'");
    
    if ($check_ban->num_rows > 0) {
        // User is banned - destroy session and redirect
        session_destroy();
        echo "<script>
            alert('Your account has been banned. Please contact customer support.');
            window.location.href = 'login.php';
        </script>";
        exit();
    }
}

$cartQty = cart_count();
$wishlistQty = wishlist_count();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Shine Musical Instrument</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom">
  <div class="container">
    <!-- Left: brand -->
    <a class="navbar-brand fw-bold" href="index.php">🎵 Shine Musical Instrument</a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNav">
      <!-- CENTER: main nav -->
      <ul class="navbar-nav mx-auto gap-lg-3 text-center">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="user_products.php">Product</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="contact.php#contact">Contact</a></li>
      </ul>

      <!-- Right: cart + wishlist + account -->
      <div class="d-flex align-items-center gap-2 ms-lg-2">
        <!-- Wishlist Button -->
        <?php if (isset($_SESSION['user'])): ?>
          <a class="btn btn-outline-secondary position-relative" href="wishlist.php" title="Wishlist">
            <i class="fa fa-heart"></i>
            <?php if ($wishlistQty > 0): ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                <?php echo $wishlistQty; ?>
              </span>
            <?php endif; ?>
          </a>
        <?php else: ?>
          <a class="btn btn-outline-secondary" href="login.php" title="Login to use wishlist">
            <i class="fa fa-heart"></i>
          </a>
        <?php endif; ?>

        <!-- Cart Button -->
        <a class="btn btn-outline-secondary position-relative" href="cart.php" title="Shopping Cart">
          <i class="fa fa-shopping-cart"></i>
          <?php if ($cartQty > 0): ?>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
              <?php echo $cartQty; ?>
            </span>
          <?php endif; ?>
        </a>

        <?php if (isset($_SESSION['user'])): ?>
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
              <i class="fa fa-user"></i> <?php echo htmlspecialchars($_SESSION['user']['user_name']); ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="order_history.php"><i class="fa fa-history me-2"></i>My Orders</a></li>
              <li><a class="dropdown-item" href="wishlist.php"><i class="fa fa-heart me-2"></i>My Wishlist</a></li>
              <li><a class="dropdown-item" href="promotion.php"><i class="fa fa-tags me-2"></i>Promotions</a></li>
              <li><a class="dropdown-item" href="user_setting.php"><i class="fas fa-user-cog me-2"></i>Setting</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa fa-sign-out me-2"></i>Logout</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="login.php" class="btn btn-primary">Login</a>
          <a href="register.php" class="btn btn-outline-primary">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>