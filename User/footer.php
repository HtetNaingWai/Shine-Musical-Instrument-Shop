<!-- Footer -->
<footer class="bg-dark text-white pt-5 pb-4">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">
                    <i class="fas fa-music me-2"></i>Shine Musical Instrument
                </h5>
                <p class="text-light">
                    Your trusted partner for quality musical instruments. We provide the best guitars, 
                    pianos, drums, and more for musicians of all levels.
                </p>
                <div class="mt-3">
                    <a href="https://www.facebook.com" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://x.com" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.instagram.com" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.youtube.com" class="text-white"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-light text-decoration-none">Home</a></li>
                    <li class="mb-2"><a href="index.php#products" class="text-light text-decoration-none">Products</a></li>
                    <li class="mb-2"><a href="about.php" class="text-light text-decoration-none">About Us</a></li>
                    <li class="mb-2"><a href="wishlist.php" class="text-light text-decoration-none">Wishlist</a></li>
                    <li class="mb-2"><a href="contact.php" class="text-light text-decoration-none">Contact</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">Categories</h5>
                <ul class="list-unstyled">
                    <?php
                    $categories = $conn->query("SELECT category_name FROM product_category LIMIT 5");
                    while($cat = $categories->fetch_assoc()): 
                    ?>
                        <li class="mb-2">
                            <a href="index.php?cat=<?php echo $cat['category_id'] ?? ''; ?>" 
                               class="text-light text-decoration-none">
                                <?php echo htmlspecialchars($cat['category_name']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-map-marker-alt me-3"></i>
                        <span class="text-light">Mawadi Min Gyi Street, Mandalay, Myanmar</span>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-phone me-3"></i>
                        <a href="tel:+959123456789" class="text-light text-decoration-none">+95 9 123 456 789</a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-envelope me-3"></i>
                        <a href="shinemusicalinstruments@gmail.com" class="text-light text-decoration-none">shinemusicalinstruments@gmail.com</a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-clock me-3"></i>
                        <span class="text-light">Mon - Sat: 9:00 AM - 6:00 PM</span>
                    </li>
                </ul>
            </div>
        </div>

        <hr class="my-4 bg-light">

        <!-- Bottom Footer -->
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0 text-light">
                    &copy; <?php echo date('Y'); ?> Shine Musical Instrument. All rights reserved.
                </p>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="https://policies.google.com/privacy?hl=en-US" class="text-light text-decoration-none me-3">Privacy Policy</a>
                <a href="https://policies.google.com/terms?hl=en-US" class="text-light text-decoration-none">Terms of Service</a>
            </div>
        </div>
    </div>
</footer>

</body>
</html>