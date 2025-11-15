<?php include("user_header.php"); ?>
<?php
$id = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT p.*, c.category_name, m.mname 
                        FROM products p 
                        JOIN product_category c ON c.category_id=p.product_category_id
                        JOIN manufacturer m ON m.mid=p.manufacturer_id
                        WHERE product_id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) { echo "<div class='container py-5'>Product not found.</div></body></html>"; exit; }

$reviews = $conn->prepare("SELECT r.review_id, r.rating, r.review_text, r.created_at, u.user_name
                           FROM review_rating r
                           JOIN users u ON u.user_id=r.user_id
                           WHERE r.product_id=? AND r.review_status IN ('pending','approved')
                           ORDER BY r.created_at DESC");
$reviews->bind_param("i", $id);
$reviews->execute();
$rev_rs = $reviews->get_result();
?>
<div class="container py-4">
  <div class="row g-4">
    <div class="col-lg-5">
      <img class="img-fluid rounded border" src="../image/<?php echo htmlspecialchars($product['image']);?>" alt="">
    </div>
    <div class="col-lg-7">
      <h3><?php echo htmlspecialchars($product['title']);?></h3>
      <div class="text-muted mb-2"><?php echo htmlspecialchars($product['category_name']);?> • <?php echo htmlspecialchars($product['mname']);?></div>
      <div class="fs-4 fw-bold mb-3">$<?php echo money($product['price']);?></div>
      <p><?php echo htmlspecialchars($product['description']);?></p>
      <div class="d-flex gap-2">
        <form method="post" action="cart.php" class="d-flex gap-2">
          <input type="hidden" name="add_pid" value="<?php echo $product['product_id'];?>">
          <input type="number" name="qty" min="1" value="1" class="form-control" style="width:100px">
          <button class="btn btn-dark"><i class="fa fa-cart-plus me-1"></i>Add to Cart</button>
        </form>
      </div>
    </div>
  </div>

  <hr class="my-4">
  <div class="row">
    <div class="col-lg-7">
      <h5 class="mb-3">Customer Reviews</h5>
      <?php if ($rev_rs->num_rows==0): ?>
        <div class="text-muted">No reviews yet.</div>
      <?php endif; ?>
      <?php while($r = $rev_rs->fetch_assoc()): ?>
        <div class="border rounded p-3 mb-3">
          <div class="d-flex justify-content-between">
            <div><strong><?php echo htmlspecialchars($r['user_name']);?></strong></div>
            <div class="text-warning"><?php echo str_repeat("★", (int)$r['rating']); ?><span class="text-muted"><?php echo str_repeat("☆", 5-(int)$r['rating']);?></span></div>
          </div>
          <div class="small text-muted"><?php echo $r['created_at']; ?></div>
          <p class="mb-0 mt-2"><?php echo nl2br(htmlspecialchars($r['review_text']));?></p>
        </div>
      <?php endwhile; ?>
    </div>
    <div class="col-lg-5">
      <h5 class="mb-3">Write a Review</h5>
      <?php if (!is_logged_in()): ?>
        <div class="alert alert-info">Please <a href="login.php?next=<?php echo urlencode($_SERVER['REQUEST_URI']);?>">login</a> to post a review.</div>
      <?php else: ?>
      <form method="post" action="review.php">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id'];?>">
        <div class="mb-3">
          <label class="form-label">Rating</label>
          <select name="rating" class="form-select" required>
            <option value="5">★★★★★</option>
            <option value="4">★★★★☆</option>
            <option value="3">★★★☆☆</option>
            <option value="2">★★☆☆☆</option>
            <option value="1">★☆☆☆☆</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Review</label>
          <textarea name="review_text" rows="4" class="form-control" placeholder="Share your thoughts..."></textarea>
        </div>
        <button class="btn btn-primary">Submit Review</button>
      </form>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php include("footer.php"); ?>
</body></html>
