<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen Grievance Portal | Smart Governance</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; }
        .hero-section { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: white; padding: 60px 0 100px; margin-bottom: -60px; }
        .form-card { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden; }
        .form-header { background: #fff; padding: 30px 30px 10px; border-bottom: 1px solid #eee; }
        .btn-primary-custom { background-color: #1e3a8a; border-color: #1e3a8a; padding: 12px; font-weight: 600; }
        .btn-primary-custom:hover { background-color: #1e40af; }
        .feature-icon { background: #e0e7ff; color: #1e3a8a; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin-bottom: 15px; font-weight: bold; }
    </style>
</head>
<body>

<div class="hero-section text-center">
    <div class="container">
        <h1 class="fw-bold display-5">Public Grievance Redressal System</h1>
        <p class="lead opacity-75">Submit your petitions easily. Our AI analyzes and routes them to the right department instantly.</p>
        <div class="mt-4">
            <a href="track_status.php" class="btn btn-outline-light px-4 py-2 rounded-pill">Track Existing Petition</a>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-10">
            <div class="card form-card bg-white">
                <div class="form-header text-center">
                    <h3 class="fw-bold text-dark">Lodge a New Petition</h3>
                    <p class="text-muted small">Please fill in the details below.</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <?php
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $new_id = $_GET['id']; // Get the ID from URL
        
        echo '<div class="alert alert-success shadow-sm border-0 rounded-3 mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <strong><i class="bi bi-check-circle-fill"></i> Success!</strong><br> 
                        Your petition has been submitted. Tracking ID: <strong>#' . $new_id . '</strong>
                        ' . (isset($_GET['msg']) && $_GET['msg'] == 'duplicate' ? '<br><small>Note: Linked to an existing issue.</small>' : '') . '
                    </div>
                    
                    <a href="acknowledgement.php?id=' . $new_id . '" target="_blank" class="btn btn-success btn-sm fw-bold">
                        <i class="bi bi-download"></i> Download Receipt
                    </a>
                </div>
              </div>';
    } else {
        echo '<div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">Error submitting petition. Please try again.</div>';
    }
}
?>

                    <form action="submit_handler.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Full Name</label>
                                <input type="text" name="petitioner_name" class="form-control form-control-lg bg-light border-0" placeholder="e.g. John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase text-muted">Contact (Phone/Email)</label>
                                <input type="text" name="contact_info" class="form-control form-control-lg bg-light border-0" placeholder="e.g. 9876543210" required>
                            </div>
                            <div class="col-12 mt-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">Grievance Description</label>
                                <textarea name="description" class="form-control bg-light border-0" rows="6" placeholder="Describe the issue in detail (Location, nature of problem, etc.)..." required></textarea>
                                <div class="form-text mt-2"><i class="bi bi-robot"></i> AI will automatically categorize this for you.</div>
                            </div>
                            <div class="col-12 mt-4">
                                <button type="submit" name="submit" class="btn btn-primary-custom w-100 rounded-3 shadow-sm">Submit Petition</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 d-none d-lg-block mt-5 pt-4">
            <div class="ps-4">
                <h5 class="fw-bold mb-4">How it works</h5>
                <div class="d-flex mb-4">
                    <div class="feature-icon flex-shrink-0">1</div>
                    <div class="ms-3">
                        <h6 class="fw-bold mb-1">Fill the Form</h6>
                        <p class="text-muted small">Provide details about the issue. No need to select categories manually.</p>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="feature-icon flex-shrink-0">2</div>
                    <div class="ms-3">
                        <h6 class="fw-bold mb-1">AI Processing</h6>
                        <p class="text-muted small">Our AI analyzes the text, assigns urgency, and routes it to the correct department.</p>
                    </div>
                </div>
                <div class="d-flex mb-4">
                    <div class="feature-icon flex-shrink-0">3</div>
                    <div class="ms-3">
                        <h6 class="fw-bold mb-1">Quick Resolution</h6>
                        <p class="text-muted small">Officials track and resolve issues based on AI priority tagging.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="text-center py-4 text-muted small mt-auto">
    &copy; 2026 Governance AI Initiative. All rights reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>