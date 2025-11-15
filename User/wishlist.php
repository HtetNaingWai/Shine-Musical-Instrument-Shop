<?php include("user_header.php"); 

// Handle remove from wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $product_id = (int)$_POST['product_id'];
    $delete_sql = "DELETE FROM wishlist WHERE user_id = ? AND product_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $user_id, $product_id);
    $delete_stmt->execute();
    $delete_stmt->close();
    
    $success_message = "Product removed from wishlist!";
}

// Handle move to cart
if (isset($_POST['move_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    // You'll need to implement your cart logic here
    $success_message = "Product moved to cart!";
}

// Get user's wishlist items
$wishlist_sql = "
    SELECT 
        w.wishlist_id,
        w.product_id,
        p.title,
        p.price,
        p.image,
        p.description,
        p.stock,
        c.category_name,
        w.created_at
    FROM wishlist w
    JOIN products p ON w.product_id = p.product_id
    JOIN product_category c ON p.product_category_id = c.category_id
    WHERE w.user_id = ?
    ORDER BY w.created_at DESC
";

$wishlist_stmt = $conn->prepare($wishlist_sql);
$wishlist_stmt->bind_param("i", $user_id);
$wishlist_stmt->execute();
$wishlist_result = $wishlist_stmt->get_result();
$wishlist_count = $wishlist_result->num_rows;
?>

<!-- Wishlist Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <!-- Alert Messages -->
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                        <i class="fa fa-check-circle fa-2x me-3 text-success"></i>
                        <div class="flex-grow-1">
                            <h5 class="alert-heading mb-1">Success!</h5>
                            <p class="mb-0"><?php echo $success_message; ?></p>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Wishlist Summary -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h4 class="mb-0">My Wishlist</h4>
                                <p class="text-muted mb-0"><?php echo $wishlist_count; ?> item(s) saved</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <a href="user_products.php" class="btn btn-outline-primary">
                                    <i class="fa fa-arrow-left me-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($wishlist_count > 0): ?>
                    <!-- Wishlist Items -->
                    <div class="row g-4">
                        <?php while($item = $wishlist_result->fetch_assoc()): ?>
                            <div class="col-12">
                                <div class="card wishlist-item shadow-sm border-0">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <!-- Product Image -->
                                            <div class="col-md-2">
                                                <img src="../image/<?php echo htmlspecialchars($item['image']); ?>" 
                                                     class="img-fluid rounded" alt="<?php echo htmlspecialchars($item['title']); ?>"
                                                     style="height: 120px; object-fit: cover;">
                                            </div>
                                            
                                            <!-- Product Details -->
                                            <div class="col-md-5">
                                                <h5 class="product-title mb-2"><?php echo htmlspecialchars($item['title']); ?></h5>
                                                <p class="text-muted mb-2 small"><?php echo htmlspecialchars($item['category_name']); ?></p>
                                                <p class="text-muted mb-2 small"><?php echo htmlspecialchars(substr($item['description'], 0, 100) . '...'); ?></p>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-<?php echo $item['stock'] > 0 ? 'success' : 'danger'; ?> me-2">
                                                        <?php echo $item['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                                    </span>
                                                    <small class="text-muted">
                                                        <i class="fa fa-calendar me-1"></i>
                                                        Added <?php echo date('M j, Y', strtotime($item['created_at'])); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            
                                            <!-- Price -->
                                            <div class="col-md-2 text-center">
                                                <h4 class="text-primary fw-bold mb-0">$<?php echo money($item['price']); ?></h4>
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="col-md-3">
                                                <div class="d-grid gap-2">
                                                    <?php if ($item['stock'] > 0): ?>
                                                        <form method="post" action="cart.php" class="d-inline">
                                                            <input type="hidden" name="add_pid" value="<?php echo $item['product_id']; ?>">
                                                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                                                <i class="fa fa-cart-plus me-2"></i>Add to Cart
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                    
                                                    <form method="post" class="d-inline">
                                                        <input type="hidden" name="product_id" value="<?php echo $item['product_id']; ?>">
                                                        <button type="submit" name="remove_from_wishlist" class="btn btn-outline-danger btn-sm w-100">
                                                            <i class="fa fa-trash me-2"></i>Remove
                                                        </button>
                                                    </form>
                                                    
                                                    <a href="product_detail.php?id=<?php echo $item['product_id']; ?>" class="btn btn-outline-dark btn-sm w-100">
                                                        <i class="fa fa-eye me-2"></i>View Details
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Empty Wishlist CTA -->
                    <div class="text-center mt-5">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-5">
                                <i class="fa fa-heart fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Love more products?</h4>
                                <p class="text-muted mb-4">Discover more amazing products to add to your wishlist</p>
                                <a href="user_products.php" class="btn btn-primary btn-lg">
                                    <i class="fa fa-shopping-bag me-2"></i>Explore Products
                                </a>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <!-- Empty Wishlist State -->
                    <div class="text-center py-5">
                        <div class="card border-0 bg-light">
                            <div class="card-body py-5">
                                <i class="fa fa-heart fa-4x text-muted mb-4"></i>
                                <h2 class="text-muted mb-3">Your wishlist is empty</h2>
                                <p class="text-muted mb-4">Start saving your favorite products to your wishlist for easy access later.</p>
                                <div class="d-flex gap-3 justify-content-center">
                                    <a href="user_products.php" class="btn btn-primary btn-lg">
                                        <i class="fa fa-shopping-bag me-2"></i>Start Shopping
                                    </a>
                                    <a href="promotions.php" class="btn btn-outline-primary btn-lg">
                                        <i class="fa fa-tags me-2"></i>View Promotions
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
.wishlist-item {
    border-radius: 15px;
    transition: all 0.3s ease;
}

.wishlist-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.product-title {
    color: #333;
    font-weight: 600;
    line-height: 1.3;
}

.card {
    border-radius: 15px;
}

.btn {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.alert {
    border-radius: 10px;
    border: none;
}

@media (max-width: 768px) {
    .display-6 {
        font-size: 2rem;
    }
    
    .wishlist-item .row > div {
        margin-bottom: 1rem;
    }
    
    .wishlist-item .row > div:last-child {
        margin-bottom: 0;
    }
}
</style>

<?php include("footer.php"); ?>
</body></html>