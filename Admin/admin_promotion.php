<?php
include('../Database/database.php');
echo "<h4 class='mb-3'>Promotions Management</h4>";

// Function to check if promotion is expired
function isPromotionExpired($end_date) {
    return strtotime($end_date) < strtotime(date('Y-m-d'));
}

// Function to check if promotion is active
function isPromotionActive($start_date, $end_date, $is_active) {
    $current_date = strtotime(date('Y-m-d'));
    $start = strtotime($start_date);
    $end = strtotime($end_date);
    
    return $is_active && ($current_date >= $start) && ($current_date <= $end);
}

// Function to get promotion usage count
function getPromotionUsageCount($conn, $promotion_id) {
    $stmt = $conn->prepare("SELECT COUNT(*) as usage_count FROM promotion_usage WHERE promotion_id = ?");
    $stmt->bind_param("i", $promotion_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
    return $data['usage_count'];
}

/* add promo */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['promotion_code'])) {
    if (isset($_POST['update_promotion'])) {
        // Update existing promotion
        $pid = (int)$_POST['promotion_id'];
        $code = trim($_POST['promotion_code']);
        $amount = (float)$_POST['promotion_amount'];
        $start = $_POST['start_date'];
        $end   = $_POST['end_date'];
        $active = isset($_POST['is_active']) ? 1 : 0;

        $stmt = $conn->prepare("UPDATE promotion SET promotion_code=?, promotion_amount=?, start_date=?, end_date=?, is_active=? WHERE promotion_id=?");
        $stmt->bind_param("sdssii", $code, $amount, $start, $end, $active, $pid);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Promotion updated successfully.</div>";
        } else {
            echo "<div class='alert alert-danger'>Failed to update promotion (maybe code already exists).</div>";
        }
        $stmt->close();
    } else {
        // Add new promotion
        $code = trim($_POST['promotion_code']);
        $amount = (float)$_POST['promotion_amount'];
        $start = $_POST['start_date'];
        $end   = $_POST['end_date'];
        $active = isset($_POST['is_active']) ? 1 : 0;

        // Check if end date is before start date
        if (strtotime($end) < strtotime($start)) {
            echo "<div class='alert alert-danger'>End date cannot be before start date.</div>";
        } else {
            $stmt = $conn->prepare("INSERT INTO promotion (promotion_code, promotion_amount, start_date, end_date, is_active) VALUES (?,?,?,?,?)");
            $stmt->bind_param("sdssi", $code, $amount, $start, $end, $active);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Promotion added successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Failed to add promotion (maybe code already exists).</div>";
            }
            $stmt->close();
        }
    }
}

/* delete promo */
if (isset($_GET['delete'])) {
    $pid = (int)$_GET['delete'];
    
    // First delete from promotion_usage table
    $conn->query("DELETE FROM promotion_usage WHERE promotion_id=$pid");
    // Then delete from promotion table
    $conn->query("DELETE FROM promotion WHERE promotion_id=$pid");
    echo "<div class='alert alert-success'>Promotion deleted successfully.</div>";
}

// Get all promotions
$promos = $conn->query("SELECT * FROM promotion ORDER BY created_at DESC");
?>

<!-- Add Promotion Form -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="card-title mb-0">Add New Promotion</h5>
    </div>
    <div class="card-body">
        <form method="post" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Promotion Code</label>
                <input class="form-control" name="promotion_code" placeholder="SUMMER25" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Discount (%)</label>
                <input class="form-control" name="promotion_amount" type="number" step="0.01" min="1" max="100" placeholder="15.00" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Start Date</label>
                <input class="form-control" name="start_date" type="date" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">End Date</label>
                <input class="form-control" name="end_date" type="date" min="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="col-md-2">
                <div class="form-check form-switch mt-3">
                    <input class="form-check-input" type="checkbox" name="is_active" id="active" checked style="transform: scale(1.2);">
                    <label class="form-check-label fw-bold" for="active">Active</label>
                </div>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100">Add</button>
            </div>
        </form>
    </div>
</div>

<!-- Promotions List -->
<div class="card">
    <div class="card-header bg-secondary text-white">
        <h5 class="card-title mb-0"><i class="fas fa-tags me-2"></i>All Promotions</h5>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-2">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Discount</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th>Usable</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($p = $promos->fetch_assoc()): 
                    $is_expired = isPromotionExpired($p['end_date']);
                    $is_active = isPromotionActive($p['start_date'], $p['end_date'], $p['is_active']);
                    $usage_count = getPromotionUsageCount($conn, $p['promotion_id']);
                    
                    $status_class = '';
                    $status_text = '';
                    
                    if (!$p['is_active']) {
                        $status_class = 'bg-secondary';
                        $status_text = 'Inactive';
                    } elseif ($is_expired) {
                        $status_class = 'bg-danger';
                        $status_text = 'Expired';
                    } elseif ($is_active) {
                        $status_class = 'bg-success';
                        $status_text = 'Active';
                    } else {
                        $status_class = 'bg-warning';
                        $status_text = 'Scheduled';
                    }
                ?>
                    <tr>
                        <td><?php echo $p['promotion_id']; ?></td>
                        <td>
                            <strong><?php echo htmlspecialchars($p['promotion_code']); ?></strong>
                        </td>
                        <td>
                            <span class="badge bg-info text-dark"><?php echo (float)$p['promotion_amount']; ?>%</span>
                        </td>
                        <td><?php echo $p['start_date']; ?></td>
                        <td>
                            <?php echo $p['end_date']; ?>
                            <?php if ($is_expired): ?>
                                <br><small class="text-danger">(Expired)</small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge bg-primary"><?php echo $usage_count; ?> uses</span>
                        </td>
                        <td>
                            <span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                        </td>
                        <td>
                            <?php if ($is_active): ?>
                                <span class="badge bg-success"><i class="fas fa-check me-1"></i>One-time use</span>
                            <?php else: ?>
                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Cannot Use</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <!-- Edit Button -->
                                <button type="button" class="btn btn-outline-primary m-1" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $p['promotion_id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <!-- View Usage Button -->
                                <button type="button" class="btn btn-outline-info m-1" data-bs-toggle="modal" data-bs-target="#usageModal<?php echo $p['promotion_id']; ?>">
                                    <i class="fas fa-eye"></i>
                                </button>
                                
                                <!-- Delete Button -->
                                <a class="btn btn-outline-danger m-1" href="?page=admin_promotion.php&delete=<?php echo $p['promotion_id']; ?>" onclick="return confirm('Are you sure you want to delete promotion <?php echo htmlspecialchars($p['promotion_code']); ?>?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal<?php echo $p['promotion_id']; ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Promotion: <?php echo htmlspecialchars($p['promotion_code']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="post">
                                            <div class="modal-body">
                                                <input type="hidden" name="promotion_id" value="<?php echo $p['promotion_id']; ?>">
                                                <input type="hidden" name="update_promotion" value="1">
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Promotion Code</label>
                                                    <input type="text" class="form-control" name="promotion_code" value="<?php echo htmlspecialchars($p['promotion_code']); ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Discount Amount (%)</label>
                                                    <input type="number" class="form-control" name="promotion_amount" step="0.01" min="1" max="100" value="<?php echo (float)$p['promotion_amount']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Start Date</label>
                                                    <input type="date" class="form-control" name="start_date" value="<?php echo $p['start_date']; ?>" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">End Date</label>
                                                    <input type="date" class="form-control" name="end_date" value="<?php echo $p['end_date']; ?>" required>
                                                </div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="is_active" id="active<?php echo $p['promotion_id']; ?>" <?php echo $p['is_active'] ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="active<?php echo $p['promotion_id']; ?>">Active Promotion</label>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Update Promotion</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Usage Modal -->
                            <div class="modal fade" id="usageModal<?php echo $p['promotion_id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Promotion Usage: <?php echo htmlspecialchars($p['promotion_code']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <?php
                                            // Get usage details
                                            $usage_sql = "
                                                SELECT pu.*, u.user_name, u.email, o.order_id, o.total_amount, o.order_date 
                                                FROM promotion_usage pu 
                                                JOIN users u ON pu.user_id = u.user_id 
                                                JOIN order_ o ON pu.order_id = o.order_id 
                                                WHERE pu.promotion_id = ? 
                                                ORDER BY pu.used_at DESC
                                            ";
                                            $usage_stmt = $conn->prepare($usage_sql);
                                            $usage_stmt->bind_param("i", $p['promotion_id']);
                                            $usage_stmt->execute();
                                            $usage_result = $usage_stmt->get_result();
                                            
                                            if ($usage_result->num_rows > 0): ?>
                                                <div class="table-responsive">
                                                    <table class="table table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>User</th>
                                                                <th>Email</th>
                                                                <th>Order ID</th>
                                                                <th>Order Total</th>
                                                                <th>Used Date</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php while($usage = $usage_result->fetch_assoc()): ?>
                                                                <tr>
                                                                    <td><?php echo htmlspecialchars($usage['user_name']); ?></td>
                                                                    <td><?php echo htmlspecialchars($usage['email']); ?></td>
                                                                    <td>#<?php echo $usage['order_id']; ?></td>
                                                                    <td>$<?php echo number_format($usage['total_amount'], 2); ?></td>
                                                                    <td><?php echo date('M j, Y g:i A', strtotime($usage['used_at'])); ?></td>
                                                                </tr>
                                                            <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            <?php else: ?>
                                                <p class="text-muted text-center">No usage recorded for this promotion yet.</p>
                                            <?php endif; ?>
                                            <?php $usage_stmt->close(); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.badge {
    font-size: 0.75em;
}
.btn-group .btn {
    border-radius: 4px;
    margin: 0 1px;
}
.table th {
    font-weight: 600;
    background-color: #f8f9fa;
}
</style>

<script>
// Set minimum end date to today for new promotions
document.querySelector('input[name="end_date"]').min = new Date().toISOString().split('T')[0];
</script>