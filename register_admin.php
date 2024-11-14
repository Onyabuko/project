<?php
// Database configuration
$host = 'localhost';
$dbname = 'sgs';
$username = 'root'; // replace with your database username
$password = ''; // replace with your database password

try {
    // Create a new PDO instance
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set PDO error mode to exception
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Prepare the SQL statement
    $stmt = $db->prepare("INSERT INTO admin (username, email, password) VALUES (:username, :email, :password)");
    
    // Bind parameters
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    // Execute and check if successful
    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location.href='admin-login.php';</script>";
    } else {
        echo "<script>alert('Registration failed. Please try again.');</script>";
    }
}
?>
