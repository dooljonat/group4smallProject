<?php
    error_reporting(-1); // reports all errors
	ini_set("display_errors", "1"); // shows all errors
	ini_set("log_errors", 1); 	
	ini_set("error_log", "/tmp/php-error.log");

	$inData = getRequestInfo();
    
    $tempResult = array();
	$searchResults = array();
    $searchCount = 0;
    
    include "DatabaseConnection.php";

    $stmt = $conn->prepare("select * from contacts where (UserID = ?) and ((? is NULL or ? = '' or FirstName like ?) and (? is null or ? = '' or LastName like ?))");
    $firstName = "%" . $inData["firstName"] . "%";
    $lastName = "%" . $inData["lastName"] . "%";
    $stmt->bind_param("sss", $firstName, $inData["userId"]);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    while($row = $result->fetch_assoc())
    {
        $searchCount++;

        $tempResult = [
            "firstName" => $row["FirstName"],
            "lastName" => $row["LastName"],
            "phone" => $row["Phone"],
            "email" => $row["Email"],
        ];
        
        array_push($searchResults, $tempResult);
    }
    
    if( $searchCount == 0 )
    {
        returnWithError( "No Records Found" );
    }
    else
    {
        returnWithInfo( $searchResults );
    }
    
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
        $retValue = '{"id":0,"results":[],"error":"' . $err . '"}';
        sendResultInfoAsJson( $retValue );
    }

    function returnWithInfo( $searchResults )
    {
        $retValue = '{"results":' . json_encode($searchResults) . ',"error":""}';
        sendResultInfoAsJson( $retValue );
    }
?>
