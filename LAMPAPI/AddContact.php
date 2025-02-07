<?php
	error_reporting(-1); // reports all errors
	ini_set("display_errors", "1"); // shows all errors
	ini_set("log_errors", 1);
	ini_set("error_log", "/tmp/php-error.log");

	include "DatabaseConnection.php";

	$inData = getRequestInfo();

	// Get input data from add_contact.html form
	$first = $inData["first"];
	$last = $inData["last"];
	$phoneNumber = $inData["phoneNumber"];
	$email = $inData["email"];

	// Convert UserId from string to int
	$currentUserId = (int)$inData["currentUserId"];

	// Ensure data is not empty
	if ($first == "" || $first == null || $last == "" || $last == null || $phoneNumber == "" || $phoneNumber == null || $email == "" || $phoneNumber == null)
	{
		returnWithError("Please ensure all forms are filled");
	}
	else
	{
		// Validate phone number with regex expression
		if (!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phoneNumber))
		{
			returnWithError("Invalid phone number. Please use format: ###-###-####");
		}
		// Validate email with 
		else if (!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			returnWithError("Invalid email address. Please use format: email@address.com");
		}

		// If all input data is valid, 
		//  insert new contact into MySQL database
		else
		{
			$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES(?, ?, ?, ?, ?)");
			$stmt->bind_param("sssss", $first, $last, $phoneNumber, $email, $currentUserId);
			$stmt->execute();
			$stmt->close();
			returnWithSuccess();
		}
	}

	// Close MySQL connection and return
	$conn->close();

	function getRequestInfo()
	{
		return json_decode(file_get_contents('php://input'), true);
	}

	function sendResultInfoAsJson( $obj )
	{
		header('Content-type: application/json');
		echo $obj;
	}
	
	function returnWithError( $err )
	{
        $retValue = '{"error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
    
    function returnWithSuccess()
	{
        $retValue = '{"error":""}';
		sendResultInfoAsJson( $retValue );
	}
?>
