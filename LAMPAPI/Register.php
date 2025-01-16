
<?php

	$inData = getRequestInfo();
	
	$id = 0;
	$firstName = "";
	$lastName = "";

	// Connecting to mysql server from PHP ...
	// - localhost = servername
	// - TheBeast  = username
	// - WeLoveCOP4331 = password
	// - COP4331   = database
	$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); 
    // NOTE: following this tutorial https://www.geeksforgeeks.org/creating-a-registration-and-login-system-with-php-and-mysql/

	if( $conn->connect_error )
	{
		returnWithError( $conn->connect_error );
	}
	else
	{
        // Check if user already exists in the database
        $checkUserStatement = $conn->prepare("SELECT 'Login' FROM 'Users' WHERE 'Login' = ?");
        //
    }
	// 	$stmt = $conn->prepare("SELECT ID,firstName,lastName FROM Users WHERE Login=? AND Password =?");
	// 	$stmt->bind_param("ss", $inData["login"], $inData["password"]);
	// 	$stmt->execute();
	// 	$result = $stmt->get_result();

	// 	if( $row = $result->fetch_assoc()  )
	// 	{
	// 		returnWithInfo( $row['firstName'], $row['lastName'], $row['ID'] );
	// 	}
	// 	else
	// 	{
	// 		returnWithError("No Records Found");
	// 	}

	// 	$stmt->close();
	// 	$conn->close();
	// }
	
	// function getRequestInfo()
	// {
	// 	return json_decode(file_get_contents('php://input'), true);
	// }

	// function sendResultInfoAsJson( $obj )
	// {
	// 	header('Content-type: application/json');
	// 	echo $obj;
	// }
	
	// function returnWithError( $err )
	// {
	// 	$retValue = '{"id":0,"firstName":"","lastName":"","error":"' . $err . '"}';
	// 	sendResultInfoAsJson( $retValue );
	// }
	
	// function returnWithInfo( $firstName, $lastName, $id )
	// {
	// 	$retValue = '{"id":' . $id . ',"firstName":"' . $firstName . '","lastName":"' . $lastName . '","error":""}';
	// 	sendResultInfoAsJson( $retValue );
	// }
	
?>
