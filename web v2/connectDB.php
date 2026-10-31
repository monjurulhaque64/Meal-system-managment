<?php
/* Database connection settings */
	$servername = "localhost";
    $username = "user put it here";		//put your phpmyadmin username.(default is "root")
    $password = "password put it here";			//if your phpmyadmin has a password put it here.(default is "root")
    $dbname = "qarabicn_meals";
    
	$conn = mysqli_connect($servername, $username, $password, $dbname);
	
	if ($conn->connect_error) {
        die("Database Connection failed: " . $conn->connect_error);
    }
?>
