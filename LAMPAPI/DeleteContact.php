<?php
	error_reporting(-1); // reports all errors
	ini_set("display_errors", "1"); // shows all errors
	ini_set("log_errors", 1);
	ini_set("error_log", "/tmp/php-error.log");

	include "DatabaseConnection.php";

	$inData = getRequestInfo();

	$currentUserId = $inData["currentUserId"];
	$contactId = $inData["contactId"];

    $stmt = $conn->prepare("delete from Contacts where (UserID = ?) and (ID = ?)");
    $stmt->bind_param("ss", $currentUserId, $contactId);
    $stmt->execute();
    $stmt->close();

    returnWithSuccess();

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
        http_response_code(400);
		sendResultInfoAsJson( $retValue );
	}

    function returnWithSuccess()
	{
        $retValue = '{"error":""}';
        http_response_code(200);
		sendResultInfoAsJson( $retValue );
	}
?>
