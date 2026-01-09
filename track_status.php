<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Track Petition Status | Governance Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f4f6f9; min-height: 100vh; display: flex; flex-direction: column; }
        .navbar { background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); padding: 15px 0; }
        .navbar-brand { font-weight: 700; color: white !important; font-size: 1.25rem; }
        .search-container { max-width: 600px; margin: 40px auto; text-align: center; }
        .search-input { border-radius: 50px 0 0 50px; border: 1px solid #e5e7eb; padding-left: 25px; }
        .search-btn { border-radius: 0 50px 50px 0; background-color: #1e3a8a; border-color: #1e3a8a; padding: 0 30px; font-weight: 600; }
        .search-btn:hover { background-color: #1e40af; }
        .status-card { border: none; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); overflow: hidden; background: white; margin-bottom: 40px; }
        .card-header-custom { background: #f8fafc; padding: 20px 30px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center; }
        .steps { display: flex; justify-content: space-between; position: relative; margin: 40px 0 20px; }
        .steps::before { content: ""; position: absolute; top: 15px; left: 0; right: 0; height: 4px; background: #e5e7eb; z-index: 1; margin: 0 40px; }
        .step { position: relative; z-index: 2; text-align: center; width: 33.33%; }
        .step-icon { width: 35px; height: 35px; background: #e5e7eb; color: #6b7280; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-weight: bold; border: 4px solid #fff; }
        .step-text { font-size: 0.85rem; color: #6b7280; font-weight: 600; text-transform: uppercase; }
        .step.active .step-icon { background: #1e3a8a; color: white; box-shadow: 0 0 0 4px #bfdbfe; }
        .step.active .step-text { color: #1e3a8a; }
        .step.completed .step-icon { background: #10b981; color: white; }
        .step.completed .step-text { color: #10b981; }
        .info-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; font-weight: 700; margin-bottom: 5px; }
        .info-value { font-size: 1rem; color: #111827; font-weight: 500; }
    </style>
</head>
<body>

<?php
// --- FIXED: TRANSLATOR FUNCTION ---
// This converts numbers (1, 2, 3) back to Text (Water, Electricity, etc.)
function getCategoryName($input) {
    $categories = [
        1 => "Water & Sewage",
        2 => "Electricity",
        3 => "Roads & Infrastructure",
        4 => "Public Health",
        5 => "Sanitation",
        6 => "Public Safety",
        7 => "Revenue & Land",
        8 => "Education",
        9 => "Social Welfare",
        10 => "General"
    ];
    // If input is a number, return the text name. If it's already text, return it as is.
    return (is_numeric($input) && isset($categories[$input])) ? $categories[$input] : $input;
}
?>

<nav class="navbar navbar-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-bank2 me-2"></i> Citizen Grievance Portal</a>
        <a href="index.php" class="btn btn-sm btn-outline-light rounded-pill px-3">Back to Home</a>
    </div>
</nav>

<div class="container flex-grow-1">
    
    <div class="search-container">
        <h3 class="fw-bold text-dark mb-3">Track Your Petition</h3>
        <p class="text-muted mb-4">Enter your Petition ID below to check the current status.</p>
        
        <form method="GET" action="track_status.php">
            <div class="input-group input-group-lg shadow-sm rounded-pill">
                <input type="number" name="petition_id" class="form-control search-input" placeholder="e.g. 1" required value="<?php echo isset($_GET['petition_id']) ? $_GET['petition_id'] : ''; ?>">
                <button class="btn btn-primary search-btn" type="submit">Track Status</button>
            </div>
        </form>
    </div>

    <?php
    if (isset($_GET['petition_id']) && $_GET['petition_id'] != '') {
        $id = mysqli_real_escape_string($conn, $_GET['petition_id']);
        $sql = "SELECT * FROM petitions WHERE id = '$id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $status = $row['status'];
            
            // Progress Bar Logic
            $s1 = $s2 = $s3 = ""; 
            if ($status == 'Pending') { $s1 = "active"; }
            elseif ($status == 'In-Progress') { $s1 = "completed"; $s2 = "active"; }
            elseif ($status == 'Resolved') { $s1 = "completed"; $s2 = "completed"; $s3 = "completed"; }
            
            // Get Readable Category Name
            $display_category = getCategoryName($row['category']);
            ?>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="status-card">
                        
                        <div class="card-header-custom">
                            <div>
                                <div class="text-uppercase small text-muted fw-bold">Petition ID</div>
                                <div class="h4 mb-0 fw-bold text-dark">#<?php echo $row['id']; ?></div>
                            </div>
                            <a href="acknowledgement.php?id=<?php echo $row['id']; ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                                <i class="bi bi-download me-1"></i> Receipt
                            </a>
                        </div>

                        <div class="card-body p-4">
                            
                            <div class="steps">
                                <div class="step <?php echo $s1; ?>">
                                    <div class="step-icon"><i class="bi bi-file-earmark-text"></i></div>
                                    <div class="step-text">Received</div>
                                </div>
                                <div class="step <?php echo $s2; ?>">
                                    <div class="step-icon"><i class="bi bi-gear-fill"></i></div>
                                    <div class="step-text">Processing</div>
                                </div>
                                <div class="step <?php echo $s3; ?>">
                                    <div class="step-icon"><i class="bi bi-check-lg"></i></div>
                                    <div class="step-text">Resolved</div>
                                </div>
                            </div>

                            <hr class="my-4 opacity-25">

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="info-label">Category</div>
                                    <div class="info-value"><span class="badge bg-light text-dark border"><?php echo $display_category; ?></span></div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-label">Submission Date</div>
                                    <div class="info-value"><?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></div>
                                </div>
                                <div class="col-12">
                                    <div class="info-label">Original Grievance</div>
                                    <div class="p-3 bg-light rounded text-dark border-start border-4 border-primary">
                                        <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                                    </div>
                                </div>
                                
                                <?php if(!empty($row['official_remarks'])): ?>
                                <div class="col-12">
                                    <div class="info-label text-success">Official Response / Action Taken</div>
                                    <div class="p-3 bg-success bg-opacity-10 rounded text-success border border-success border-opacity-25">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <?php echo nl2br(htmlspecialchars($row['official_remarks'])); ?>
                                    </div>
                                </div>
                                <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-warning border-0 small">
                                        <i class="bi bi-clock-history me-2"></i> No official remarks yet. Your petition is under review.
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

        <?php 
        } else {
            echo '<div class="alert alert-danger shadow-sm border-0 rounded-3 text-center p-4 mt-3" style="max-width: 600px; margin: 0 auto;">
                    <i class="bi bi-x-circle-fill fs-1 mb-2 d-block"></i>
                    <h5 class="fw-bold">Petition Not Found</h5>
                    <p class="mb-0">We could not find any record with ID <strong>#' . htmlspecialchars($_GET['petition_id']) . '</strong>.</p>
                  </div>';
        }
    }
    ?>
</div>

<footer class="text-center py-4 text-muted small mt-auto">
    &copy; 2026 Governance AI Initiative.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>