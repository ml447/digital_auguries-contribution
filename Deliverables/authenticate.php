<?php
#!/usr/bin/php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
// Change this to your connection info.
define('DATABASE_HOST', 'localhost');
define('DATABASE_USER', 'ml447');
define('DATABASE_PASS', 'ITRabbit$490');
define('DATABASE_NAME', 'phplogin');
// Try and connect using the info above.
$con = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
if ( !isset($_POST['username'], $_POST['password']) ) {
	// Could not get the data that should have been sent.
	exit('Please fill both the username and password fields!');
}
// Echo username and password for testing only!
//echo 'Username: ' . $_POST['username'] . '<br>';
//echo 'Password: ' . $_POST['password'] . '<br>';
//echo $password;
if ($stmt = $con->prepare('SELECT id, password FROM accounts WHERE username = ?')) {
	// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
	$stmt->bind_param('s', $_POST['username']);
	$stmt->execute();
	// Store the result so we can check if the account exists in the database.
	$stmt->store_result();
	if ($stmt->num_rows > 0) {
	$stmt->bind_result($id, $password);
	$stmt->fetch();


	// Account exists, now we verify the password.
// Note: remember to use password_hash in your registration file to store the hashed passwords.
// Echoing the password being compared to for testing purposes
//	echo 'Password to Compare: ' . $password . '<br>'; // Echo the password
//	$string1 = $_POST['password'];
//	$string2 = $password;	
//	if ($string1 == $string2) {
  //  	echo "Strings are equal.";
//	} else {
  //  	echo "Strings are not equal.";
//	}

	if (password_verify($_POST['password'], $password)) {
		// Verification success! User has logged-in!
		// Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
		session_regenerate_id();
		$_SESSION['loggedin'] = TRUE;
		$_SESSION['name'] = $_POST['username'];
		$_SESSION['id'] = $id;

		// Send login success to RabbitMQ
		$output = exec("python3 rabbitmq_publisher.py");


		//Runs python script to RabbitMQ
	//	$output = exec("python3 hello.py 2>&1");
	//	$output = exec("python3 rabbitmq_publisher.py");
//		echo $output;
		// Redirect the user to the main site
           	 header("Location: mainsite.html");
		exit(); // Ensure that script execution stops after redirection
	} else {
		// Send login failure to RabbitMQ
		$output = exec("python3 rabbitmq_fail.py");
		// Incorrect password, redirect back to login page with error message
			header("Location: index.html");
			exit();
//		 echo 'Incorrect username or password!<br>';
		 // Debugging Checkpoint: Compare hashed passwords directly Testing only, probably DELETE THIS AND THE 3 lines below.
	//	echo 'Hashed password from DB: ' . $password . '<br>';
	//	$hashedEnteredPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
	//	echo 'Hashed entered password: ' . $hashedEnteredPassword . '<br>';

	}
	} else {
	// Send login failure to RabbitMQ
        $output = exec("python3 rabbitmq_fail.py");
	// Incorrect username, redirect back to login page with error message
		header("Location: index.html");
		exit();
//	echo 'Incorrect username or password!';
}


	$stmt->close();
}
?>
