
<?php
	error_reporting(-1); // reports all errors
	ini_set("display_errors", "1"); // shows all errors
	ini_set("log_errors", 1);
	ini_set("error_log", "/tmp/php-error.log");

	include "DatabaseConnection.php";

	$inData = getRequestInfo();
	
	// NOTE TO FUTURE COLLABORATORS:
	// This is commented out for now because it seemed like it didn't do anything
	// Trying uncommenting if potential issues with login
	// $id = 0;
	// $firstName = "";
	// $lastName = "";

	// Get user from database that matches Login & Password
	$stmt = $conn->prepare("SELECT ID,firstName,lastName FROM Users WHERE Login=? AND Password =?");
	$stmt->bind_param("ss", $inData["login"], $inData["password"]);
	$stmt->execute();
	$result = $stmt->get_result();

	// Return with logged in user if successful
	if( $row = $result->fetch_assoc()  )
	{
		returnWithInfo( $row['firstName'], $row['lastName'], $row['ID'] );
	}
	
	// If unsuccessful, return with error
	else
	{
		returnWithError("");
	}

	// Close statement and end connection
	$stmt->close();
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
		$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
		sendResultInfoAsJson( $retValue );
	}
	
	function returnWithInfo( $firstName, $lastName, $id )
	{
		$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
		sendResultInfoAsJson( $retValue );
	}
	
?>
