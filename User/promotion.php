<?php include("user_header.php"); ?>

<?php
$user_id = $_SESSION['user']['user_id'] ?? 0;

// Get active promotions and check if user has used them
$sql = "
    SELECT p.promotion_id, p.promotion_code, p.promotion_amount, p.start_date, p.end_date,
           CASE WHEN pu.usage_id IS NOT NULL THEN 1 ELSE 0 END as used_by_user
    FROM promotion p 
    LEFT JOIN promotion_usage pu ON p.promotion_id = pu.promotion_id AND pu.user_id = ?
    WHERE p.is_active=1 AND p.start_date <= CURDATE() AND p.end_date >= CURDATE()
    ORDER BY p.start_date DESC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$rs = $stmt->get_result();
?>

<div class="container py-4">
  <h4 class="mb-3"><i class="fa fa-tags me-2"></i>Active Promotions</h4>
  
  <?php if ($rs->num_rows == 0): ?>
    <div class="alert alert-info">No active promotions right now.</div>
  <?php else: ?>
    <div class="row g-3">
    <?php while($p = $rs->fetch_assoc()): ?>
      <div class="col-md-6 col-lg-4">
        <div class="card h-100 <?php echo $p['used_by_user'] ? 'border-warning' : ''; ?>">
          <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($p['promotion_code']);?></h5>
            <div class="display-6"><?php echo (float)$p['promotion_amount'];?>% OFF</div>
            <div class="small text-muted mt-2"><?php echo $p['start_date'];?> → <?php echo $p['end_date'];?></div>
            
            <?php if ($p['used_by_user']): ?>
              <div class="mt-2">
                <span class="badge bg-warning text-dark">
                  <i class="fa fa-check-circle me-1"></i>Already Used
                </span>
              </div>
            <?php endif; ?>
          </div>
          
          <div class="card-footer bg-transparent">
            <?php if ($p['used_by_user']): ?>
              <!-- Show disabled button if already used -->
              <button class="btn btn-secondary w-100" disabled>
                <i class="fa fa-check me-1"></i>Already Applied
              </button>
            <?php else: ?>
              <!-- Show active button if not used -->
              <form method="post" action="cart.php" class="d-flex gap-2">
                <input type="hidden" name="promo" value="<?php echo htmlspecialchars($p['promotion_code']);?>">
                <button name="apply_promo" value="1" class="btn btn-dark w-100">Apply in Cart</button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
    </div>
  <?php endif; ?>
  
  <?php $stmt->close(); ?>
</div>

<?php include("footer.php"); ?>
</body></html>