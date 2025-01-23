<?php
	error_reporting(-1); // reports all errors
	ini_set("display_errors", "1"); // shows all errors
	ini_set("log_errors", 1);
	ini_set("error_log", "/tmp/php-error.log");

	include "DatabaseConnection.php";

	$inData = getRequestInfo();

	$userId = $inData["userId"];
	$id = $inData["id"];

    $stmt = $conn->prepare("delete from Contacts where (UserID = ?) and (ID = ?)");
    $stmt->bind_param("ss", $userId, $id);
    $stmt->execute();
    $stmt->close();

    returnWithError("");

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
	
?>
