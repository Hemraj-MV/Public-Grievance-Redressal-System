<?php 
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
include 'db_connect.php'; 

// --- HELPER: Translator Function ---
function getCategoryName($input) {
    $categories = [
        1 => "Water & Sewage", 2 => "Electricity", 3 => "Roads & Infrastructure",
        4 => "Public Health", 5 => "Sanitation", 6 => "Public Safety",
        7 => "Revenue & Land", 8 => "Education", 9 => "Social Welfare", 10 => "General"
    ];
    return (is_numeric($input) && isset($categories[$input])) ? $categories[$input] : $input;
}

// --- LOGIC: Filter by Category ---
$category_filter = isset($_GET['category_filter']) ? $_GET['category_filter'] : 'All';

// Build Query
$sql = "SELECT * FROM petitions";
if ($category_filter != 'All') {
    $sql .= " WHERE category LIKE '%$category_filter%' OR category = '$category_filter'";
}
$sql .= " ORDER BY FIELD(priority, 'Urgent', 'High', 'Medium', 'Low'), created_at DESC";

// --- LOGIC: Fetch Stats ---
$total = $conn->query("SELECT COUNT(*) as c FROM petitions")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM petitions WHERE status='Pending'")->fetch_assoc()['c'];
$resolved = $conn->query("SELECT COUNT(*) as c FROM petitions WHERE status='Resolved'")->fetch_assoc()['c'];
$urgent = $conn->query("SELECT COUNT(*) as c FROM petitions WHERE priority='Urgent' AND status!='Resolved'")->fetch_assoc()['c'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Smart Governance</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        body { background-color: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* Navbar Styling */
        .navbar { background-color: #0f172a; padding: 1rem 0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .navbar-brand { font-weight: 700; font-size: 1.25rem; letter-spacing: 0.5px; }
        
        /* Card Styling */
        .card { border: none; border-radius: 12px; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); transition: all 0.2s; }
        .stat-card { padding: 20px; height: 100%; display: flex; flex-direction: column; justify-content: center; }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        
        .icon-box { width: 48px; height: 48px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin-bottom: 15px; }
        
        /* Table Styling */
        .table-card { overflow: hidden; }
        .table thead th { background-color: #f8fafc; font-size: 0.75rem; text-transform: uppercase; color: #64748b; font-weight: 700; padding: 16px; border-bottom: 1px solid #e2e8f0; }
        .table tbody td { padding: 16px; vertical-align: middle; color: #334155; font-size: 0.95rem; border-bottom: 1px solid #f1f5f9; }
        .table tbody tr:last-child td { border-bottom: none; }
        
        /* Badges */
        .badge-soft { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; letter-spacing: 0.3px; }
        .badge-urgent { background-color: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
        .badge-high { background-color: #fff7ed; color: #9a3412; border: 1px solid #fed7aa; }
        .badge-medium { background-color: #eff6ff; color: #1e40af; border: 1px solid #bfdbfe; }
        .badge-low { background-color: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
        
        .status-pill { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 9999px; font-size: 0.8rem; font-weight: 600; }
        .status-pending { background-color: #fffbeb; color: #b45309; }
        .status-progress { background-color: #eff6ff; color: #1d4ed8; }
        .status-resolved { background-color: #ecfdf5; color: #047857; }
        
        /* Button Styling */
        .btn-reminder { background: #f59e0b; color: #fff; font-weight: 600; border: none; padding: 8px 20px; border-radius: 50px; display: flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-reminder:hover { background: #d97706; color: white; transform: scale(1.02); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark mb-4">
    <div class="container-fluid px-4">
        <span class="navbar-brand d-flex align-items-center">
            <i class="bi bi-grid-fill me-2 text-primary"></i> 
            <span>Governance AI <span class="text-secondary fw-light opacity-75">| Admin Panel</span></span>
        </span>
        
        <div class="d-flex align-items-center gap-3">
            <a href="cron_reminders.php" target="_blank" class="btn-reminder shadow-sm text-decoration-none">
                <i class="bi bi-bell-fill"></i> Run Reminder Check
            </a>
            
            <div class="vr bg-secondary opacity-25 mx-2" style="height: 30px;"></div>
            
            <span class="text-white small opacity-75">Administrator</span>
            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 ms-2">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid px-4 pb-5">
    
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="row g-4 h-100">
                <div class="col-md-6">
                    <div class="card stat-card">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-files"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Total Petitions</p>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo $total; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card stat-card">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Pending Action</p>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo $pending; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card stat-card">
                        <div class="icon-box bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-lightning-fill"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Urgent & Critical</p>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo $urgent; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card stat-card">
                        <div class="icon-box bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <p class="text-muted small fw-bold text-uppercase mb-1">Resolved Cases</p>
                            <h2 class="mb-0 fw-bold text-dark"><?php echo $resolved; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h6 class="fw-bold mb-0 text-dark">AI Department Analysis</h6>
                    
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <div style="width: 100%; height: 250px;">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">Active Petitions Queue</h5>
            
            <form method="GET" class="d-flex align-items-center">
                <i class="bi bi-funnel-fill text-muted me-2"></i>
                <select name="category_filter" class="form-select form-select-sm shadow-none" style="width: 200px; border-color: #cbd5e1;" onchange="this.form.submit()">
                    <option value="All">All Departments</option>
                    <option value="Water" <?php if($category_filter == 'Water') echo 'selected'; ?>>Water & Sewage</option>
                    <option value="Electricity" <?php if($category_filter == 'Electricity') echo 'selected'; ?>>Electricity</option>
                    <option value="Roads" <?php if($category_filter == 'Roads') echo 'selected'; ?>>Roads & Infra</option>
                    <option value="Health" <?php if($category_filter == 'Health') echo 'selected'; ?>>Public Health</option>
                    <option value="Sanitation" <?php if($category_filter == 'Sanitation') echo 'selected'; ?>>Sanitation</option>
                    <option value="Safety" <?php if($category_filter == 'Safety') echo 'selected'; ?>>Public Safety</option>
                    <option value="Revenue" <?php if($category_filter == 'Revenue') echo 'selected'; ?>>Revenue & Land</option>
                    <option value="Education" <?php if($category_filter == 'Education') echo 'selected'; ?>>Education</option>
                </select>
            </form>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Petitioner</th>
                        <th>Category</th>
                        <th>AI Priority</th>
                        <th style="width: 35%;">Description</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            // Priority Badge Logic
                            $p_badge = 'badge-low';
                            if($row['priority'] == 'Urgent') $p_badge = 'badge-urgent';
                            elseif($row['priority'] == 'High') $p_badge = 'badge-high';
                            elseif($row['priority'] == 'Medium') $p_badge = 'badge-medium';

                            // Status Pill Logic
                            $s_pill = 'status-pending';
                            $s_icon = 'bi-hourglass-split';
                            if($row['status'] == 'Resolved') { $s_pill = 'status-resolved'; $s_icon = 'bi-check-circle-fill'; }
                            elseif($row['status'] == 'In-Progress') { $s_pill = 'status-progress'; $s_icon = 'bi-gear-wide-connected'; }

                            // Translate Category
                            $display_cat = getCategoryName($row['category']);

                            echo "<tr>";
                            echo "<td class='fw-bold text-secondary'>#{$row['id']}</td>";
                            echo "<td>
                                    <div class='fw-bold text-dark'>{$row['petitioner_name']}</div>
                                    <div class='small text-muted'>{$row['contact_info']}</div>
                                  </td>";
                            echo "<td><span class='badge bg-light text-dark border'>{$display_cat}</span></td>";
                            echo "<td><span class='badge-soft {$p_badge}'>{$row['priority']}</span></td>";
                            echo "<td><span class='d-inline-block text-truncate text-secondary' style='max-width: 350px;'>" . htmlspecialchars($row['description']) . "</span></td>";
                            echo "<td>
                                    <span class='status-pill {$s_pill}'>
                                        <i class='bi {$s_icon} me-1'></i> {$row['status']}
                                    </span>
                                  </td>";
                            echo "<td class='text-end'>
                                    <a href='view_petition.php?id={$row['id']}' class='btn btn-sm btn-dark px-3 rounded-pill fw-bold'>Manage</a>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-5 text-muted'>No petitions found for this filter.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    <?php
    $chartData = $conn->query("SELECT category, COUNT(*) as count FROM petitions GROUP BY category");
    $labels = []; $data = [];
    while($row = $chartData->fetch_assoc()) {
        $labels[] = getCategoryName($row['category']); 
        $data[] = $row['count'];
    }
    ?>

    const ctx = document.getElementById('categoryChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                data: <?php echo json_encode($data); ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6366f1'],
                borderWidth: 0,
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 11 }, padding: 15 } }
            },
            layout: { padding: 10 }
        }
    });
</script>
</body>
</html>