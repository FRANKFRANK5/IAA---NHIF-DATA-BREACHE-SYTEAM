<?php
/**
 * NHIF STATUS MANAGEMENT SYSTEM
 * Backend: Secure Status Update (OWASP A01:2021 & A03:2021)
 */

// 1. ANZA NA SESSION CHECK (Lazima iwe Line 1)
session_start();

// Tunakagua kama 'admin_id' ipo. Kama haipo, tunamfukuza mtu arudi login
if (!isset($_SESSION['admin_id'])) {
    header("Location: auth_login.php");
    exit();
}

// 2. UNGANISHA NA DATABASE
include 'db_connection.php';

// 3. SHUGHULIKIA DATA ILIYOTUMWA (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $report_id  = $_POST['id'] ?? '';
    $new_status = $_POST['status'] ?? '';

    if (!empty($report_id) && !empty($new_status)) {
        
        /**
         * SECURE SQL: Prepared Statements kuzuia SQL Injection
         */
        $stmt = $conn->prepare("UPDATE breaches SET status = ? WHERE id = ?");
        
        // "si" -> s (string kwa status), i (integer kwa id)
        $stmt->bind_param("si", $new_status, $report_id);

        if ($stmt->execute()) {
            // Success: Mrudishe Admin kwenye dashboard
            header("Location: view_breaches.php?status=success");
            exit();
        } else {
            // Error Logging
            error_log("Update failed: " . $stmt->error);
            echo "Security Error: Incident update failed.";
        }
        $stmt->close();
    }
}

// 4. FUNGA CONNECTION
$conn->close();
?>
