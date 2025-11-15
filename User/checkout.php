<?php include("user_header.php"); require_login(); ?>
<?php
cart_init();
$cart = $_SESSION['cart'] ?? [];
if (!$cart) { header("Location: cart.php"); exit; }

$items=[]; $subtotal=0;
$ids = implode(",", array_map('intval', array_keys($cart)));
$rs = $conn->query("SELECT product_id, title, price FROM products WHERE product_id IN ($ids)");
while($row = $rs->fetch_assoc()){
  $row['qty'] = $cart[$row['product_id']];
  $row['line'] = $row['qty'] * $row['price'];
  $subtotal += $row['line'];
  $items[] = $row;
}

$discount=0; $promo_code = $_SESSION['promo_code'] ?? '';
$promo_error = '';
$promotion_id = null;

if ($promo_code) {
    // Check if promotion exists and is active
    $p = active_promotion($promo_code, $conn);
    if ($p) {
        $promotion_id = $p['promotion_id'];
        // Check if user has already used this promotion
        $user_id = (int)$_SESSION['user']['user_id'];
        $check_usage = $conn->prepare("SELECT * FROM promotion_usage WHERE promotion_id = ? AND user_id = ?");
        $check_usage->bind_param("ii", $promotion_id, $user_id);
        $check_usage->execute();
        $usage_result = $check_usage->get_result();
        
        if ($usage_result->num_rows > 0) {
            // User has already used this promotion
            $promo_error = "You have already used this promotion code.";
            $promo_code = '';
            $promotion_id = null;
            unset($_SESSION['promo_code']);
        } else {
            // User can use this promotion
            $discount = round($subtotal * ((float)$p['promotion_amount']/100.0), 2);
        }
        $check_usage->close();
    } else {
        $promo_error = "Invalid or expired promotion code.";
        $promo_code = '';
        $promotion_id = null;
        unset($_SESSION['promo_code']);
    }
}

$total = max(0, $subtotal - $discount);

$done = false;
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $addr = trim($_POST['shipping_address'] ?? '');
    $method = $_POST['payment_method'] ?? 'Cash on Delivery';
    if ($addr==='') $addr = 'N/A';

    // create order
    $uid = (int)$_SESSION['user']['user_id'];
    $stmt = $conn->prepare("INSERT INTO order_ (customer_id, order_status, total_amount, shipping_address, promo_code, promo_amount) VALUES (?,?,?,?,?,?)");
    $status = 'pending';
    $stmt->bind_param("isdssd", $uid, $status, $total, $addr, $promo_code, $discount);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // order details
    $od = $conn->prepare("INSERT INTO order_detail (order_id, product_item_id, quantity, unit_price, discount_amount) VALUES (?,?,?,?,?)");
    foreach($items as $it){
        $pid = $it['product_id']; $qty=$it['qty']; $price=$it['price']; $disc=0.00;
        $od->bind_param("iiidd", $order_id, $pid, $qty, $price, $disc);
        $od->execute();

        // reduce stock
        $conn->query("UPDATE products SET stock = GREATEST(0, stock - ".(int)$qty.") WHERE product_id=".(int)$pid);
    }

    // Record promotion usage if promo code was used
    if ($promo_code && $promotion_id) {
        $usage_stmt = $conn->prepare("INSERT INTO promotion_usage (promotion_id, user_id, order_id) VALUES (?, ?, ?)");
        $usage_stmt->bind_param("iii", $promotion_id, $uid, $order_id);
        $usage_stmt->execute();
        $usage_stmt->close();
    }

    // payment record
    $pay_status = ($method === 'Cash on Delivery') ? 'pending' : 'completed';
    $pay = $conn->prepare("INSERT INTO payment (order_id, payment_amount, payment_method, payment_status, payment_date) VALUES (?,?,?,?, NOW())");
    $pay->bind_param("idss", $order_id, $total, $method, $pay_status);
    $pay->execute();

    // clear cart and promo code
    $_SESSION['cart'] = [];
    unset($_SESSION['promo_code']);
    $done = true;
}
?>
<div class="container py-4" style="max-width:800px">
<?php if ($done): ?>
  <div class="alert alert-success">
    <h5 class="mb-1">Thank you! Your order has been placed.</h5>
    <div>Order ID: <strong>#<?php echo $order_id;?></strong></div>
    <?php if ($promo_code): ?>
    <div>Promotion Code: <strong><?php echo htmlspecialchars($promo_code); ?></strong> applied successfully!</div>
    <?php endif; ?>
    <div class="mt-2"><a class="btn btn-outline-secondary" href="order_history.php">Track your order</a></div>
  </div>
<?php else: ?>
  <h4 class="mb-3">Checkout</h4>
  
  <!-- Promotion Code Status -->
  <?php if ($promo_error): ?>
    <div class="alert alert-warning">
      <?php echo $promo_error; ?>
    </div>
  <?php endif; ?>
  
  <?php if ($promo_code && !$promo_error): ?>
    <div class="alert alert-success">
      Promotion code <strong><?php echo htmlspecialchars($promo_code); ?></strong> applied! 
      You saved $<?php echo money($discount); ?>
    </div>
  <?php endif; ?>

  <div class="card mb-3">
    <div class="card-body">
      <?php foreach($items as $it): ?>
        <div class="d-flex justify-content-between">
          <div><?php echo htmlspecialchars($it['title']);?> × <?php echo $it['qty'];?></div>
          <div>$<?php echo money($it['line']);?></div>
        </div>
      <?php endforeach; ?>
      <hr>
      <div class="d-flex justify-content-between"><div>Subtotal</div><div>$<?php echo money($subtotal);?></div></div>
      <?php if ($discount > 0): ?>
      <div class="d-flex justify-content-between"><div>Discount (<?php echo htmlspecialchars($promo_code); ?>)</div><div class="text-success">-$<?php echo money($discount);?></div></div>
      <?php endif; ?>
      <div class="d-flex justify-content-between fs-5"><div>Total</div><div>$<?php echo money($total);?></div></div>
    </div>
  </div>
  <form method="post">
    <div class="mb-3">
      <label class="form-label">Shipping Address</label>
      <textarea name="shipping_address" class="form-control" rows="3" required></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Payment Method</label>
      <select name="payment_method" class="form-select">
        <option>Cash on Delivery</option>
        <option>Credit/Debit Card</option>
        <option>Mobile Banking</option>
      </select>
    </div>
    <button class="btn btn-primary w-100">Confirm Purchase</button>
  </form>
<?php endif; ?>
</div>
<?php include("footer.php"); ?>
</body></html>