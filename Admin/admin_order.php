<?php
include('../Database/database.php');
echo "<h4 class='mb-3'>Orders</h4>";

/* update status */
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['order_id'], $_POST['order_status'])) {
  $oid=(int)$_POST['order_id'];
  $st = $_POST['order_status'];
  $stmt=$conn->prepare("UPDATE order_ SET order_status=? WHERE order_id=?");
  $stmt->bind_param("si",$st,$oid);
  $stmt->execute();
  echo "<div class='alert alert-success'>Order #$oid status updated to <b>".htmlspecialchars($st)."</b>.</div>";
}

/* list orders */
$q = "SELECT o.order_id,o.order_status,o.order_date,o.total_amount,o.shipping_address,
            u.user_name,u.email
      FROM order_ o 
      JOIN users u ON u.user_id=o.customer_id
      ORDER BY o.order_date DESC";
$orders = $conn->query($q);

/* details prepared stmt */
$detail = $conn->prepare("SELECT p.title,d.quantity,d.unit_price 
                          FROM order_detail d 
                          JOIN products p ON p.product_id=d.product_item_id
                          WHERE d.order_id=?");
?>
<div class="table-responsive">
<table class="table table-hover align-middle">
  <thead>
    <tr><th>ID</th><th>Customer</th><th>Date</th><th>Status</th><th>Total</th><th>Details</th><th>Action</th></tr>
  </thead>
  <tbody>
  <?php while($o=$orders->fetch_assoc()): ?>
    <tr>
      <td>#<?php echo $o['order_id'];?></td>
      <td>
        <?php echo htmlspecialchars($o['user_name']);?><br>
        <small class="text-muted"><?php echo htmlspecialchars($o['email']);?></small>
      </td>
      <td><?php echo $o['order_date'];?></td>
      <td>
        <form method="post" class="d-flex gap-2">
          <input type="hidden" name="order_id" value="<?php echo $o['order_id'];?>">
          <select name="order_status" class="form-select form-select-sm" style="width:150px">
            <?php
              $opts=['pending','processing','shipped','delivered','cancelled'];
              foreach($opts as $s){
                $sel = $s===$o['order_status']?'selected':'';
                echo "<option $sel>$s</option>";
              }
            ?>
          </select>
          <button class="btn btn-sm btn-outline-primary">Save</button>
        </form>
      </td>
      <td>$<?php echo number_format($o['total_amount'],2);?></td>
      <td>
        <?php
          $detail->bind_param("i",$o['order_id']);
          $detail->execute(); $drs=$detail->get_result();
          while($d=$drs->fetch_assoc()){
            echo "<div>".htmlspecialchars($d['title'])." × ".$d['quantity']." — $".number_format($d['quantity']*$d['unit_price'],2)."</div>";
          }
        ?>
        <div class="small text-muted mt-1">Ship: <?php echo htmlspecialchars($o['shipping_address']);?></div>
      </td>
      <td>
        <?php if($o['order_status']!=='cancelled'): ?>
          <form method="post">
            <input type="hidden" name="order_id" value="<?php echo $o['order_id'];?>">
            <input type="hidden" name="order_status" value="cancelled">
            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Cancel order #<?php echo $o['order_id'];?>?')">Cancel</button>
          </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>
</div>
