<?php include("user_header.php"); ?>
<?php
cart_init();

if (isset($_POST['add_pid'])) {
    add_to_cart($_POST['add_pid'], (int)($_POST['qty'] ?? 1));
    header("Location: cart.php");
    exit;
}
if (isset($_POST['update'])) {
    foreach($_POST['qty'] as $pid => $q) update_cart_qty($pid, $q);
}
if (isset($_GET['remove'])) {
    remove_from_cart((int)$_GET['remove']);
    header("Location: cart.php");
    exit;
}
$cart = $_SESSION['cart'];
$items = [];
$subtotal = 0;

if ($cart) {
    $ids = implode(",", array_map('intval', array_keys($cart)));
    $rs = $conn->query("SELECT product_id, title, price, image FROM products WHERE product_id IN ($ids)");
    while($row = $rs->fetch_assoc()){
        $row['qty'] = $cart[$row['product_id']];
        $row['line'] = $row['qty'] * $row['price'];
        $subtotal += $row['line'];
        $items[] = $row;
    }
}

$promo_code = $_SESSION['promo_code'] ?? '';
$discount = 0;
if ($promo_code) {
    if ($p = active_promotion($promo_code, $conn)) {
        // promotion_amount is percent (e.g., 10.00 for 10%)
        $discount = round($subtotal * ((float)$p['promotion_amount'] / 100.0), 2);
    } else {
        unset($_SESSION['promo_code']); // expired
    }
}
$total = max(0, $subtotal - $discount);

if (isset($_POST['apply_promo'])) {
    $code = trim($_POST['promo']);
    if ($code !== '' && active_promotion($code, $conn)) {
        $_SESSION['promo_code'] = $code;
    } else {
        $_SESSION['promo_code'] = '';
        $promo_error = "Invalid or inactive promotion.";
    }
    header("Location: cart.php");
    exit;
}
?>
<div class="container py-4">
  <h4 class="mb-3">Shopping Cart</h4>
  <?php if (empty($items)): ?>
    <div class="alert alert-info">Your cart is empty. <a href="index.php">Continue shopping</a></div>
  <?php else: ?>
  <form method="post">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead><tr><th>Product</th><th>Price</th><th style="width:140px">Qty</th><th>Total</th><th></th></tr></thead>
        <tbody>
          <?php foreach($items as $it): ?>
          <tr>
            <td>
              <div class="d-flex align-items-center gap-3">
                <img src="../image/<?php echo htmlspecialchars($it['image']);?>" width="70" class="rounded border">
                <div>
                  <a href="product_detail.php?id=<?php echo $it['product_id'];?>"><?php echo htmlspecialchars($it['title']);?></a>
                </div>
              </div>
            </td>
            <td>$<?php echo money($it['price']);?></td>
            <td>
              <input type="number" class="form-control" name="qty[<?php echo $it['product_id'];?>]" value="<?php echo $it['qty'];?>" min="0">
            </td>
            <td>$<?php echo money($it['line']);?></td>
            <td><a class="btn btn-sm btn-outline-danger" href="cart.php?remove=<?php echo $it['product_id'];?>"><i class="fa fa-trash"></i></a></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="row g-3">
      <div class="col-md-6">
        <div class="input-group">
          <input type="text" name="promo" class="form-control" placeholder="Promotion code" value="<?php echo htmlspecialchars($promo_code);?>">
          <button class="btn btn-outline-secondary" name="apply_promo" value="1">Apply</button>
        </div>
        <?php if (!empty($promo_error)): ?><div class="small text-danger mt-1"><?php echo $promo_error;?></div><?php endif; ?>
      </div>
      <div class="col-md-6 text-end">
        <div>Subtotal: <strong>$<?php echo money($subtotal);?></strong></div>
        <div>Discount: <strong class="text-success">-$<?php echo money($discount);?></strong></div>
        <div class="fs-5">Total: <strong>$<?php echo money($total);?></strong></div>
      </div>
    </div>
    <div class="d-flex justify-content-between mt-3">
      <a href="index.php" class="btn btn-outline-secondary">Continue Shopping</a>
      <div>
        <button class="btn btn-outline-dark me-2" name="update" value="1">Update Cart</button>
        <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
      </div>
    </div>
  </form>
  <?php endif; ?>
</div>
<?php include("footer.php"); ?>
</body></html>
