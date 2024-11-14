<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'sgs';
$username = 'root'; 
$password = ''; 

// Create a new mysqli connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && isset($user['password'])) {
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $user['username'];
            header("Location: admin-dashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid username or password'); window.location.href='admin-login.php';</script>";
        }
    } else {
        echo "<script>alert('Invalid username or password'); window.location.href='admin-login.php';</script>";
    }
}
?>

<!-- HTML for Admin Login Form -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login</title>
    
   <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
   <link rel="stylesheet" type="text/css" href="css/style.css">
   <link rel="stylesheet" href="css/responsive.css">
   <link rel="icon" href="images/fevicon.png" type="image/gif" />
   <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
   <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
   <link href="https://fonts.googleapis.com/css?family=Lato:400,700|Poppins:400,700&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="css/owl.carousel.min.css">
   <link rel="stylesheet" href="css/owl.theme.default.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css"
      media="screen">
  

    <style>
      /* Centering the login section */
      .login-section {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        /* Card styling */
        .card {
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 42%;
        }

        .card-header {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: black;
            background-color: #007bff;
            padding: 10px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .btn-block {
            width: 100%;
        }
    </style>
</head>
<body>
<div class="header_section fixed-top">
      <div class="container-fluid">
         <div class="row">
            <div class="col-sm-2 col-6">
               <a class="logo" href="index.php"><h1 style="font-size: 40px;color: white;">SGS</h1></a>
            </div>
            <div class="col-sm-8 col-6" style="margin-top: 15px;">
               <nav class="navbar navbar-expand-lg navbar-light bg-light">
                  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                     aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                     <span class="navbar-toggler-icon"></span>
                  </button>
                  <div class="collapse navbar-collapse" id="navbarNav">
                     <ul class="navbar-nav">
                        <li class="nav-item active">
                           <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" href="admin-login.php">Admin</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" href="user-login.php">User Login</a>
                        </li>
                        <li class="nav-item">
                           <a class="nav-link" href="registration.php">User Regsitration</a>
                        </li>
                     </ul>
                  </div>
               </nav>
            </div>
         </div>
      </div>
   </div>
   <!-- header section end -->
   <br><br>
<br>
   <!-------login form ---->
   <div class="login-section">
    <div class="card">
        <div class="card-header">
            Admin Login
        </div>
        <div class="card-body">
            <form action="admin-login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
        </div>
    </div>
</div>


<!----footer--->

   <div class="copyright_section">
      <div class="container">
         <p class="copyright_text">&copy; Students Grievance System.</p>
      </div>
   </div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
