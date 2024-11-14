<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}
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
      <h1 class="text-center"> <a href="user-dashboard.php"  style=" color: orange; font-weight: bold; font-size:90%">SGS Dashboard</h1></a>
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

      <!-- Card Section -->
      <div class="container mt-4">
         <div class="row">
            <!-- Card for Profile Overview -->
            <div class="col-md-4 mb-4">
               <div class="card">
                  <div class="card-body text-center">
                     <i class="fas fa-user-circle fa-3x text-primary mb-3"></i>
                     <h5 class="card-title">Profile Overview</h5>
                     <p class="card-text">View and update your personal information.</p>
                     <a href="profile.php" class="btn btn-primary">View Profile</a>
                  </div>
               </div>
            </div>

            <!-- Card for Grievance Submission -->
            <div class="col-md-4 mb-4">
               <div class="card">
                  <div class="card-body text-center">
                     <i class="fas fa-edit fa-3x text-success mb-3"></i>
                     <h5 class="card-title">Submit a Grievance</h5>
                     <p class="card-text">Report issues and grievances directly to the administration.</p>
                     <a href="submit-grievance.php" class="btn btn-success">Submit Now</a>
                  </div>
               </div>
            </div>

            <!-- Card for Notifications -->
            <div class="col-md-4 mb-4">
               <div class="card">
                  <div class="card-body text-center">
                     <i class="fas fa-bell fa-3x text-warning mb-3"></i>
                     <h5 class="card-title">Notifications</h5>
                     <p class="card-text">Stay updated with the latest notifications from SGS.</p>
                     <a href="notifications.php" class="btn btn-warning">View Notifications</a>
                  </div>
               </div>
            </div>
         </div>

         <div class="row">
            <!-- Card for My Grievances -->
            <div class="col-md-4 mb-4">
               <div class="card">
                  <div class="card-body text-center">
                     <i class="fas fa-folder-open fa-3x text-info mb-3"></i>
                     <h5 class="card-title">My Grievances</h5>
                     <p class="card-text">View all grievances you have submitted in the past.</p>
                     <a href="grievances.php" class="btn btn-info">View Grievances</a>
                  </div>
               </div>
            </div>

            <!-- Card for Support -->
            <div class="col-md-4 mb-4">
               <div class="card">
                  <div class="card-body text-center">
                     <i class="fas fa-question-circle fa-3x text-secondary mb-3"></i>
                     <h5 class="card-title">Support</h5>
                     <p class="card-text">Need assistance? Get support from the SGS team.</p>
                     <a href="support.php" class="btn btn-secondary">Get Support</a>
                  </div>
               </div>
            </div>

            <!-- Card for Settings -->
            <div class="col-md-4 mb-4">
               <div class="card">
                  <div class="card-body text-center">
                     <i class="fas fa-cog fa-3x text-dark mb-3"></i>
                     <h5 class="card-title">Settings</h5>
                     <p class="card-text">Customize your dashboard experience.</p>
                     <a href="settings.php" class="btn btn-dark">Open Settings</a>
                  </div>
               </div>
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
