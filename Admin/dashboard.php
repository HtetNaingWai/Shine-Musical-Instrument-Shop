<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php
include('../Database/database.php');

if(isset($_GET["dcid"])) {
  $dcid= (int)$_GET["dcid"];
  $conn->query("DELETE FROM product_category WHERE category_id=$dcid");
  header("Location: dashboard.php?page=category.php"); exit;
}
if(isset($_GET["dmid"])) {
  $dmid= (int)$_GET["dmid"];
  $conn->query("DELETE FROM manufacturer WHERE mid=$dmid");
  header("Location: dashboard.php?page=manufacturer.php"); exit;
}
?>
<div class="admincontainer d-flex">
  <div class="adminleft p-3 border-end" style="min-width:240px">
    <p class="title h5 mb-3">👤 Dashboard</p>
    <a class="d-block mb-2" href="dashboard.php?page=main.php"><i class="fa-solid fa-gauge"></i> Dashboard</a>
    <a class="d-block mb-2" href="dashboard.php?page=users.php"><i class="fa-solid fa-users"></i> Users</a>
    <a class="d-block mb-2" href="dashboard.php?page=category.php"><i class="fa-solid fa-list"></i> Product Categories</a>
    <a class="d-block mb-2" href="dashboard.php?page=products.php"><i class="fa-solid fa-box"></i> Products</a>
    <a class="d-block mb-2" href="dashboard.php?page=manufacturer.php"><i class="fa-solid fa-truck"></i> Manufacturer</a>
    <a class="d-block mb-2" href="dashboard.php?page=admin_order.php"><i class="fa-solid fa-shopping-cart"></i> Orders</a>
    <a class="d-block mb-2" href="dashboard.php?page=admin_promotion.php"><i class="fa-solid fa-tags"></i> Promotions</a>
    <a class="d-block mb-2" href="dashboard.php?page=messages.php"><i class="fa-solid fa-envelope"></i> Messages</a>
    <a class="d-block mt-3 text-danger" href="admin_logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>

  <div class="adminright p-3 flex-grow-1">
    
    <?php
      if (isset($_GET["page"])) {
        include($_GET["page"]);
        $_SESSION["page"] = $_GET["page"];
      } else if (isset($_SESSION["page"])) {
        include($_SESSION["page"]);
      } else {
        echo "<h4>Welcome, Admin</h4><p>Select an item on the left to manage the store.</p>";
      }
    ?>
  </div>
</div>
</body>
</html>
