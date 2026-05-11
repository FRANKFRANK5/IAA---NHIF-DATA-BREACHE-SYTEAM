<?php
/**
 * NHIF SECURE AI BRIDGE (GROK CONNECTOR)
 * Purpose: Provides a secure API endpoint for AI analysis.
 */

header('Content-Type: application/json');
require_once 'db_connection.php';

// 1. SECURITY: The Secret API Key
// Hii ndio key utakayompa Grok ili aweze kuingia.
$authorized_key = "NHIF_GROK_SECURE_ACCESS_2026";

// Check if the key is provided in the URL
$provided_key = $_GET['api_key'] ?? '';

if ($provided_key !== $authorized_key) {
    http_response_code(401); // Unauthorized
    echo json_encode([
        "status" => "error",
        "message" => "Invalid API Key. Access Denied."
    ]);
    exit();
}

// 2. DATA AGGREGATION: Fetching analytics for Grok
$stats = [];

// Hesabu jumla ya matukio yote
$total_res = $conn->query("SELECT COUNT(*) as total FROM breaches");
$stats['total_incidents'] = $total_res->fetch_assoc()['total'];

// Hesabu matukio yenye hatari kubwa (High/Critical)
$critical_res = $conn->query("SELECT COUNT(*) as critical FROM breaches WHERE severity IN ('High', 'Critical')");
$stats['critical_threats'] = $critical_res->fetch_assoc()['critical'];

// Kuchukua matukio 5 ya mwisho
$latest_res = $conn->query("SELECT title, severity, status FROM breaches ORDER BY created_at DESC LIMIT 5");
$latest_incidents = [];
while ($row = $latest_res->fetch_assoc()) {
    $latest_incidents[] = $row;
}
$stats['recent_activity'] = $latest_incidents;

// 3. OUTPUT: Send data in JSON format
echo json_encode([
    "status" => "success",
    "system" => "NHIF Incident Management",
    "data" => $stats,
    "timestamp" => date("Y-m-d H:i:s")
]);

$conn->close();
?>