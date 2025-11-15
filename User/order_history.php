<?php include("user_header.php"); require_login(); ?>
<?php
$uid = (int)$_SESSION['user']['user_id'];
$stmt = $conn->prepare("SELECT order_id, order_status, order_date, total_amount, shipping_address 
                        FROM order_ WHERE customer_id=? ORDER BY order_date DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$rs = $stmt->get_result();

$details = $conn->prepare("SELECT d.quantity, d.unit_price, p.title 
                           FROM order_detail d 
                           JOIN products p ON p.product_id=d.product_item_id
                           WHERE d.order_id=?");
?>
<div class="container py-4">
  <h4 class="mb-3">My Orders</h4>
  <?php if ($rs->num_rows==0): ?>
    <div class="alert alert-info">No orders yet.</div>
  <?php endif; ?>
  <?php while($o = $rs->fetch_assoc()): ?>
    <div class="card mb-3">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div><strong>Order #<?php echo $o['order_id'];?></strong> • <?php echo $o['order_date'];?></div>
          <div>Status: <span class="badge text-bg-secondary"><?php echo $o['order_status'];?></span></div>
        </div>
        <div class="mt-2 small text-muted">Ship to: <?php echo htmlspecialchars($o['shipping_address']);?></div>
        <hr>
        <?php $details->bind_param("i", $o['order_id']); $details->execute(); $drs=$details->get_result(); ?>
        <?php while($d = $drs->fetch_assoc()): ?>
          <div class="d-flex justify-content-between">
            <div><?php echo htmlspecialchars($d['title']);?> × <?php echo $d['quantity'];?></div>
            <div>$<?php echo money($d['unit_price'] * $d['quantity']);?></div>
          </div>
        <?php endwhile; ?>
        <div class="text-end mt-2">Total: <strong>$<?php echo money($o['total_amount']);?></strong></div>
      </div>
    </div>
  <?php endwhile; ?>
</div>
<?php include("footer.php"); ?>
</body></html>
