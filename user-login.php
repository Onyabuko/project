<?php
session_start();

// Database configuration
$host = 'localhost';
$dbname = 'sgs';
$username = 'root'; // replace with your database username
$password = ''; // replace with your database password

// Create connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and execute SQL statement to check user credentials
    $sql = "SELECT id, password FROM students WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    // If a user is found
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password);
        $stmt->fetch();

        // Verify the password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id; // Store user ID in session
            $_SESSION['username'] = $username;
            header("Location: user-dashboard.php");
            exit();
        } else {
            $error_message = "Incorrect username or password.";
        }
    } else {
        $error_message = "Incorrect username or password.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <title>User Login</title>
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
</head>
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
   <br> <br>

   <!---login form--->
   <div class="login-section" style="margin-top: 50px;">
      <div class="container">
         <br>
         <div class="row justify-content-center">
            <div class="col-md-6">
               <div class="card">
                  <div class="card-header text-center bg-primary text-white">
                     <h1><strong>User Login</strong></h1>
                  </div>
                  <div class="card-body">
                     <!-- Display error message if login fails -->
                     <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                           <?php echo $error_message; ?>
                        </div>
                     <?php endif; ?>

                     <!-- Login Form -->
                     <form action="user-login.php" method="POST">
                        <div class="form-group">
                           <label for="username">Username</label>
                           <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                        </div>
                        <div class="form-group">
                           <label for="password">Password</label>
                           <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        <div class="form-group text-center">
                           <button type="submit" class="btn btn-primary btn-block">Login</button>
                        </div>
                     </form>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
<br>
   <div class="copyright_section">
      <div class="container">
         <p class="copyright_text">&copy; Students Grievance System.</p>
      </div>
   </div>
   <script src="js/jquery.min.js"></script>
   <script src="js/popper.min.js"></script>
   <script src="js/bootstrap.bundle.min.js"></script>
   <script src="js/jquery-3.0.0.min.js"></script>
   <script src="js/plugin.js"></script>
   <script src="js/jquery.mCustomScrollbar.concat.min.js"></script>
   <script src="js/custom.js"></script>
   <script src="js/owl.carousel.js"></script>
   <script src="https:cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
</body>
</html>
