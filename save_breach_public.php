<?php
/**
 * NHIF PUBLIC INCIDENT REPORTING SYSTEM
 * Purpose: Receives security incident reports from students and staff.
 * Security: Uses Prepared Statements to prevent SQL Injection.
 */

require_once 'db_connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. DATA SANITIZATION: Clean inputs to prevent Cross-Site Scripting (XSS)
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    
    /** * DEFAULT VALUES FOR PUBLIC REPORTS:
     * - Severity is set to 'Low' by default until verified by Admin.
     * - Status is 'Open' for all new entries.
     */
    $severity = "Low"; 
    $status = "Open";

// 2. DATABASE PERSISTENCE: Using Prepared Statements for Security
    /** * We have added the 'source' column to differentiate between 
     * reports coming from the public portal and internal logs.
     */
    $source = "public"; 
    
    // Prepare the SQL statement with 5 placeholders (?)
    $stmt = $conn->prepare("INSERT INTO breaches (title, severity, description, status, source, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    
    /**
     * BIND PARAMETERS:
     * "sssss" indicates that all 5 parameters are strings.
     * Order: title, severity, description, status, source
     */
    $stmt->bind_param("sssss", $title, $severity, $description, $status, $source);
    if ($stmt->execute()) {
        /** * SUCCESS: Show a professional success message and redirect to homepage.
         * This ensures the user knows their report has been securely sent.
         */
        echo "<script>
                alert('Success! Your security report has been submitted to NHIF IT Department.');
                window.location.href='index.php';
              </script>";
    } else {
        /**
         * ERROR HANDLING: Log the error and show a generic message to the user.
         * We don't show the full database error to public users for security reasons.
         */
        error_log("Database Error: " . $stmt->error);
        echo "<script>
                alert('Error: Unable to submit report. Please contact system administrator.');
                window.location.href='index.php';
              </script>";
    }

    $stmt->close();
} else {
    // Redirect if the file is accessed directly without POST data
    header("Location: index.php");
    exit();
}

$conn->close();
?>