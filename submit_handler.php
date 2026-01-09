<?php
include 'db_connect.php';
include 'ai_processor.php';
include 'duplicate_check.php'; // <--- LOAD THE MISSING FILE

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['petitioner_name']);
    $contact = mysqli_real_escape_string($conn, $_POST['contact_info']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // 1. AI CATEGORIZATION (Using ai_processor.php)
    $ai_results = analyzeWithAI($description);
    $category = mysqli_real_escape_string($conn, $ai_results['category']);
    $priority = mysqli_real_escape_string($conn, $ai_results['priority']);

    // 2. AI DUPLICATE CHECK (Using duplicate_check.php)
    // This calls the function you wrote to check for similar issues
    $parent_id = findDuplicate($conn, $description); 
    
    // If we found a parent_id, it IS a duplicate.
    $is_duplicate = ($parent_id != null) ? 1 : 0;
    
    // Prepare SQL value for parent_id (NULL or Number)
    $sql_parent_id = ($parent_id) ? $parent_id : "NULL";

    // 3. SAVE TO DATABASE
    $sql = "INSERT INTO petitions (petitioner_name, contact_info, description, category, priority, parent_id) 
            VALUES ('$name', '$contact', '$description', '$category', '$priority', $sql_parent_id)";

    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        // Redirect with success message
        $msg_type = $is_duplicate ? "&msg=duplicate" : "";
        header("Location: index.php?status=success&id=" . $last_id . $msg_type);
        exit();
    } else {
        header("Location: index.php?status=error");
        exit();
    }
}
$conn->close();
?>