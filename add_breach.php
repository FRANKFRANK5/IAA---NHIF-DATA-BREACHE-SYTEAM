<?php
/**
 * NHIF DATA BREACH MANAGEMENT SYSTEM
 * Backend: Secure Data Processing (OWASP A03:2021 Compliant)
 * Technique: Using Prepared Statements to Prevent SQL Injection
 */

// Include the secure database connection
include 'db_connection.php';

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Data Collection with Null Coalescing (Basic Validation)
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $severity    = $_POST['severity'] ?? '';
    $status      = "open";

    /**
     * 2. Prepared Statements (Security Control)
     * Instead of placing variables directly in the SQL string, we use placeholders (?)
     * This ensures that the database treats the input strictly as data, not executable code.
     */
    $stmt = $conn->prepare("INSERT INTO breaches (title, description, severity, status) VALUES (?, ?, ?, ?)");
    
    /**
     * 3. Bind Parameters
     * "ssss" means we are binding 4 variables, and all of them are Strings.
     */
    $stmt->bind_param("ssss", $title, $description, $severity, $status);

    // 4. Execution of the Secure Statement
    if ($stmt->execute()) {
        /**
         * 5. Security Redirection
         * Redirecting back to index with a success flag. 
         * Minimizes information disclosure about the backend logic.
         */
        header("Location: index.html?status=success");
        exit();
    } else {
        /**
         * 6. A09:2021 - Secure Logging and Error Handling
         * We log the specific error internally for the admin, 
         * but show a generic message to the user to prevent information leakage.
         */
        error_log("NHIF System Error: " . $stmt->error); 
        echo "A technical error occurred. Please contact the system administrator.";
    }

    // 7. Resource Management: Close statement and connection
    $stmt->close();
    $conn->close();
}
?>