<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost"; // Your server name
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "sgs"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for success/error messages
$success_message = "";
$error_message = "";

// Fetch current username from the database
$user_id = $_SESSION['user_id'];
$sql = "SELECT username FROM students WHERE id = ?";
$stmt = $conn->prepare($sql);

// Check if the statement was prepared successfully
if ($stmt === false) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username);
$stmt->fetch();
$stmt->close();

// Handle form submission for updating username
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_username'])) {
    $new_username = trim($_POST['username']);

    if (!empty($new_username)) {
        $sql = "UPDATE students SET username = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        // Check if the statement was prepared successfully
        if ($stmt === false) {
            die("Error preparing statement: " . $conn->error);
        }

        $stmt->bind_param("si", $new_username, $user_id);

        if ($stmt->execute()) {
            $success_message = "Profile updated successfully.";
        } else {
            $error_message = "Error updating profile: " . $stmt->error; // Use $stmt->error for statement error
        }
        $stmt->close();
    } else {
        $error_message = "Username cannot be empty.";
    }
}

// Handle form submission for changing password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (!empty($current_password) && !empty($new_password) && !empty($confirm_password)) {
        if ($new_password === $confirm_password) {
            // Verify the current password
            $sql = "SELECT password FROM students WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $stmt->bind_result($hashed_password);
            $stmt->fetch();
            $stmt->close();

            if (password_verify($current_password, $hashed_password)) {
                // Update the password
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $sql = "UPDATE students SET password = ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("si", $new_hashed_password, $user_id);

                if ($stmt->execute()) {
                    $success_message = "Password changed successfully.";
                } else {
                    $error_message = "Error changing password: " . $stmt->error;
                }
                $stmt->close();
            } else {
                $error_message = "Current password is incorrect.";
            }
        } else {
            $error_message = "New passwords do not match.";
        }
    } else {
        $error_message = "All fields are required.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <title>Student Dashboard</title>
   <!-- Bootstrap CSS -->
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
   <!-- Custom CSS -->
   <link rel="stylesheet" href="css/style.css">
   <!-- Font Awesome for icons -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <!-- Google Fonts -->
   <link href="https://fonts.googleapis.com/css?family=Lato:400,700|Poppins:400,700&display=swap" rel="stylesheet">
   <!-- Favicon -->
   <link rel="icon" href="images/fevicon.png" type="image/gif" />
   <style>
      body {
         font-family: 'Lato', sans-serif;
      }
      .sidebar {
         width: 250px;
         height: 100vh;
         position: fixed;
         top: 0;
         left: 0;
         background-color: #343a40;
         color: #fff;
         padding-top: 20px;
      }
      .sidebar a {
         color: #fff;
         display: block;
         padding: 15px;
         font-size: 18px;
         text-decoration: none;
      }
      .sidebar a:hover {
         background-color: #495057;
         color: #fff;
         text-decoration: none;
      }
      .content {
         margin-left: 250px;
         padding: 0; /* Adjusted padding to 0 to align header */
      }
      .dashboard-header {
         background-color: #007bff;
         color: white;
         padding: 20px;
         text-align: center;
         font-size: 24px;
      }
      .card {
         border: none;
         border-radius: 10px;
         box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }
   </style>
</head>

<body>
   <!-- Sidebar -->
   <div class="sidebar">
      <h1 class="text-center"> <a href="user-dashboard.php" style=" color: orange; font-weight: bold; font-size:90%">SGS Dashboard</h1></a>
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

      <div class="container mt-3"> <!-- Reduced margin-top to keep it close to header -->
         <div class="row justify-content-center">
            <div class="col-md-6">
               <div class="card shadow">
                  <div class="card-header text-center bg-primary text-white">
                     <h3><strong>My Profile</strong></h3>
                  </div>
                  <div class="card-body">

                     <!-- Display Success or Error message -->
                     <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                           <?php echo $success_message; ?>
                        </div>
                     <?php elseif (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                           <?php echo $error_message; ?>
                        </div>
                     <?php endif; ?>

                     <!-- Profile Update Form -->
                     <form action="profile.php" method="POST">
                        <div class="form-group">
                           <label for="username">Username</label>
                           <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
                        </div>
                        <input type="hidden" name="update_username" value="1">
                        <div class="form-group text-center">
                           <button type="submit" class="btn btn-primary btn-block">Update Profile</button>
                        </div>
                     </form>

                     <!-- Link to Change Password Modal -->
                     <div class="text-center mt-3">
                        <button type="button" class="btn btn-link" data-toggle="modal" data-target="#changePasswordModal">Change Password</button>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- Change Password Modal -->
   <div class="modal fade" id="changePasswordModal" tabindex="-1" role="dialog" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
               </button>
            </div>
            <div class="modal-body">
               <form action="profile.php" method="POST">
                  <div class="form-group">
                     <label for="current_password">Current Password</label>
                     <input type="password" class="form-control" id="current_password" name="current_password" required>
                  </div>
                  <div class="form-group">
                     <label for="new_password">New Password</label>
                     <input type="password" class="form-control" id="new_password" name="new_password" required>
                  </div>
                  <div class="form-group">
                     <label for="confirm_password">Confirm New Password</label>
                     <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                  </div>
                  <input type="hidden" name="change_password" value="1">
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                     <button type="submit" class="btn btn-primary">Change Password</button>
                  </div>
               </form>
            </div>
         </div>
      </div>
   </div>

   <!-- JavaScript Files -->
   <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
