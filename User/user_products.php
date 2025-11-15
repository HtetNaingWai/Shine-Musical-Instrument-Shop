<?php include("user_header.php"); ?>

<?php
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Handle wishlist actions - SIMPLE VERSION
if (isset($_POST['add_to_wishlist'])) {
    $product_id = (int)$_POST['product_id'];
    
    // For demo - you can store in session or temporary table
    // This is a simple demo using session
    if (!isset($_SESSION['wishlist_demo'])) {
        $_SESSION['wishlist_demo'] = [];
    }
    
    if (!in_array($product_id, $_SESSION['wishlist_demo'])) {
        $_SESSION['wishlist_demo'][] = $product_id;
        echo "<script>alert('Product added to wishlist!');</script>";
    } else {
        echo "<script>alert('Product already in wishlist!');</script>";
    }
}

if (isset($_POST['remove_from_wishlist'])) {
    $product_id = (int)$_POST['product_id'];
    
    if (isset($_SESSION['wishlist_demo'])) {
        $key = array_search($product_id, $_SESSION['wishlist_demo']);
        if ($key !== false) {
            unset($_SESSION['wishlist_demo'][$key]);
            echo "<script>alert('Product removed from wishlist!');</script>";
        }
    }
}

// Get categories
$categories = $conn->query("SELECT category_id, category_name FROM product_category ORDER BY category_name");

// Build products query
$sql = "SELECT p.product_id, p.title, p.price, p.image, p.description, c.category_name
        FROM products p 
        JOIN product_category c ON c.category_id = p.product_category_id 
        WHERE 1=1";
        
if ($category_id > 0) {
    $sql .= " AND p.product_category_id = $category_id";
}

if ($q !== '') {
    $sql .= " AND p.title LIKE '%" . $conn->real_escape_string($q) . "%'";
}

$sql .= " ORDER BY p.product_id DESC LIMIT $limit OFFSET $offset";

$products = $conn->query($sql);

// Count for pagination
$count_sql = "SELECT COUNT(*) as total FROM products p WHERE 1=1";
if ($category_id > 0) $count_sql .= " AND p.product_category_id = $category_id";
if ($q !== '') $count_sql .= " AND p.title LIKE '%" . $conn->real_escape_string($q) . "%'";

$total_result = $conn->query($count_sql);
$total_data = $total_result->fetch_assoc();
$total_products = $total_data['total'];
$total_pages = ceil($total_products / $limit);
?>

<div class="container py-5">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-secondry text-dark">
                    <h5 class="card-title mb-0"><i class="fa fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <form method="get" class="mb-4">
                        <input type="hidden" name="category" value="<?php echo $category_id; ?>">
                        <div class="input-group">
                            <input type="text" name="q" class="form-control" placeholder="Search products..." 
                                   value="<?php echo htmlspecialchars($q); ?>">
                            <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                        </div>
                    </form>

                    <h6 class="fw-bold mb-3">Categories</h6>
                    <div class="list-group list-group-flush">
                        <a href="user_products.php" 
                           class="list-group-item list-group-item-action <?php echo $category_id == 0 ? 'active' : ''; ?>">
                            All Categories
                        </a>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <a href="user_products.php?category=<?php echo $cat['category_id']; ?>" 
                               class="list-group-item list-group-item-action <?php echo $category_id == $cat['category_id'] ? 'active' : ''; ?>">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-0">Products</h4>
                    <small class="text-muted">Page <?php echo $page; ?> of <?php echo $total_pages; ?></small>
                </div>
                <div class="d-flex gap-2">
                    <a href="wishlist.php" class="btn btn-outline-danger">
                        <i class="fa fa-heart me-1"></i>Wishlist
                    </a>
                    <a href="promotions.php" class="btn btn-outline-primary">
                        <i class="fa fa-tags me-1"></i>Promotions
                    </a>
                </div>
            </div>

            <?php if ($products->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while($p = $products->fetch_assoc()): ?>
                        <div class="col-6 col-md-4 col-lg-4">
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="../image/<?php echo htmlspecialchars($p['image']);?>" 
                                         class="img-fluid" alt="<?php echo htmlspecialchars($p['title']); ?>">
                                    <div class="product-actions">
                                        <a href="product_detail.php?id=<?php echo $p['product_id'];?>" class="btn btn-sm btn-light">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        
                                        <!-- Wishlist Button - ALWAYS VISIBLE -->
                                        <form method="post" class="d-inline">
                                            <input type="hidden" name="product_id" value="<?php echo $p['product_id']; ?>">
                                            <?php 
                                            // Check if product is in session wishlist
                                            $in_wishlist = isset($_SESSION['wishlist_demo']) && in_array($p['product_id'], $_SESSION['wishlist_demo']);
                                            ?>
                                            <?php if ($in_wishlist): ?>
                                                <button type="submit" name="remove_from_wishlist" class="btn btn-sm btn-danger" title="Remove from Wishlist">
                                                    <i class="fa fa-heart"></i>
                                                </button>
                                            <?php else: ?>
                                                <button type="submit" name="add_to_wishlist" class="btn btn-sm btn-outline-danger" title="Add to Wishlist">
                                                    <i class="fa fa-heart"></i>
                                                </button>
                                            <?php endif; ?>
                                        </form>
                                        
                                        <form method="post" action="cart.php" class="d-inline">
                                            <input type="hidden" name="add_pid" value="<?php echo $p['product_id'];?>">
                                            <button class="btn btn-sm btn-primary" type="submit" title="Add to Cart">
                                                <i class="fa fa-cart-plus"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <div class="product-info p-3">
                                    <h6 class="product-title mb-2"><?php echo htmlspecialchars($p['title']);?></h6>
                                    <div class="small text-muted mb-2"><?php echo htmlspecialchars($p['category_name']);?></div>
                                    <p class="small text-muted mb-2"><?php echo htmlspecialchars(substr($p['description'], 0, 60) . '...'); ?></p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="product-price fw-bold text-primary">$<?php echo number_format($p['price'], 2); ?></span>
                                        <span class="badge bg-success">In Stock</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?category=<?php echo $category_id; ?>&q=<?php echo urlencode($q); ?>&page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="?category=<?php echo $category_id; ?>&q=<?php echo urlencode($q); ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?category=<?php echo $category_id; ?>&q=<?php echo urlencode($q); ?>&page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">No products found</h4>
                    <p class="text-muted">Try adjusting your search or filter criteria</p>
                    <a href="user_products.php" class="btn btn-primary">View All Products</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    height: 100%;
}

.product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
}

.product-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-actions {
    position: absolute;
    top: 10px;
    right: 10px;
    opacity: 0;
    transition: opacity 0.3s ease;
    display: flex;
    gap: 5px;
}

.product-card:hover .product-actions {
    opacity: 1;
}

.product-info {
    background: white;
}

.product-title {
    color: #333;
    font-weight: 600;
    line-height: 1.3;
}

.product-price {
    font-size: 1.1rem;
}

.list-group-item.active {
    background-color: #656a71ff;
    border-color: #010101ff;
}

@media (max-width: 768px) {
    .product-actions {
        opacity: 1;
    }
}
</style>

<?php include("footer.php"); ?>
</body></html>