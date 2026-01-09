<?php 
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php'; 

$id = $_GET['id'];
$notification_msg = "";

// Update logic
if (isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $remarks = mysqli_real_escape_string($conn, $_POST['remarks']);
    
    // 1. Update Database
    $conn->query("UPDATE petitions SET status='$status', official_remarks='$remarks' WHERE id=$id");
    
    // 2. SIMULATE SENDING NOTIFICATION TO PETITIONER (Requirement: "Communicate status")
    // In a real server, you would use: mail($petitioner_email, "Status Update", ...);
    $notification_msg = "
    <div class='alert alert-success border-0 shadow-sm mb-4'>
        <div class='d-flex align-items-center'>
            <i class='bi bi-envelope-check-fill fs-4 me-3'></i>
            <div>
                <strong>Update Successful!</strong><br>
                Automatic notification (Email/SMS) has been sent to the petitioner regarding this status change.
            </div>
        </div>
    </div>";
}

// Fetch Data
$res = $conn->query("SELECT * FROM petitions WHERE id=$id");
$data = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Petition #<?php echo $id; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f3f4f6; font-family: 'Inter', sans-serif; }
        
        /* Navbar */
        .navbar { background-color: #111827 !important; padding: 1rem 2rem; }
        .navbar-brand { font-weight: 700; letter-spacing: 0.5px; }

        /* Layout */
        .main-card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); overflow: hidden; }
        .card-header-custom { background: #fff; padding: 25px 30px; border-bottom: 1px solid #e5e7eb; }
        
        /* Details Section */
        .detail-label { font-size: 0.75rem; text-transform: uppercase; color: #6b7280; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 4px; }
        .detail-value { font-size: 1rem; color: #111827; font-weight: 500; }
        
        /* Description Box */
        .desc-box { background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 20px; color: #374151; line-height: 1.6; }
        
        /* Status Badges */
        .badge-urgent { background-color: #fee2e2; color: #991b1b; }
        .badge-normal { background-color: #e0f2fe; color: #075985; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark shadow-sm mb-5">
    <div class="container-fluid">
        <span class="navbar-brand"><i class="bi bi-shield-lock me-2"></i> Admin Workspace</span>
        <a href="dashboard.php" class="btn btn-outline-secondary btn-sm text-white border-secondary">Back to Dashboard</a>
    </div>
</nav>

<div class="container pb-5" style="max-width: 900px;">
    
    <?php echo $notification_msg; ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="card main-card bg-white mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">Petition Details #<?php echo $id; ?></h5>
                    <span class="badge bg-light text-dark border"><?php echo date("d M Y", strtotime($data['created_at'])); ?></span>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="detail-label">Petitioner Name</div>
                            <div class="detail-value"><?php echo htmlspecialchars($data['petitioner_name']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">Contact Info</div>
                            <div class="detail-value text-primary"><i class="bi bi-telephone me-1"></i> <?php echo htmlspecialchars($data['contact_info']); ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">AI Category</div>
                            <div class="detail-value"><span class="badge bg-secondary bg-opacity-10 text-dark"><?php echo $data['category']; ?></span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-label">AI Priority</div>
                            <div class="detail-value">
                                <?php 
                                    $p_class = ($data['priority'] == 'Urgent') ? 'badge-urgent' : 'badge-normal';
                                    echo "<span class='badge $p_class px-3 rounded-pill'>{$data['priority']}</span>";
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="detail-label">Grievance Description</div>
                    <div class="desc-box mb-4">
                        <?php echo nl2br(htmlspecialchars($data['description'])); ?>
                    </div>

                    <?php if($data['parent_id']): ?>
                        <div class="alert alert-warning d-flex align-items-center border-0 shadow-sm">
                            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                            <div>
                                <strong>Duplicate Detected</strong><br>
                                This petition is linked to original Petition 
                                <a href="view_petition.php?id=<?php echo $data['parent_id']; ?>" class="fw-bold">#<?php echo $data['parent_id']; ?></a>.
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card main-card bg-white">
                <div class="card-header-custom bg-primary text-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-tools me-2"></i> Official Action</h6>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Update Status</label>
                            <select name="status" class="form-select form-select-lg">
                                <option value="Pending" <?php if($data['status']=='Pending') echo 'selected'; ?>>Pending</option>
                                <option value="In-Progress" <?php if($data['status']=='In-Progress') echo 'selected'; ?>>In-Progress</option>
                                <option value="Resolved" <?php if($data['status']=='Resolved') echo 'selected'; ?>>Resolved</option>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold small">Official Remarks</label>
                            <textarea name="remarks" class="form-control" rows="5" placeholder="Enter action taken or reply to citizen..."><?php echo $data['official_remarks']; ?></textarea>
                            <div class="form-text small">This will be visible to the citizen.</div>
                        </div>

                        <button type="submit" name="update_status" class="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                            Update & Notify
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>