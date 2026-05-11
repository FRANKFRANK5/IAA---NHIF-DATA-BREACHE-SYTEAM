<?php
/**
 * NHIF INCIDENT DELETION MODULE
 * Purpose: Securely removes a security incident from the database.
 * Security: Validates session and uses Prepared Statements.
 */

session_start();
require_once 'db_connection.php';

// 1. ACCESS CONTROL: Only authenticated administrators can delete records
if (!isset($_SESSION['admin_id'])) {
    // Log unauthorized attempt for security monitoring
    error_log("Unauthorized deletion attempt blocked.");
    die("Security Error: Unauthorized access denied.");
}

// 2. INPUT VALIDATION: Ensure the ID is present and is a valid integer
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 3. DATABASE OPERATION: Using Prepared Statements to prevent SQL Injection
    $stmt = $conn->prepare("DELETE FROM breaches WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // SUCCESS: Redirect back to the dashboard with a success message
        header("Location: view_breaches.php?msg=deleted");
        exit();
    } else {
        // ERROR HANDLING: Log the specific database error for debugging
        error_log("Database Error during deletion: " . $conn->error);
        echo "An internal error occurred. Please contact the system administrator.";
    }
    $stmt->close();
} else {
    // Redirect if no valid ID was provided in the request
    header("Location: view_breaches.php");
    exit();
}

$conn->close();
?>