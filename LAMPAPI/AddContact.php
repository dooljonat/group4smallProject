<?php
	error_reporting(-1); // reports all errors
	ini_set("display_errors", "1"); // shows all errors
	ini_set("log_errors", 1);
	ini_set("error_log", "/tmp/php-error.log");

	include "DatabaseConnection.php";

	$inData = getRequestInfo();

	// TODO: Handle input validation using regex

	// Get input data from add_contact.html form
	$first = $inData["first"];
	$last = $inData["last"];
	$phoneNumber = $inData["phoneNumber"];
	$email = $inData["email"];

	// Convert UserId from string to int
	$currentUserId = (int)$inData["currentUserId"];

	// Insert new contact into MySQL
	$stmt = $conn->prepare("INSERT INTO Contacts (FirstName, LastName, Phone, Email, UserID) VALUES(?, ?, ?, ?, ?)");
	$stmt->bind_param("sssss", $first, $last, $phoneNumber, $email, $currentUserId);
	$stmt->execute();
	$stmt->close();

	// Close MySQL connection and return
	$conn->close();
	returnWithError("");

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
	
?>