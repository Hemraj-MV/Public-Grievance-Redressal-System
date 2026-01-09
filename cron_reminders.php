<?php
include 'db_connect.php';

// Logic: Find petitions that are 'Pending', marked 'Urgent', and older than 24 hours
// INTERVAL 1 DAY means 24 hours. Change to HOUR if you want to test faster.
$sql = "SELECT * FROM petitions 
        WHERE status = 'Pending' 
        AND priority = 'Urgent' 
        AND created_at < NOW() - INTERVAL 1 DAY";

$result = $conn->query($sql);

echo "<h2>Running Automated Reminder Check...</h2>";

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $id = $row['id'];
        $officials_email = "admin@government.local"; // In a real app, fetch specific department email
        
        $subject = "URGENT REMINDER: Petition #$id is Overdue";
        $message = "Petition #$id regarding '{$row['category']}' is marked URGENT and has been pending for over 24 hours.\n\nDescription: {$row['description']}";
        
        // Simulating Email Sending (Since local XAMPP doesn't have a mail server configured)
        // In production, you would uncomment: mail($officials_email, $subject, $message);
        
        echo "<div style='border:1px solid red; padding:10px; margin:10px; color:red;'>";
        echo "<strong>[ALERT SENT]</strong> To: $officials_email <br>";
        echo "Subject: $subject <br>";
        echo "Body: " . substr($message, 0, 50) . "...";
        echo "</div>";

        // Update the 'last_reminder_sent' column so we don't spam them every second
        $conn->query("UPDATE petitions SET last_reminder_sent = NOW() WHERE id = $id");
    }
} else {
    echo "<div style='color:green;'>No overdue urgent petitions found. Good job!</div>";
}
?>