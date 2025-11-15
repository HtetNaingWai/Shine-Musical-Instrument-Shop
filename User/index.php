<?php include("user_header.php"); ?>
<?php
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$cat = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;

$cats = $conn->query("SELECT category_id, category_name FROM product_category ORDER BY category_name");

$sql = "SELECT p.product_id, p.title, p.price, p.image, c.category_name 
        FROM products p 
        JOIN product_category c ON c.category_id=p.product_category_id";
$params = [];
if ($q !== '') {
    $sql .= " WHERE p.title LIKE ?";
    $stmt = $conn->prepare($sql);
    $like = "%$q%";
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $products = $stmt->get_result();
} elseif ($cat > 0) {
    $sql .= " WHERE p.product_category_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cat);
    $stmt->execute();
    $products = $stmt->get_result();
} else {
    $sql .= " ORDER BY p.product_id DESC LIMIT 8";
    $products = $conn->query($sql);
}

// Get featured products for slideshow (latest 5 products)
$featured_products = $conn->query("
    SELECT p.product_id, p.title, p.price, p.image, p.description 
    FROM products p 
    ORDER BY p.product_id DESC 
    LIMIT 5
");

// Get all categories with images
$categories = $conn->query("
    SELECT c.category_id, c.category_name, 
           COALESCE(p.image, 'default-category.jpg') as category_image
    FROM product_category c
    LEFT JOIN products p ON p.product_category_id = c.category_id
    GROUP BY c.category_id, c.category_name
");
?>

<!-- Hero Slideshow -->
<section class="hero-slider">
  <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <!-- Indicators -->
    <div class="carousel-indicators">
      <?php 
      $i = 0;
      while($featured = $featured_products->fetch_assoc()): 
      ?>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?php echo $i; ?>" 
                class="<?php echo $i == 0 ? 'active' : ''; ?>" aria-current="<?php echo $i == 0 ? 'true' : 'false'; ?>" 
                aria-label="Slide <?php echo $i + 1; ?>"></button>
      <?php 
        $i++;
      endwhile; 
      $featured_products->data_seek(0);
      ?>
    </div>

    <!-- Slides -->
    <div class="carousel-inner">
      <?php 
      $j = 0;
      while($featured = $featured_products->fetch_assoc()): 
      ?>
        <div class="carousel-item <?php echo $j == 0 ? 'active' : ''; ?>">
          <div class="carousel-image-wrapper">
            <img src="../image/<?php echo htmlspecialchars($featured['image']); ?>" 
                 class="d-block w-100" alt="<?php echo htmlspecialchars($featured['title']); ?>">
            <div class="carousel-overlay"></div>
          </div>
          <div class="carousel-caption">
            <div class="container">
              <div class="row justify-content-center text-center">
                <div class="col-lg-8">
                  <h1 class="display-4 fw-bold mb-3 text-white"><?php echo htmlspecialchars($featured['title']); ?></h1>
                  <p class="lead mb-4 text-light"><?php echo htmlspecialchars(substr($featured['description'], 0, 120) . '...'); ?></p>
                  <div class="d-flex align-items-center justify-content-center gap-3 mb-4">
                    <h3 class="text-warning mb-0">$<?php echo money($featured['price']); ?></h3>
                    <span class="badge bg-success fs-6">Featured</span>
                  </div>
                  <div class="d-flex gap-3 justify-content-center">
                    <a href="product_detail.php?id=<?php echo $featured['product_id']; ?>" 
                       class="btn btn-light btn-lg px-4 py-2">
                      <i class="fa fa-eye me-2"></i>View Details
                    </a>
                    <form method="post" action="cart.php" class="d-inline">
                      <input type="hidden" name="add_pid" value="<?php echo $featured['product_id']; ?>">
                      <button class="btn btn-primary btn-lg px-4 py-2" type="submit">
                        <i class="fa fa-cart-plus me-2"></i>Add to Cart
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php 
        $j++;
      endwhile; 
      ?>
    </div>

    <!-- Controls -->
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="visually-hidden">Next</span>
    </button>
  </div>
</section>

<!-- Categories Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold text-dark mb-3">Browse Categories</h2>
      <p class="lead text-muted">Discover our wide range of musical instruments</p>
    </div>
    <div class="row g-4">
      <!-- Electric Guitar -->
      <div class="col-6 col-md-4 col-lg-3">
        <a href="user_products.php?category=63" class="text-decoration-none">
          <div class="category-card">
            <div class="category-image">
              <img src="../image/electric-guitar-category.jpg" 
                   alt="Electric Guitar"
                   class="img-fluid">
              <div class="category-overlay"></div>
            </div>
            <div class="category-content text-center p-3">
              <h5 class="category-title mb-0">Electric Guitar</h5>
              <span class="category-link">Explore <i class="fa fa-arrow-right ms-1"></i></span>
            </div>
          </div>
        </a>
      </div>

      <!-- Acoustic Guitar -->
      <div class="col-6 col-md-4 col-lg-3">
        <a href="user_products.php?category=67" class="text-decoration-none">
          <div class="category-card">
            <div class="category-image">
              <img src="../image/acoustic-guitar-category.jpg" 
                   alt="Acoustic Guitar"
                   class="img-fluid">
              <div class="category-overlay"></div>
            </div>
            <div class="category-content text-center p-3">
              <h5 class="category-title mb-0">Acoustic Guitar</h5>
              <span class="category-link">Explore <i class="fa fa-arrow-right ms-1"></i></span>
            </div>
          </div>
        </a>
      </div>

      <!-- Piano -->
      <div class="col-6 col-md-4 col-lg-3">
        <a href="user_products.php?category=65" class="text-decoration-none">
          <div class="category-card">
            <div class="category-image">
              <img src="../image/piano-category.jpg" 
                   alt="Piano"
                   class="img-fluid">
              <div class="category-overlay"></div>
            </div>
            <div class="category-content text-center p-3">
              <h5 class="category-title mb-0">Piano</h5>
              <span class="category-link">Explore <i class="fa fa-arrow-right ms-1"></i></span>
            </div>
          </div>
        </a>
      </div>

      <!-- Ukelele -->
      <div class="col-6 col-md-4 col-lg-3">
        <a href="user_products.php?category=66" class="text-decoration-none">
          <div class="category-card">
            <div class="category-image">
              <img src="../image/ukelele-category.jpg" 
                   alt="Ukelele"
                   class="img-fluid">
              <div class="category-overlay"></div>
            </div>
            <div class="category-content text-center p-3">
              <h5 class="category-title mb-0">Ukelele</h5>
              <span class="category-link">Explore <i class="fa fa-arrow-right ms-1"></i></span>
            </div>
          </div>
        </a>
      </div>

      <!-- Electric Drum -->
      <div class="col-6 col-md-4 col-lg-3">
        <a href="user_products.php?category=68" class="text-decoration-none">
          <div class="category-card">
            <div class="category-image">
              <img src="../image/electric-drum-category.jpg" 
                   alt="Electric Drum"
                   class="img-fluid">
              <div class="category-overlay"></div>
            </div>
            <div class="category-content text-center p-3">
              <h5 class="category-title mb-0">Electric Drum</h5>
              <span class="category-link">Explore <i class="fa fa-arrow-right ms-1"></i></span>
            </div>
          </div>
        </a>
      </div>

      <!-- Violin -->
      <div class="col-6 col-md-4 col-lg-3">
        <a href="user_products.php?category=72" class="text-decoration-none">
          <div class="category-card">
            <div class="category-image">
              <img src="../image/violin-category.jpg" 
                   alt="Violin"
                   class="img-fluid">
              <div class="category-overlay"></div>
            </div>
            <div class="category-content text-center p-3">
              <h5 class="category-title mb-0">Violin</h5>
              <span class="category-link">Explore <i class="fa fa-arrow-right ms-1"></i></span>
            </div>
          </div>
        </a>
      </div>

      <!-- Bass Guitar -->
      <div class="col-6 col-md-4 col-lg-3">
        <a href="user_products.php?category=74" class="text-decoration-none">
          <div class="category-card">
            <div class="category-image">
              <img src="../image/bass-guitar-category.jpg" 
                   alt="Bass Guitar"
                   class="img-fluid">
              <div class="category-overlay"></div>
            </div>
            <div class="category-content text-center p-3">
              <h5 class="category-title mb-0">Bass Guitar</h5>
              <span class="category-link">Explore <i class="fa fa-arrow-right ms-1"></i></span>
            </div>
          </div>
        </a>
      </div>

      <!-- All Categories -->
      <div class="col-6 col-md-4 col-lg-3">
        <a href="user_products.php" class="text-decoration-none">
          <div class="category-card">
            <div class="category-image">
              <img src="../image/all-categories.jpg" 
                   alt="All Categories"
                   class="img-fluid">
              <div class="category-overlay"></div>
            </div>
            <div class="category-content text-center p-3">
              <h5 class="category-title mb-0">All Categories</h5>
              <span class="category-link">View All <i class="fa fa-arrow-right ms-1"></i></span>
            </div>
          </div>
        </a>
      </div>
    </div>
  </div>
</section>
<!-- Why Choose Us -->
<section class="py-5 bg-dark text-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold mb-3">Why Choose Shine Musical Instrument?</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-6">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <i class="fa fa-check-circle text-light fa-2x me-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-3">Expert Guidance</h4>
                        <p class="text-light">Our team of musicians provides personalized advice to help you choose the perfect instrument for your needs and skill level.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <i class="fa fa-check-circle text-light fa-2x me-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-3">Quality Assurance</h4>
                        <p class="text-light">Every instrument undergoes rigorous testing before it reaches you. We stand behind the quality of every product we sell.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <i class="fa fa-check-circle text-light fa-2x me-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-3">Competitive Prices</h4>
                        <p class="text-light">We work directly with manufacturers to bring you the best prices without compromising on quality.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex align-items-start">
                    <div class="flex-shrink-0">
                        <i class="fa fa-check-circle text-light fa-2x me-3"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-3">After-Sales Support</h4>
                        <p class="text-light">We provide ongoing support, maintenance tips, and guidance to ensure you get the most out of your instrument.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white">
    <div class="container text-center py-5">
        <h2 class="display-5 fw-bold mb-4">Ready to Find Your Perfect Instrument?</h2>
        <p class="lead mb-4">Join thousands of satisfied musicians who trust Shine Musical Instrument</p>
        <div class="d-flex gap-3 justify-content-center">
            <a href="index.php#products" class="btn btn-light btn-lg px-4 py-2">
                <i class="fa fa-shopping-bag me-2"></i>Shop Now
            </a>
            <a href="contact.php" class="btn btn-outline-light btn-lg px-4 py-2">
                <i class="fa fa-envelope me-2"></i>Contact Us
            </a>
        </div>
    </div>
</section>

<?php include("footer.php"); ?>

<style>
/* Hero Slider Styles */
.hero-slider {
  position: relative;
}

.carousel-image-wrapper {
  position: relative;
  height: 70vh;
  overflow: hidden;
}

.carousel-image-wrapper img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.carousel-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, rgba(0,0,0,0.7), rgba(0,0,0,0.3));
}

.carousel-caption {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  bottom: auto;
}

/* Category Cards */
.category-card {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  transition: all 0.3s ease;
  height: 100%;
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0,0,0,0.2);
}

.category-image {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.category-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.category-card:hover .category-image img {
  transform: scale(1.1);
}

.category-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(to bottom, transparent, rgba(0,0,0,0.7));
}

.category-content {
  position: relative;
  z-index: 2;
}

.category-title {
  color: #333;
  font-weight: 600;
}

.category-link {
  color: #0d6efd;
  font-size: 0.9rem;
  font-weight: 500;
}

/* Product Cards */
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

/* Responsive */
@media (max-width: 768px) {
  .carousel-image-wrapper {
    height: 50vh;
  }
  
  .carousel-caption h1 {
    font-size: 2rem;
  }
  
  .product-actions {
    opacity: 1;
  }
}
</style>
</body></html>