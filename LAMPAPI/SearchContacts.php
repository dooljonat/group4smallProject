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
    
    $currentUserId = $inData["currentUserId"];
    $search = $inData["search"];
    $wildSearch = '%' . $search . '%';

    $words = array_filter(explode(' ', $search));
    
    $query = "SELECT * FROM Contacts WHERE (UserID = ?) AND (FirstName LIKE ?
        OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?)";
    
    if (count($words) > 1) {
        $query .= " OR (FirstName LIKE ? AND LastName LIKE ?)";
    }

    $stmt = $conn->prepare($query);
    
    if (count($words) > 1) {
        $word1 = '%' . $words[0] . '%';
        $word2 = '%' . $words[1] . '%';
        $stmt->bind_param("sssssss", $currentUserId, $wildSearch, $wildSearch, $wildSearch, $wildSearch, $word1, $word2);
    } else {
        $stmt->bind_param("sssss", $currentUserId, $wildSearch, $wildSearch, $wildSearch, $wildSearch);
    }

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
            "id" => $row["ID"],
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
