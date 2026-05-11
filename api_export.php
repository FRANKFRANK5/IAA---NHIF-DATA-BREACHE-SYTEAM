<?php
/**
 * NHIF DATA EXPORT API
 * Purpose: Securely exports breach data in JSON format for external reporting.
 * Security: Requires 'admin_id' session check to prevent unauthorized data leaks.
 */

// 1. ACCESS CONTROL (Lazima iwe Line 1)
session_start();
if (!isset($_SESSION['admin_id'])) {
    // If not logged in, return a 401 Unauthorized error in JSON format
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized access. Authentication required."]);
    exit();
}

// 2. DATABASE CONNECTION
include 'db_connection.php';

// 3. FETCH DATA FROM DATABASE
$sql = "SELECT id, title, description, severity, status, created_at FROM breaches";
$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Cleaning and adding data to the array
        $data[] = [
            "report_id"   => $row['id'],
            "title"       => htmlspecialchars($row['title']),
            "description" => htmlspecialchars($row['description']),
            "severity"    => strtoupper($row['severity']),
            "status"      => strtoupper($row['status']),
            "date_reported" => $row['created_at']
        ];
    }
}

// 4. OUTPUT AS JSON
header('Content-Type: application/json');
echo json_encode([
    "organization" => "NHIF Tanzania",
    "system_name"  => "Breach Management System",
    "export_date"  => date('Y-m-d H:i:s'),
    "total_records" => count($data),
    "incidents"    => $data
], JSON_PRETTY_PRINT);

// 5. CLOSE CONNECTION
$conn->close();
?>