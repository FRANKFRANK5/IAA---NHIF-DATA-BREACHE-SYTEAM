<?php
/**
 * NHIF SECURITY INCIDENT DASHBOARD
 * Purpose: Provides a live administrative overview of security breaches from the BMS database.
 */

session_start();
require_once 'db_connection.php';

// 1. ACCESS CONTROL: Ensure the administrator is authenticated
if (!isset($_SESSION['admin_id'])) {
    header("Location: auth_login.php");
    exit();
}

/**
 * 2. DATA RETRIEVAL & SANITIZATION
 * We fetch all incidents and ensure the 'source' field is populated 
 * to prevent "Data Not Found" errors in the JavaScript filter.
 */
$query = "SELECT * FROM breaches ORDER BY created_at DESC";
$result = $conn->query($query);

$breach_data = [];
if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        /**
         * DATA MIGRATION LOGIC:
         * If the 'source' column is empty (for old records), 
         * we categorize it as 'internal' by default.
         */
        if (empty($row['source'])) {
            $row['source'] = 'internal'; 
        }
        
        $breach_data[] = $row; 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NHIF Admin - SECURITY PORTAL</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        /*  Aesthetic Styling */
        body { 
            background-color: #141d2b; /* Dark Navy/Black */
            color: #9feaf9; 
            font-family: 'Courier New', Courier, monospace; 
        }

        .navbar-nhif { 
            background-color: #0d121b; 
            border-bottom: 2px solid #9fef00; /* Neon Green Border */
        }

        .navbar-brand { color: #9fef00 !important; text-shadow: 0 0 10px #9fef00; }

        /* Severity Colors - HTB Style */
        .severity-Critical { background-color: #ff0000 !important; color: white !important; box-shadow: 0 0 10px #ff0000; }
        .severity-High { background-color: #ff4d00 !important; color: white !important; }
        .severity-Medium { background-color: #ffc107 !important; color: black !important; }
        .severity-Low { background-color: #9fef00 !important; color: black !important; }

        /* Dashboard Cards */
        .card-stats { 
            background-color: #1b2738; 
            border: 1px solid #2d3e52; 
            border-left: 5px solid #9fef00; 
            color: #9feaf9;
        }

        .text-muted { color: #627b9d !important; }
        .fw-bold { color: #ffffff; }

        /* Table Styling */
        .table-responsive { 
            background-color: #1b2738 !important; 
            border: 1px solid #2d3e52;
        }

        .table { color: #9feaf9 !important; }
        .table-dark { --bs-table-bg: #0d121b; }
        
        .table-hover tbody tr:hover { 
            background-color: rgba(159, 239, 0, 0.05) !important; 
            color: #ffffff !important;
        }

        /* Input and Modal */
        .form-control, .form-select { 
            background-color: #0d121b; 
            border: 1px solid #2d3e52; 
            color: #9fef00; 
        }
        .form-control:focus { background-color: #0d121b; color: #9fef00; border-color: #9fef00; box-shadow: none; }

        .modal-content { background-color: #1b2738; border: 1px solid #9fef00; color: #9feaf9; }
        .modal-header { border-bottom: 1px solid #2d3e52; }
        .modal-footer { border-top: 1px solid #2d3e52; }
        .bg-light { background-color: #0d121b !important; color: #9fef00; border: 1px solid #2d3e52; }

        .btn-success { background-color: #9fef00; border: none; color: black; font-weight: bold; }
        .btn-success:hover { background-color: #82c400; color: black; }
        .btn-outline-info { color: #9fef00; border-color: #9fef00; }
        .btn-outline-info:hover { background-color: #9fef00; color: black; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-nhif shadow mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">[#] NHIF_INCIDENT_MGMT</a>
        <a href="logout.php" class="btn btn-outline-danger btn-sm">Terminate Session</a>
    </div>
</nav>

<div class="container mb-4 text-center">
    <div class="btn-group shadow">
        <button class="btn btn-outline-success active" onclick="filterBySource('all')">Zote</button>
        <button class="btn btn-outline-success" onclick="filterBySource('internal')">Internal Logs</button>
        <button class="btn btn-outline-success" onclick="filterBySource('public')">Public Reports</button>
    </div>
</div>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card card-stats shadow-sm p-3">
                <h6 class="text-muted">Total Security Incidents</h6>
                <h3 id="totalBreaches" class="fw-bold">0</h3>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-stats shadow-sm p-3" style="border-left-color: #ff0000;">
                <h6 class="text-muted">Critical & High Threats</h6>
                <h3 id="criticalCount" class="fw-bold text-danger">0</h3>
            </div>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-5">
            <input type="text" id="searchInput" class="form-control shadow-sm" placeholder="Search logs by ID or Title...">
        </div>
        <div class="col-md-7 text-end">
            <button class="btn btn-success shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#addModal">
                <b>+</b> Log_New_Incident
            </button>
        </div>
    </div>

    <div class="table-responsive shadow-sm p-3 rounded">
        <table class="table table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Ref ID</th>
                    <th>Date Reported</th>
                    <th>Incident Title</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                </tbody>
        </table>
        <div id="noResults" class="text-center py-4 text-muted" hidden>No records found in current segment.</div>
    </div>
</div>

<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark">
                <h5 class="modal-title" style="color:#9fef00">Breach Intelligence Detail</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div id="modalBody" class="modal-body">
                </div>
        </div>
    </div>
</div>

<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark">
                <h5 class="modal-title" style="color:#9fef00">Log New Security Incident</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="save_breach.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Incident Title</label>
                        <input type="text" name="title" class="form-control" placeholder="..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Severity Level</label>
                        <select name="severity" class="form-select">
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Incident Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4">Execute Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
/**
 * NHIF Incident Management System - Client Side Logic
 * Handles data filtering, XSS prevention, and dynamic UI updates.
 */

// Global data store injected from PHP
const breaches = <?php echo json_encode($breach_data); ?> || [];
let currentFilter = 'all'; // Tracks the current active source filter (internal/public)

/**
 * SANITIZATION: Prevents Cross-Site Scripting (XSS) 
 * by encoding special characters into HTML entities.
 */
function escapeHTML(str) {
    if (!str) return "";
    return str.replace(/[&<>"']/g, function(m) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[m];
    });
}

/**
 * UI CONTROLLER: Handles button clicks for source filtering.
 * Switches between Internal Logs and Public Reports.
 */
function filterBySource(sourceType) {
    currentFilter = sourceType;
    
    // Update UI: Toggle the 'active' class for buttons
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Highlight the clicked button
    if(event) event.target.classList.add('active');

    // Re-render table with the current search input and new filter
    renderTable(document.getElementById("searchInput").value);
}

/**
 * CORE RENDERER: Filters and displays the breach data into the HTML table.
 * Implements multi-layer filtering (Search Query + Source Type).
 */
function renderTable(searchTerm = "") {
    const tableBody = document.getElementById("tableBody");
    const noResults = document.getElementById("noResults");
    tableBody.innerHTML = "";

    // Apply filtering logic
    const filtered = breaches.filter(b => {
        // Layer 1: Search by ID or Title
        const matchesSearch = b.title.toLowerCase().includes(searchTerm.toLowerCase()) || 
                             b.id.toString().includes(searchTerm);
        
        // Layer 2: Filter by Source (Internal vs Public)
        const matchesSource = (currentFilter === 'all') || (b.source === currentFilter);
        
        return matchesSearch && matchesSource;
    });

    // Toggle 'No Results' visibility
    noResults.hidden = (filtered.length > 0);

   // Inject filtered rows into the DOM
    filtered.forEach(b => {
        const row = `
            <tr>
                <td><strong style="color:#9fef00">#BR-${b.id}</strong></td>
                <td>${b.created_at}</td>
                <td>${escapeHTML(b.title)}</td>
                <td><span class="badge severity-${b.severity}">${b.severity.toUpperCase()}</span></td>
                <td>
                    <span class="badge bg-secondary small" style="font-size:0.7rem">${b.source.toUpperCase()}</span>
                </td>
                <td>
                    <button class="btn btn-sm btn-outline-info me-1" onclick="showDetails(${b.id})">
                        Review
                    </button>
                    
                    <a href="delete_breach.php?id=${b.id}" 
                       class="btn btn-sm btn-outline-danger" 
                       onclick="return confirm('WARNING: Are you sure you want to delete this record? This action cannot be undone and digital evidence will be lost.')">
                        Delete
                    </a>
                </td>
            </tr>`;
        tableBody.innerHTML += row;
    });
    

    // Update the dashboard counter cards
    updateDashboardStats();
}

/**
 * MODAL HANDLER: Fetches specific incident details 
 * and displays them in a secure modal popup.
 */
function showDetails(id) {
    const b = breaches.find(x => x.id == id);
    if (!b) return;

    document.getElementById("modalBody").innerHTML = `
        <div class="p-1">
            <label class="text-muted small">Incident Title</label>
            <p class="fw-bold mb-3 border-bottom border-secondary pb-2" style="color:#9fef00">${escapeHTML(b.title)}</p>
            <div class="row mb-3">
                <div class="col-6">
                    <label class="text-muted small d-block">Severity</label>
                    <span class="badge severity-${b.severity}">${b.severity}</span>
                </div>
                <div class="col-6">
                    <label class="text-muted small d-block">Source</label>
                    <span class="text-info fw-bold">${b.source.toUpperCase()}</span>
                </div>
            </div>
            <label class="text-muted small">Incident Description</label>
            <div class="bg-light p-3 rounded" style="font-size:0.9rem; min-height:100px;">
                ${escapeHTML(b.description)}
            </div>
        </div>
    `;
    new bootstrap.Modal(document.getElementById('detailsModal')).show();
}

/**
 * STATISTICS: Calculates and updates the summary counts 
 * on the top dashboard cards.
 */
function updateDashboardStats() {
    const totalCount = breaches.length;
    const criticalHighCount = breaches.filter(b => b.severity === 'Critical' || b.severity === 'High').length;
    document.getElementById("totalBreaches").innerText = totalCount;
    document.getElementById("criticalCount").innerText = criticalHighCount;
}

// Event Listener for real-time search filtering
document.getElementById("searchInput").addEventListener("input", (e) => {
    renderTable(e.target.value);
});

// Initialize table on page load
document.addEventListener("DOMContentLoaded", () => {
    renderTable();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>