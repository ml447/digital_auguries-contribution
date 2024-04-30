<?php
// Database connection parameters
define('DB_HOST', 'localhost');
define('DB_USER', 'ml447');
define('DB_PASS', 'ITRabbit$490');
define('DB_NAME', 'phplogin');

// Connect to the database
$con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $email = mysqli_real_escape_string($con, $_POST['email']); // Add email validation

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user data into the database
    $sql = "INSERT INTO accounts (username, password, email) VALUES ('$username', '$hashed_password', '$email')"; // Update SQL query
    if (mysqli_query($con, $sql)) {
        echo "Registration successful!";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($con);
    }
}

// Close the database connection
mysqli_close($con);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registration Form</title>
</head>
<body>
    <h2>Registration Form</h2>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        Username: <input type="text" name="username" required><br>
        Password: <input type="password" name="password" required><br>
        Email: <input type="email" name="email" required><br> <!-- Add email input -->
        <input type="submit" value="Register">
    </form>
</body>
</html>

