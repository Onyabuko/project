<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin-login.php");
    exit();
}

// Database configuration
$host = 'localhost';
$dbname = 'sgs';
$username = 'root'; 
$password = ''; 

// Create a new mysqli connection
$conn = new mysqli($host, $username, $password, $dbname);

// Fetch statistics
$total_grievances_result = $conn->query("SELECT COUNT(*) AS count FROM grievances");
$total_grievances = $total_grievances_result->fetch_assoc()['count'];

$resolved_grievances_result = $conn->query("SELECT COUNT(*) AS count FROM grievances WHERE status = 'Resolved'");
$resolved_grievances = $resolved_grievances_result->fetch_assoc()['count'];

$pending_grievances_result = $conn->query("SELECT COUNT(*) AS count FROM grievances WHERE status = 'Pending'");
$pending_grievances = $pending_grievances_result->fetch_assoc()['count'];
// Fetch number of unresolved grievances
$unresolved_grievances_result = $conn->query("SELECT COUNT(*) AS count FROM grievances WHERE status = 'Pending'");
$unresolved_grievances = $unresolved_grievances_result->fetch_assoc()['count'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Student Grievance System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        #wrapper {
            display: flex;
        }
        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            background-color: #343a40;
        }
        #page-content-wrapper {
            flex: 1;
        }
        .sidebar-heading {
            font-size: 1.5rem;
            padding: 1rem;
            text-align: center;
            color: #fff;
        }
        .list-group-item {
            color: #ccc;
        }
        .list-group-item:hover {
            background-color: #495057;
            color: #fff;
        }
        .card {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card h5 {
            font-size: 1.25rem;
        }
        .navbar {
            padding: 0.5rem 1rem;
        }
        .container-fluid h1 {
            font-size: 1.75rem;
        }
    </style>
</head>
<body>

<!----sidebar--->
<div id="wrapper">
    <div class="bg-dark border-right" id="sidebar-wrapper">
        <a href="admin-dashboard.php" style="de"><div class="sidebar-heading"><i class="fas fa-user-shield"></i> Admin Dashboard</div></a>
        <div class="list-group list-group-flush">
            <a href="admin-dashboard.php" class="list-group-item list-group-item-action bg-dark">
                <i class="fas fa-tachometer-alt"></i> Dashboard Overview
            </a>
            <a href="manage_grievances.php" class="list-group-item list-group-item-action bg-dark">
    <i class="fas fa-tasks"></i> Manage Grievances
    <?php if ($unresolved_grievances > 0): ?>
        <span class="badge badge-danger ml-2"><?= $unresolved_grievances ?></span>
    <?php endif; ?>
</a>
            <a href="manage_categories.php" class="list-group-item list-group-item-action bg-dark">
                      <i class="fas fa-cogs"></i> Manage Categories
            </a>
            <a href="reports.php" class="list-group-item list-group-item-action bg-dark">
                <i class="fas fa-chart-line"></i> Reports
            </a>
            <a href="settings.php" class="list-group-item list-group-item-action bg-dark">
                <i class="fas fa-cogs"></i> Settings
            </a>
            <a href="admin-logout.php" class="list-group-item list-group-item-action bg-dark">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
<!--- end of side bar--->

    <div id="page-content-wrapper">
                <div class="container-fluid mt-4">
            <h1 class="mb-4">Dashboard Overview</h1>
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card text-white bg-info">
                        <div class="card-body text-center">
                            <h5 class="card-title"><i class="fas fa-exclamation-circle"></i> Total Grievances</h5>
                            <p class="card-text display-4"><?= $total_grievances ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card text-white bg-success">
                        <div class="card-body text-center">
                            <h5 class="card-title"><i class="fas fa-check-circle"></i> Resolved Grievances</h5>
                            <p class="card-text display-4"><?= $resolved_grievances ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 mb-4">
                    <div class="card text-white bg-warning">
                        <div class="card-body text-center">
                            <h5 class="card-title"><i class="fas fa-clock"></i> Pending Grievances</h5>
                            <p class="card-text display-4"><?= $pending_grievances ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap and jQuery JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>



</body>
</html>
