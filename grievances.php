<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sgs";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $category = $_POST['category'];
    $message = $_POST['message'];
    $status = 'Pending';
    $regDate = date('Y-m-d H:i:s');

    $complaintFile = null;
    if (!empty($_FILES['complaintFile']['name'])) {
        $targetDir = "uploads/";
        $complaintFile = $targetDir . basename($_FILES['complaintFile']['name']);
        if (!move_uploaded_file($_FILES['complaintFile']['tmp_name'], $complaintFile)) {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    // Insert data into the grievances table
    $sql = "INSERT INTO grievances (student_id, grievance_type, message, file, submission_date, status) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $conn->error);
    } else {
        $stmt->bind_param("isssss", $userId, $category, $message, $complaintFile, $regDate, $status);
        if ($stmt->execute()) {
            echo "Grievance submitted successfully.";
            header("Location: grievances.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch grievances for the logged-in user
$sql = "SELECT * FROM grievances WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Lato:400,700|Poppins:400,700&display=swap" rel="stylesheet">
    <link rel="icon" href="images/fevicon.png" type="image/gif" />
    <style>
        body { font-family: 'Lato', sans-serif; }
        .sidebar { width: 250px; height: 100vh; position: fixed; top: 0; left: 0; background-color: #343a40; color: #fff; padding-top: 20px; }
        .sidebar a { color: #fff; display: block; padding: 15px; font-size: 18px; text-decoration: none; }
        .sidebar a:hover { background-color: #495057; color: #fff; text-decoration: none; }
        .content { margin-left: 250px; padding: 0; }
        .dashboard-header { background-color: #007bff; color: white; padding: 20px; text-align: center; font-size: 24px; }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <h1 class="text-center"><a href="user-dashboard.php" style="color: orange; font-weight: bold; font-size:90%">SGS Dashboard</a></h1>
        <a href="profile.php"><i class="fas fa-user-circle"></i> Profile</a>
        <a href="grievances.php"><i class="fas fa-folder-open"></i> My Grievances</a>
        <a href="submit-grievance.php"><i class="fas fa-edit"></i> Submit Grievance</a>
        <a href="notifications.php"><i class="fas fa-bell"></i> Notifications</a>
        <a href="support.php"><i class="fas fa-question-circle"></i> Support</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <!-- Main Content -->
    <div class="content">
        <div class="dashboard-header">
            <h2><b>Student Grievance System</b></h2>
        </div>

        <div class="container mt-3">
            <!-- Submit Grievance Button -->
            <div class="text-center mb-4">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#submitGrievanceModal">Submit Grievance</button>
            </div>

            <!-- Submit Grievance Modal -->
            <div class="modal fade" id="submitGrievanceModal" tabindex="-1" role="dialog" aria-labelledby="submitGrievanceModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="submitGrievanceModalLabel">Submit Grievance</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="grievances.php" method="POST" enctype="multipart/form-data">
                                <div class="form-group">
                                    <label for="category">Category</label>
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="">Select Category</option>
                                        <option value="Academic">Academic</option>
                                        <option value="Administrative">Administrative</option>
                                        <option value="Financial">Financial</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="message">Message</label>
                                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="complaintFile">Attach File</label>
                                    <input type="file" class="form-control-file" id="complaintFile" name="complaintFile">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grievance Table -->
            <h3 class="text-center mb-4">My Grievances</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Grievance Number</th>
                        <th>Category</th>
                        <th>Message</th>
                        <th>Complaint File</th>
                        <th>Registration Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['id']); ?></td>
                <td><?php echo htmlspecialchars($row['grievance_type']); ?></td>
                <td><?php echo htmlspecialchars($row['message']); ?></td>
                <td>
                    <?php if (!empty($row['file'])): ?>
                        <a href="<?php echo htmlspecialchars($row['file']); ?>" target="_blank">View File</a>
                    <?php else: ?>
                        N/A
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['submission_date']); ?></td>
                <td><?php echo htmlspecialchars($row['status']); ?></td>
                <td>
                    <!-- View Responses Button -->
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#responseModal<?php echo $row['id']; ?>">View Responses</button>
                    
                    <!-- Response Modal -->
                    <div class="modal fade" id="responseModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="responseModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="responseModalLabel<?php echo $row['id']; ?>">Admin Response</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <?php echo htmlspecialchars($row['admin_response'] ?? 'No response yet.'); ?>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="7" class="text-center">No grievances found.</td>
        </tr>
    <?php endif; ?>
</tbody>

            </table>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
