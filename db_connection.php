<?php
/**
 * NHIF DATABASE CONNECTION CONFIGURATION
 * Security Level: High (OWASP Compliant)
 */

// 1. Database Credentials (Configure based on your local/live server)
$host     = "127.0.0.1";
$username = "root";       // On a live server, use a user with limited privileges
$password = "";           // Enter your database password here
$dbname   = "bms";        // Ensure this matches your database name

// 2. Initialize Connection
$conn = new mysqli($host, $username, $password, $dbname);

// 3. Security Check: Connection Validation
if ($conn->connect_error) {
    /**
     * SECURITY: Avoid showing specific "connect_error" in the browser. 
     * Log the error internally and show a generic message to prevent data leakage.
     */
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Our systems are currently experiencing technical difficulties. Please try again later.");
}

// 4. Set Character Set to UTF-8
// Essential for preventing encoding issues and enhancing security against special character injections.
$conn->set_charset("utf8mb4");

/**
 * SECURITY: Prevent Direct Access
 * Ensures this file cannot be executed directly by unauthorized users.
 */
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    die("Direct access not allowed.");
}

// Connection is now ready for use in other files via include 'db_connection.php';
?>