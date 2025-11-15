<?php
include('../Database/database.php');
echo "<h4 class='mb-3'>Users Management</h4>";

// Ban user functionality
if (isset($_GET['ban'])) {
    $uid = (int)$_GET['ban'];
    $conn->query("UPDATE users SET status='banned' WHERE user_id=$uid");
    echo "<div class='alert alert-warning'>User has been banned.</div>";
}

// Unban user functionality
if (isset($_GET['unban'])) {
    $uid = (int)$_GET['unban'];
    $conn->query("UPDATE users SET status='active' WHERE user_id=$uid");
    echo "<div class='alert alert-success'>User has been unbanned.</div>";
}

// Delete user functionality with error handling
if (isset($_GET['delete'])) {
    $uid = (int)$_GET['delete'];
    
    try {
        // First, check if user has orders
        $order_check = $conn->query("SELECT COUNT(*) as order_count FROM order_ WHERE customer_id=$uid");
        $order_data = $order_check->fetch_assoc();
        
        if ($order_data['order_count'] > 0) {
            echo "<div class='alert alert-danger'>
                    <i class='fa fa-exclamation-triangle'></i> Cannot delete user #$uid because they have existing orders.
                    <br><small>Use the ban feature instead to restrict their access while preserving order history.</small>
                  </div>";
        } else {
            // If no orders, proceed with deletion
            $conn->query("DELETE FROM users WHERE user_id=$uid");
            echo "<div class='alert alert-success'>User permanently deleted.</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>
                <i class='fa fa-exclamation-triangle'></i> Error: Cannot delete user #$uid.
                <br><small>This user has related records in other tables (orders, reviews, etc.).</small>
                <br><small>Use ban feature instead.</small>
              </div>";
    }
}

// First, let's check if the users table has a status column, if not, add it
$check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'status'");
if ($check_column->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN status ENUM('active', 'banned') DEFAULT 'active'");
}

// Get users with their order count
$rs = $conn->query("
    SELECT u.user_id, u.user_name, u.email, COALESCE(u.status, 'active') as status,
           COUNT(o.order_id) as order_count
    FROM users u 
    LEFT JOIN order_ o ON u.user_id = o.customer_id 
    GROUP BY u.user_id 
    ORDER BY u.user_id DESC
");
?>
<div class="table-responsive">
<table class="table table-striped align-middle">
  <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Orders</th>
        <th>Status</th>
        <th width='250'>Actions</th>
    </tr>
  </thead>
  <tbody>
  <?php while($u=$rs->fetch_assoc()): ?>
    <tr>
      <td><?php echo $u['user_id']; ?></td>
      <td><?php echo htmlspecialchars($u['user_name']); ?></td>
      <td><?php echo htmlspecialchars($u['email']); ?></td>
      <td>
          <span class="badge bg-<?php echo $u['order_count'] > 0 ? 'primary' : 'secondary'; ?>">
              <?php echo $u['order_count']; ?> orders
          </span>
      </td>
      <td>
          <?php if ($u['status'] == 'banned'): ?>
              <span class="badge bg-danger">Banned</span>
          <?php else: ?>
              <span class="badge bg-success">Active</span>
          <?php endif; ?>
      </td>
      <td>
        <div class="btn-group btn-group-sm" role="group">
          <?php if ($u['status'] == 'active'): ?>
            <a class="btn btn-outline-warning" href="?page=users.php&ban=<?php echo $u['user_id'];?>" onclick="return confirm('Ban this user? They will not be able to login.')" title="Ban User">
              <i class="fa fa-ban"></i> Ban
            </a>
          <?php else: ?>
            <a class="btn btn-outline-success" href="?page=users.php&unban=<?php echo $u['user_id'];?>" onclick="return confirm('Unban this user? They will be able to login again.')" title="Unban User">
              <i class="fa fa-check"></i> Unban
            </a>
          <?php endif; ?>
          
          <?php if ($u['order_count'] == 0): ?>
            <a class="btn btn-outline-danger" href="?page=users.php&delete=<?php echo $u['user_id'];?>" onclick="return confirm('Permanently delete user #<?php echo $u['user_id']; ?>? This action cannot be undone!')" title="Delete User">
              <i class="fa fa-trash"></i> Delete
            </a>
          <?php else: ?>
            <button class="btn btn-outline-secondary" disabled title="Cannot delete - User has orders">
              <i class="fa fa-trash"></i> Delete
            </button>
          <?php endif; ?>
        </div>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>

<!-- Statistics Card -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">
                            <?php 
                            $total_users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
                            echo $total_users;
                            ?>
                        </h4>
                        <p class="card-text">Total Users</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">
                            <?php 
                            $active_users = $conn->query("SELECT COUNT(*) as active FROM users WHERE COALESCE(status, 'active') = 'active'")->fetch_assoc()['active'];
                            echo $active_users;
                            ?>
                        </h4>
                        <p class="card-text">Active Users</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-user-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">
                            <?php 
                            $banned_users = $conn->query("SELECT COUNT(*) as banned FROM users WHERE status = 'banned'")->fetch_assoc()['banned'];
                            echo $banned_users;
                            ?>
                        </h4>
                        <p class="card-text">Banned Users</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-user-slash fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="card-title">
                            <?php 
                            $users_with_orders = $conn->query("SELECT COUNT(DISTINCT customer_id) as with_orders FROM order_")->fetch_assoc()['with_orders'];
                            echo $users_with_orders;
                            ?>
                        </h4>
                        <p class="card-text">Users with Orders</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fa fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mt-3">
    <h6><i class="fa fa-info-circle"></i> Important Information</h6>
    <p class="mb-0">Users with existing orders cannot be deleted due to database integrity constraints. Use the <strong>Ban</strong> feature to restrict their access while preserving order history.</p>
</div>

<style>
.btn-group .btn {
    border-radius: 4px;
    margin: 0 2px;
}
.badge {
    font-size: 0.75em;
}
.card {
    border-radius: 10px;
}
.btn:disabled {
    cursor: not-allowed;
}
</style>