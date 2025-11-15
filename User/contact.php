<?php include("user_header.php"); ?>
<?php
$adminEmail = "shinemusicalinstruments@example.com";

// create table if not exists (kept simple)
$conn->query("
  CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    subject VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$sent = false; $err = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $uid = isset($_SESSION['user']) ? (int)$_SESSION['user']['user_id'] : null;

    if ($name==='' || $email==='' || $subject==='' || $message==='') {
        $err = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?,?,?,?,?)");
        $stmt->bind_param("issss", $uid, $name, $email, $subject, $message);
        $sent = $stmt->execute();
        if (!$sent) $err = "Could not send your message. Please try again.";
        // Optional: also try to email (commented for local XAMPP)
        // @mail($adminEmail, "[Shine Contact] $subject", $message . \"\\n\\nFrom: $name <$email>\");
    }
}
?>

<!-- Hero Section -->
<section class="py-5 bg-secondry text-dark">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-5 fw-bold mb-3">Contact Us</h1>
                <p class="lead mb-4">Have a question about a product, order, or promotion? We're here to help!</p>
            </div>
            <div class="col-lg-4 text-center">
                <i class="fa fa-envelope fa-6x text-dark opacity-75"></i>
            </div>
        </div>
    </div>
</section>

<!-- Contact Form Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg border-0">
                    <div class="card-header bg-dark text-light py-4">
                        <h3 class="card-title mb-0 text-center">
                            <i class="fa fa-paper-plane me-2"></i>Send us a Message
                        </h3>
                    </div>
                    <div class="card-body p-5">
                        <?php if ($sent): ?>
                            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="fa fa-check-circle fa-2x me-3 text-success"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Message Sent Successfully!</h5>
                                    <p class="mb-0">Thanks! Your message has been received. We'll get back to you soon.</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php elseif ($err): ?>
                            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                                <i class="fa fa-exclamation-triangle fa-2x me-3 text-danger"></i>
                                <div>
                                    <h5 class="alert-heading mb-1">Oops! Something went wrong</h5>
                                    <p class="mb-0"><?php echo $err; ?></p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form method="post" class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Your Name <span class="text-danger">*</span></label>
                                <input name="name" class="form-control form-control-lg" required 
                                       value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['user_name']) : '';?>"
                                       placeholder="Enter your full name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control form-control-lg" required 
                                       value="<?php echo isset($_SESSION['user']) ? htmlspecialchars($_SESSION['user']['email']) : '';?>"
                                       placeholder="your.email@example.com">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Subject <span class="text-danger">*</span></label>
                                <input name="subject" class="form-control form-control-lg" required 
                                       placeholder="What is this regarding?">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">Message <span class="text-danger">*</span></label>
                                <textarea name="message" rows="6" class="form-control form-control-lg" required 
                                          placeholder="Tell us how we can help you..."></textarea>
                            </div>
                            <div class="col-12 text-center">
                                <button class="btn btn-dark btn-lg px-5 py-3 fw-bold">
                                    <i class="fa fa-paper-plane me-2"></i>Send Message
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact Information -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="icon-wrapper bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                    <i class="fa fa-envelope fa-2x"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Email Us</h5>
                                <p class="text-muted mb-3">Send us an email anytime</p>
                                <a href="mailto:<?php echo htmlspecialchars($adminEmail); ?>" class="btn btn-outline-dark">
                                    <?php echo htmlspecialchars($adminEmail); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body text-center p-4">
                                <div class="icon-wrapper bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                                    <i class="fa fa-store fa-2x"></i>
                                </div>
                                <h5 class="fw-bold text-dark mb-2">Store Information</h5>
                                <div class="text-muted">
                                    <p class="mb-1"><i class="fa fa-map-marker-alt me-2 text-primary"></i>Shine Musical Instrument</p>
                                    <p class="mb-1"><i class="fa fa-location-arrow me-2 text-primary"></i>Mandalay, Myanmar</p>
                                    <p class="mb-0"><i class="fa fa-phone me-2 text-primary"></i>09789654892</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center mb-5">
                    <h2 class="display-6 fw-bold text-dark mb-3">Quick Help</h2>
                    <p class="lead text-muted">Common questions and answers</p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="fa fa-shipping-fast text-primary fa-2x me-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Shipping Questions?</h5>
                                <p class="text-muted mb-0">We offer fast shipping across Myanmar with tracking for all orders.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="fa fa-undo text-primary fa-2x me-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Return Policy</h5>
                                <p class="text-muted mb-0">30-day return policy for unused items in original packaging.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="fa fa-tools text-primary fa-2x me-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Instrument Repair</h5>
                                <p class="text-muted mb-0">Professional repair services available for all instruments.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <div class="flex-shrink-0">
                                <i class="fa fa-graduation-cap text-primary fa-2x me-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Music Lessons</h5>
                                <p class="text-muted mb-0">Learn from experienced teachers - guitar, piano, drums & more.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.card {
    border-radius: 15px;
}

.card-header {
    border-radius: 15px 15px 0 0 !important;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%) !important;
}

.form-control {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn {
    border-radius: 10px;
    transition: all 0.3s ease;
}

.icon-wrapper {
    transition: transform 0.3s ease;
}

.card:hover .icon-wrapper {
    transform: scale(1.1);
}

.alert {
    border-radius: 10px;
    border: none;
}

@media (max-width: 768px) {
    .display-5 {
        font-size: 2.5rem;
    }
    
    .display-6 {
        font-size: 2rem;
    }
}
</style>

<?php include("footer.php"); ?>
</body></html>