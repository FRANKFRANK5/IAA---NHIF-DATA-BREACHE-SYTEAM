<?php
/**
 * NHIF SECURITY PORTAL - SECURED DATA PERSISTENCE
 * Fixed: Replaced insecure queries with Prepared Statements (OWASP A03:2021)
 */

session_start();
require_once 'db_connection.php';

// 1. ACCESS CONTROL: Ensure only logged-in admins can save data
if (!isset($_SESSION['admin_id'])) {
    // Badala ya 'die', tunamrudisha kwenye login kwa usalama
    header("Location: auth_login.php?error=unauthorized");
    exit();
}

// 2. DATA VALIDATION
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Trim and Sanitize for XSS prevention
    $title = htmlspecialchars(trim($_POST['title']));
    $severity = htmlspecialchars(trim($_POST['severity']));
    $description = htmlspecialchars(trim($_POST['description']));
    $status = "Open"; 

    // 3. SECURE DATABASE INSERTION: Using Prepared Statements
    // Hapa ndipo tunapoziba tundu la SQL Injection
    $stmt = $conn->prepare("INSERT INTO breaches (title, severity, description, status, created_at) VALUES (?, ?, ?, ?, NOW())");
    
    // "ssss" inamaanisha data zote nne ni Strings
    $stmt->bind_param("ssss", $title, $severity, $description, $status);

    if ($stmt->execute()) {
        // Success: Redirect back with a clean success flag
        header("Location: view_breaches.php?status=success");
        exit();
    } else {
        // Error: Record error secretly, don't show internal database details to the user
        error_log("Database Error: " . $stmt->error);
        die("An internal error occurred. Please try again later.");
    }
    
    $stmt->close();
} else {
    header("Location: view_breaches.php");
    exit();
}

$conn->close();
?>