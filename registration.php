<?php
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

// Start session
session_start();

// Set up PHPMailer
require_once 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Function to send OTP via email
function sendOTPByEmail($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'onyabukoisaac@gmail.com';
        $mail->Password = 'xrojhegmaovrtcpo';
        $mail->Port = 587; // TLS
        
        $mail->setFrom('onyabukoisaac@gmail.com', 'Admin');
        $mail->addAddress($email);
        
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Code';
        $mail->Body = "<h3>Your OTP Code:</h3><p>$otp</p>";
        $mail->AltBody = "Your OTP code is: $otp";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        return false;
    }
}

// Handle OTP request via AJAX
if (isset($_POST['action']) && $_POST['action'] == 'request_otp') {
    $email = $_POST['email'];
    if (filter_var($email, FILTER_VALIDATE_EMAIL) && substr($email, -11) === '@muni.ac.ug') {
        $otp = rand(100000, 999999);
        $_SESSION['otp'] = $otp;
        
        if (sendOTPByEmail($email, $otp)) {
            echo json_encode(['status' => 'success', 'message' => 'OTP sent to your email!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send OTP. Please try again.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid Muni email address.']);
    }
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_otp'])) {
    $input_otp = $_POST['otp'];
    if ($input_otp == $_SESSION['otp']) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        if ($password !== $confirm_password) {
            echo "<script>alert('Passwords do not match!');</script>";
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO students (username, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $username, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>
                    alert('Registration successful!');
                    window.location.href = 'user-login.php';
                  </script>";
        } else {
            echo "<script>alert('Error: " . $stmt->error . "');</script>";
        }

        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Invalid OTP!');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Registration</title>
    <!-- Include Bootstrap, Font Awesome, and custom CSS here -->
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
   <br> <br><br>
    <section class="signup-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card shadow">
                        <div class="card-header text-center bg-primary text-white">
                            <h1><b>User Registration</b></h1>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST" id="registrationForm">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your Muni email" required>
                                </div>
                                <div class="form-group text-center">
                                    <button type="button" id="requestOtpButton" class="btn btn-primary">Request OTP</button>
                                </div>
                                <div id="otp-message" style="display: none;" class="alert alert-success">
                                    OTP sent successfully!
                                </div>
                                <div class="form-group">
                                    <label for="otp">Enter OTP</label>
                                    <input type="text" class="form-control" id="otp" name="otp" placeholder="Enter the OTP" required>
                                </div>
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                                </div>
                                <div class="form-group text-center">
                                    <button type="submit" name="verify_otp" class="btn btn-primary btn-block">Sign Up</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="copyright_section">
      <div class="container">
         <p class="copyright_text">&copy; Students Grievance System.</p>
      </div>
   </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $('#requestOtpButton').click(function() {
            var email = $('#email').val();
            $.ajax({
                url: '', // Current page
                method: 'POST',
                data: { action: 'request_otp', email: email },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status === 'success') {
                        $('#otp-message').show().text(res.message);
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error:", error);
                }
            });
        });
    </script>
</body>
</html>
