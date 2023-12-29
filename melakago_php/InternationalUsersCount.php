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


if ($_SERVER["REQUEST_METHOD"] == "POST") {
     try {
		if (isset($_POST['roleId'])){
			
			$roleId = $_POST['roleId'];
			$stmt = $db->prepare("SELECT COUNT(*) as count FROM appuser WHERE roleId = :roleId AND country != 'Malaysia'");
			
			$stmt->bindParam(':roleId', $roleId);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				http_response_code(200);
				$userData = $stmt->fetch(PDO::FETCH_ASSOC);
				$response = ['count' => $userData['count']];
			
			} else {
				http_response_code(404); // Not Found
				$response['error'] = "No tourism services found with roleId = $roleId";
				$response['count'] = 0;
			}
			
		}
		else {
            http_response_code(400); // Bad Request
            $response->error = "Missing required parameters";
        }
		
		  
    } catch (Exception $ee) {
        http_response_code(500);
        $response['error'] = "Error occurred " . $ee->getMessage();
    }
}
else if ($_SERVER["REQUEST_METHOD"] == "GET") {
   
}
else if ($_SERVER["REQUEST_METHOD"] == "PUT")
{
	
   
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');

// Then send the JSON response
echo json_encode($response);
exit();
?>
