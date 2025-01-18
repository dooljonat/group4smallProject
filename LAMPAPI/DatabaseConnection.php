<?php
// Connecting to mysql server from PHP ...
// - localhost = servername
// - TheBeast  = username
// - WeLoveCOP4331 = password
// - COP4331   = database
$conn = new mysqli("localhost", "TheBeast", "WeLoveCOP4331", "COP4331"); 	
if( $conn->connect_error )
{
    returnWithError( $conn->connect_error );
}
?>