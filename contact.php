<?php
$page_title = "Contact Us";
include 'includes/header.php';

$success = '';
$error = '';

if ($_POST) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    if ($name && $email && $subject && $message) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$name, $email, $subject, $message])) {
                $success = "Thank you for your message! We'll get back to you soon.";
                // Clear form data
                $_POST = [];
            } else {
                $error = "Failed to send message. Please try again.";
            }
        } catch (Exception $e) {
            $error = "Error sending message. Please try again.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<div class="container my-5">
    <div class="row text-center mb-5">
        <div class="col-12">
            <h1 class="display-4 fw-bold mb-4">Contact Us</h1>
            <p class="lead text-muted">Have questions about our products or need technical support? We're here to help!</p>
        </div>
    </div>

    <div class="row">
        <!-- Contact Information -->
        <div class="col-lg-6 mb-5">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon me-4" style="width: 60px; height: 60px;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Store Location</h5>
                                <p class="text-muted mb-0">
                                    123 Tech Plaza<br>
                                    Mumbai, Maharashtra 400001<br>
                                    India
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon me-4" style="width: 60px; height: 60px;">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Call Us</h5>
                                <p class="text-muted mb-0">
                                    Sales: +91 22 1234 5678<br>
                                    Support: +91 98765 43210
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon me-4" style="width: 60px; height: 60px;">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Email</h5>
                                <p class="text-muted mb-0">
                                    General Inquiries: info@techhub.com<br>
                                    Technical Support: support@techhub.com
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card h-100 p-4">
                        <div class="d-flex align-items-start">
                            <div class="feature-icon me-4" style="width: 60px; height: 60px;">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-2">Operating Hours</h5>
                                <p class="text-muted mb-0">
                                    Monday - Saturday: 10:00 AM - 8:00 PM<br>
                                    Sunday: 11:00 AM - 6:00 PM<br>
                                    Technical Support: 24/7
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Form -->
        <div class="col-lg-6">
            <div class="contact-form">
                <h3 class="fw-bold mb-4">Send us a Message</h3>

                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" id="contactForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="name" name="name" required 
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required 
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label for="subject" class="form-label fw-semibold">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required 
                                   value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">
                        </div>
                        <div class="col-12">
                            <label for="message" class="form-label fw-semibold">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
