<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "melakago";
$username = "root";
$password = "@Idris123";

$db = new PDO ("mysql:host=$hostname;dbname=$database",$username,$password);
// initial response code
// response code will be changed if the request goes into any of the process

http_response_code(404);
$response = new stdClass();

{
	$jsonbody = json_decode(file_get_contents('php://input'));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    try {
		
		if ( isset($jsonbody -> tourismServiceId))
	    {
			 
			$tourismServiceId = $jsonbody->tourismServiceId;
			
			$stmt = $db->prepare("INSERT INTO qrspot (`tourismServiceId`) 
                    VALUES (:tourismServiceId)");
			
			$stmt->execute([
                    ':tourismServiceId' => $jsonbody->tourismServiceId
                ]);

            if ($stmt->rowCount() > 0) {
                    // Get the last inserted id (qrId)
                    $lastInsertId = $db->lastInsertId();
    
                    http_response_code(200);
                    $response->qrId = $lastInsertId;
                    $response->message = "Successfully registered";
            } else {
                    http_response_code(500);
                    $response->error = "Failed to register QR spot";
            }
			
		}
		
    } catch (Exception $ee) {
        http_response_code(500);
        $response = ['error' => "Error occurred: " . $ee->getMessage()];
    }
   
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
   
}
else if ($_SERVER["REQUEST_METHOD"] == "PUT")
{
	
   
}

echo json_encode($response);
exit();
?>
