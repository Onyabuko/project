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

// Handle status updates
if (isset($_POST['update_status'])) {
    $id = $_POST['grievance_id'];
    $new_status = $_POST['new_status'];
    $conn->query("UPDATE grievances SET status = '$new_status' WHERE id = $id");
}

// Handle grievance deletion
if (isset($_POST['delete_grievance'])) {
    $id = $_POST['grievance_id'];
    $conn->query("DELETE FROM grievances WHERE id = $id");
}

// Fetch all grievances
$grievances_result = $conn->query("SELECT * FROM grievances");

// Handle admin response submission
if (isset($_POST['submit_response'])) {
    $id = $_POST['grievance_id'];
    $response = $conn->real_escape_string($_POST['admin_response']);
    $conn->query("UPDATE grievances SET admin_response = '$response' WHERE id = $id");
}

// Fetch number of unresolved grievances
$unresolved_grievances_result = $conn->query("SELECT COUNT(*) AS count FROM grievances WHERE status = 'Pending'");
$unresolved_grievances = $unresolved_grievances_result->fetch_assoc()['count'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Grievances - Student Grievance System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        #wrapper {
            display: flex;
        }
        #sidebar-wrapper {
            min-height: 100vh;
            width: 250px;
            background-color: #343a40;
            padding:0px;
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

<div class="container mt-5">
    <h1 class="mb-4">Manage Grievances</h1>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($grievance = $grievances_result->fetch_assoc()): ?>
                <tr>
                    <td><?= $grievance['id'] ?></td>
                    <td><?= $grievance['message'] ?></td>
                    <td>
                        <span class="badge <?= $grievance['status'] == 'Resolved' ? 'badge-success' : 'badge-warning' ?>">
                            <?= $grievance['status'] ?>
                        </span>
                    </td>
                    <td><?= $grievance['submission_date'] ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#viewModal<?= $grievance['id'] ?>">View</button>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="grievance_id" value="<?= $grievance['id'] ?>">
                            <input type="hidden" name="new_status" value="<?= $grievance['status'] == 'Resolved' ? 'Pending' : 'Resolved' ?>">
                            <button type="submit" name="update_status" class="btn btn-<?= $grievance['status'] == 'Resolved' ? 'warning' : 'success' ?> btn-sm">
                                <?= $grievance['status'] == 'Resolved' ? 'Mark Pending' : 'Mark Resolved' ?>
                            </button>
                        </form>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="grievance_id" value="<?= $grievance['id'] ?>">
                            <button type="submit" name="delete_grievance" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this grievance?');">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>

                <!-- View Modal with Admin Response -->
                <div class="modal fade" id="viewModal<?= $grievance['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?= $grievance['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel<?= $grievance['id'] ?>">Grievance Details</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>ID:</strong> <?= $grievance['id'] ?></p>
                                <p><strong>Category:</strong> <?= $grievance['grievance_type'] ?></p>
                                <p><strong>Message:</strong> <?= $grievance['message'] ?></p>
                                <p><strong>Status:</strong> <?= $grievance['status'] ?></p>
                                <p><strong>Date Submitted:</strong> <?= $grievance['submission_date'] ?></p>
                                <hr>
                                <p><strong>Admin Response:</strong></p>
                                <p><?= !empty($grievance['admin_response']) ? $grievance['admin_response'] : "No response yet." ?></p>
                                <form method="POST">
                                    <input type="hidden" name="grievance_id" value="<?= $grievance['id'] ?>">
                                    <textarea class="form-control" name="admin_response" placeholder="Enter your response here..." rows="3"><?= $grievance['admin_response'] ?></textarea>
                                    <button type="submit" name="submit_response" class="btn btn-primary mt-2">Submit Response</button>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap and jQuery JavaScript -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.6/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
