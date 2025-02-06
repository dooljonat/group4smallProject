
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
		// Get variables from $inData
		$newFirstName = $inData['first'];
		$newLastName = $inData['last'];
		$newLogin = $inData['login'];
		$newPassword = $inData['password'];

		// Validate input
		if ($newFirstName == null || $newFirstName == "" || 
			 $newLastName == null || $newLastName == "" ||
			 $newLogin == null || $newLogin == "" ||
			 $newPassword == null || $newPassword == "")
		{
			returnWithError("All forms must be filled.");
		}

		// If input is valid...
		else
		{
			// Prepare to insert new user into MySQL database
			$registerUserStatement = $conn->prepare("INSERT INTO Users (FirstName,LastName,Login,Password) VALUES (?,?,?,?)");
			$registerUserStatement->bind_param("ssss", $inData['first'], $inData['last'], $inData['login'], $inData['password']);

			// Execute insertion
			if ($registerUserStatement->execute())
			{
				// Get newly added user from DB
				$fetchUserStatement = $conn->prepare("SELECT ID,firstName,lastName FROM Users WHERE Login=?");
				$fetchUserStatement->bind_param("s", $inData["login"]);
				$fetchUserStatement->execute();
				$result = $fetchUserStatement->get_result();

				// If returned user has data (NOTE: it should)
				if( $row = $result->fetch_assoc()  )
				{
					returnWithInfo( $row['firstName'], $row['lastName'], $row['ID'] );
				}
				else
				{
					// TODO:
					// Change or remove these error messages in the future
					returnWithError("User that was just added isn't appearing in our database... weird");
				}
			}
			else
			{
				// TODO:
				// Change or remove these error messages in the future
				returnWithError("Oh no...... it didn't work");
			}
			$registerUserStatement->close();
		}
	}

	// Close statements and end connection
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
