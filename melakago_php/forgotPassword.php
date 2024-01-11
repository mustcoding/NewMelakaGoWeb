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



if ($_SERVER["REQUEST_METHOD"] == "PUT")
{
			
				if (isset($jsonbody->email) && isset($jsonbody->password)) {
					try {
						$email = $jsonbody->email;
						$password = $jsonbody->password;
				
						$stmt = $db->prepare("UPDATE appuser SET password = :password WHERE email = :email");
						$stmt->bindParam(':email', $email);
						$stmt->bindParam(':password', $password);
				
						$stmt->execute();
				
						if ($stmt->rowCount() == 1) {
							http_response_code(200);
							$response->success = "Password updated successfully.";
						} else {
							http_response_code(400);
							$response->error = "Failed to update password.";
						}
					} catch (Exception $e) {
						http_response_code(500);
						$response->error = "Error occurred " . $e->getMessage();
					}
				}
				else {
					http_response_code(400);  // Bad Request
					$response->error = "Invalid JSON format. appUserId and accessStatus are required.";
				}
				

             
}
else if ($_SERVER["REQUEST_METHOD"] == "POST")
{
		
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
