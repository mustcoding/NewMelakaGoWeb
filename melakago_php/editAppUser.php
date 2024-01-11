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
			try 
			{
				if (isset($jsonbody->appUserId) && isset($jsonbody->nickName)&& isset($jsonbody->phoneNumber)&& isset($jsonbody->email)) {
					 $appUserId = $jsonbody->appUserId;
					 $nickName = $jsonbody->nickName;
					 $phoneNumber = $jsonbody->phoneNumber;
					 $email = $jsonbody->email;


					$stmt = $db->prepare("UPDATE appuser SET nickName = :nickName, phoneNumber=:phoneNumber, email=:email WHERE appUserId = :appUserId");
					$stmt->bindParam(':appUserId', $appUserId);
					$stmt->bindParam(':nickName', $nickName);
					$stmt->bindParam(':phoneNumber', $phoneNumber);
					$stmt->bindParam(':email', $email);
					$stmt->execute();

					if ($stmt->rowCount() == 1) {
						http_response_code(200);
						$response->success = "Profile updated successfully.";
					} else {
						http_response_code(400);  // Bad Request
						$response->error = "Failed to update profile.";
					}
				} 
				else if(isset($jsonbody->appUserId) && isset($jsonbody->password)){
					$appUserId = $jsonbody->appUserId;
					$password = $jsonbody->password;

					$stmt = $db->prepare("UPDATE appuser SET password = :password WHERE appUserId = :appUserId");
					$stmt->bindParam(':appUserId', $appUserId);
					$stmt->bindParam(':password', $password);

					$stmt->execute();

					if ($stmt->rowCount() == 1) {
						http_response_code(200);
						$response->success = "Password updated successfully.";
					} else {
						http_response_code(400);  // Bad Request
						$response->error = "Failed to update profile.";
					}

				}
				else if (isset($jsonbody->email) && isset($jsonbody->password)) {
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
				
			} catch (Exception $e) {
				http_response_code(500);
				$response->error = "Error occurred " . $e->getMessage();
			}
             
}
else if ($_SERVER["REQUEST_METHOD"] == "POST")
{
			try 
			{
				if (isset($jsonbody->appUserId)) {
					 $appUserId = $jsonbody->appUserId;


					$stmt = $db->prepare("SELECT * FROM appuser WHERE appUserId = :appUserId");
					$stmt->bindParam(':appUserId', $appUserId);
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
						$response->error = "Failed to update profile.";
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
