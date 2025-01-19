
<?php
	error_reporting(-1); // reports all errors
	ini_set("display_errors", "1"); // shows all errors
	ini_set("log_errors", 1); 	
	ini_set("error_log", "/tmp/php-error.log");

	include "DatabaseConnection.php";

	$inData = getRequestInfo();
	
	$id = 0;
	$firstName = "";
	$lastName = "";

	// Check if user already exists in the database
	$checkUserStatement = $conn->prepare("SELECT Login FROM Users WHERE Login = ?");
	$checkUserStatement->bind_param("s", $inData["login"]);

	$checkUserStatement->execute();
	$result = $checkUserStatement->get_result();

	// If user login already exists
	if ($result->num_rows > 0)
	{
		returnWithError("User already exists");
	}

	// If user login doesn't already exist...
	// Attempt to create a new user and add it to the MySql database
	else
	{
		// Prepare to insert new user into MySQL database
		$registerUserStatement = $conn->prepare("INSERT INTO Users (FirstName,LastName,Login,Password) VALUES (?,?,?,?)");
		$registerUserStatement->bind_param("ssss", $firstName, $lastName, $inData['login'], $inData['password']);

		// Execute insertion
		if ($registerUserStatement->execute())
		{
			returnWithError("New user inserted!");
		}
		else
		{
			returnWithError("Oh naur...... it didn't work");
		}
	}

	$checkUserStatement->close();
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
