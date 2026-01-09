<?php
include 'db_connect.php';

if (!isset($_GET['id'])) {
    die("Invalid Request");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
$sql = "SELECT * FROM petitions WHERE id = '$id'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Petition ID not found.");
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Acknowledgement #<?php echo $id; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        body { background-color: #525659; font-family: 'Times New Roman', serif; }
        .page-container {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 30px auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            position: relative;
        }
        .header { text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .gov-title { font-size: 24px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; }
        .sub-title { font-size: 16px; margin-top: 5px; }
        .receipt-box { border: 1px solid #333; padding: 15px; margin: 20px 0; background-color: #f8f9fa; }
        .footer { position: absolute; bottom: 20mm; left: 20mm; right: 20mm; text-align: center; font-size: 12px; border-top: 1px solid #ccc; padding-top: 10px; }
        .watermark { position: absolute; top: 40%; left: 50%; transform: translate(-50%, -50%) rotate(-45deg); font-size: 80px; color: rgba(0,0,0,0.05); z-index: 0; pointer-events: none; font-weight: bold; border: 5px solid rgba(0,0,0,0.05); padding: 20px; }
    </style>
</head>
<body>

<div class="text-center mt-3 mb-3">
    <button onclick="downloadPDF()" class="btn btn-warning fw-bold px-4 shadow">
        <i class="bi bi-file-earmark-pdf-fill"></i> Download PDF
    </button>
    <a href="index.php" class="btn btn-light ms-2">Back to Home</a>
</div>

<div class="page-container" id="receiptContent">
    
    <div class="watermark">OFFICIAL COPY</div>

    <div class="header">
        <div class="gov-title">Department of Public Governance</div>
        <div class="sub-title">Citizen Grievance Redressal Portal</div>
        <div class="small mt-2">Government of Tamil Nadu</div>
    </div>

    <h4 class="text-center mb-4"><u>ACKNOWLEDGEMENT RECEIPT</u></h4>

    <p><strong>Date:</strong> <?php echo date("d-m-Y H:i A", strtotime($row['created_at'])); ?></p>
    <p>To,</p>
    <p><strong><?php echo htmlspecialchars($row['petitioner_name']); ?></strong><br>
    Contact: <?php echo htmlspecialchars($row['contact_info']); ?></p>

    <p class="mt-4">Dear Citizen,</p>

    <p>This is to acknowledge the receipt of your grievance petition submitted through our automated AI Governance Portal. Your petition has been successfully registered in our system.</p>

    <div class="receipt-box">
        <table class="table table-borderless mb-0">
            <tr>
                <td width="30%"><strong>Petition ID:</strong></td>
                <td class="fs-5">#<?php echo $row['id']; ?></td>
            </tr>
            <tr>
                <td><strong>Category:</strong></td>
                <td><?php echo $row['category']; ?></td>
            </tr>
            <tr>
                <td><strong>Priority Assigned:</strong></td>
                <td><?php echo $row['priority']; ?></td>
            </tr>
            <tr>
                <td><strong>Current Status:</strong></td>
                <td><?php echo $row['status']; ?></td>
            </tr>
        </table>
    </div>

    <p><strong>Grievance Summary:</strong><br>
    <i>"<?php echo nl2br(htmlspecialchars(substr($row['description'], 0, 300))); ?>..."</i></p>

    <p class="mt-4">You can track the status of your petition using the Petition ID mentioned above on the official portal.</p>

    <br><br><br>
    <div class="row">
        <div class="col-6">
            <p><strong>Authorized Signatory</strong><br>
            <small>Automated System Generated</small></p>
        </div>
        <div class="col-6 text-end">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=PetitionID-<?php echo $row['id']; ?>" alt="QR" style="width: 80px;">
        </div>
    </div>

    <div class="footer">
        This is a computer-generated document and does not require a physical signature.<br>
        Governance AI Initiative © 2026
    </div>
</div>

<script>
    function downloadPDF() {
        // Select the element to capture
        const element = document.getElementById('receiptContent');
        
        // Options for the PDF generation
        const opt = {
            margin:       0,
            filename:     'Petition_Acknowledgement_<?php echo $id; ?>.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
        };

        // Generate and Save
        html2pdf().set(opt).from(element).save();
    }
</script>

</body>
</html>