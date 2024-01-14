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

if ($_SERVER["REQUEST_METHOD"] == "POST")
{
			try 
			{
				if (isset($jsonbody->appUserId)&&isset($jsonbody->password) ) {
					 $appUserId = $jsonbody->appUserId;
                     $password = $jsonbody->password;

					$stmt = $db->prepare("SELECT * FROM appuser WHERE appUserId = :appUserId and password=:password");
					$stmt->bindParam(':appUserId', $appUserId);
                    $stmt->bindParam(':password', $password);
					$stmt->execute();

					if ($stmt->rowCount() == 1) {
						$userData = $stmt->fetch(PDO::FETCH_ASSOC);
                        http_response_code(200);
                        $response = [
                            'appUserId' => $userData['appUserId'],
                            'firstName' => $userData['firstName'],
                            'lastName' => $userData['lastName'],
                            'nickName' => $userData['nickName'],
                            'dateOfBirth' => $userData['dateOfBirth'],
                            'phoneNumber' => $userData['phoneNumber'],
                            'email' => $userData['email'],
                            'password' => $userData['password'],
                            'accessStatus' => $userData['accessStatus'],
                            'country' => $userData['country'],
                            'roleId' => $userData['roleId'], // Include roleId in the response
                            'points' => $userData['points'],
                        ];
						
					} else {
						http_response_code(400);  // Bad Request
						$response->error = "user not exist.";
					}
				} 
				else {
					http_response_code(400);  // Bad Request
					$response->error = "Invalid JSON format. appUserId and accessStatus are required.";
				}
			} catch (Exception $e) {
				http_response_code(500);
				$response->error = "Error occurred " . $e->getMessage();
			}
   
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
