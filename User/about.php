<?php include("user_header.php"); ?>

<!-- Hero Section -->
<section class="py-5 bg-light text-white">
    <div class="container py-5">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold text-black mb-4">About Shine Musical Instrument</h1>
                <p class="lead text-black mb-4">Your trusted partner in the world of music. We bring quality instruments to musicians of all levels, from beginners to professionals.</p>
                
            </div>
            <div class="col-lg-6 text-center">
                <img src="../image/about.jpg" alt="Music Store" class="img-fluid rounded-3 shadow-lg" style="max-height: 400px;">
            </div>
        </div>
    </div>
</section>

<!-- Our Story -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <img src="../image/about3.jpg" alt="Our Music Store" class="img-fluid rounded-3 shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold text-dark mb-4">Our Story</h2>
                <p class="lead text-muted mb-4">Founded with a passion for music and a vision to make quality instruments accessible to everyone.</p>
                <p class="mb-4">Shine Musical Instrument started as a small family business in 2025. Our founder, a passionate musician himself, noticed the lack of affordable yet high-quality instruments in the market. What began as a humble store has now grown into a trusted destination for musicians across the country.</p>
                <p class="mb-4">We believe that everyone deserves the opportunity to create beautiful music. That's why we carefully select each instrument in our collection, ensuring they meet our high standards for sound quality, durability, and playability.</p>
                <p>Join us on our musical journey and let Shine Musical Instrument be a part of your story.</p>

                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Values -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-dark mb-3">Our Values</h2>
            <p class="lead text-muted">What makes us different</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="value-icon bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa fa-gem fa-2x"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Quality First</h4>
                    <p class="text-muted">Every instrument is carefully selected and tested to ensure it meets our high standards for sound quality and craftsmanship.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="value-icon bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa fa-handshake fa-2x"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Customer Trust</h4>
                    <p class="text-muted">We build lasting relationships with our customers through honest advice, excellent service, and reliable support.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="value-icon bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fa fa-music fa-2x"></i>
                    </div>
                    <h4 class="fw-bold mb-3">Passion for Music</h4>
                    <p class="text-muted">We're musicians serving musicians. Our team shares your passion and understands your needs.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<style>
.team-card {
    padding: 2rem;
    border-radius: 15px;
    transition: transform 0.3s ease;
}

.team-card:hover {
    transform: translateY(-5px);
}

.team-image img {
    width: 200px;
    height: 200px;
    object-fit: cover;
    border: 5px solid #f8f9fa;
}

.value-icon {
    transition: transform 0.3s ease;
}

.value-icon:hover {
    transform: scale(1.1);
}

.social-links a {
    transition: color 0.3s ease;
}

.social-links a:hover {
    color: #0d6efd !important;
}

.hero-section {
    background: linear-gradient(135deg, #a2a5aaff 0%, #7e8586ff 100%);
}


.team-card {
    background: white;
    box-shadow: 0 5px 15px rgba(87, 83, 83, 0.1);
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }
    
    .display-5 {
        font-size: 2rem;
    }
}
</style>

<?php include("footer.php"); ?>
</body></html>