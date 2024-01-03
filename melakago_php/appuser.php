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

// Retrieve JSON data from the request
$json_data = file_get_contents("php://input");

// Decode JSON data
$data = json_decode($json_data, true);

http_response_code(404);
$response = new stdClass();


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
    if (isset($_POST['firstName']) &&
		isset($_POST['lastName']) &&
		isset($_POST['nickName']) &&
		isset($_POST['dateOfBirth']) &&
		isset($_POST['phoneNumber']) &&
		isset($_POST['email']) &&
		isset($_POST['password']) &&
		isset($_POST['accessStatus'])&&
		isset($_POST['country']) &&
		isset($_POST['roleId']) &&
		isset($_POST['points'])) {

			$firstName = $_POST['firstName'];
			$lastName = $_POST['lastName'];
			$nickName = $_POST['nickName'];
			$dateOfBirth = $_POST['dateOfBirth'];
			$phoneNumber = $_POST['phoneNumber'];
			$email = $_POST['email'];
			$password = $_POST['password'];
			$accessStatus = $_POST['accessStatus'];
			$country = $_POST['country'];
			$roleId = $_POST['roleId'];
			$points = $_POST['points'];
			
            // Check if the email already exists
            $stmt = $db->prepare("SELECT email FROM appuser WHERE email=:email");
            $stmt->bindParam(':email', $email);
			$stmt->execute();
			
            if ($stmt->rowCount() > 0) {
//$response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
               // Bad Request
                $response->error = "Email is already registered";
            } else {
                // Insert the new user
                $stmt = $db->prepare("INSERT INTO appuser (`firstName`,`lastName`,`nickName`,`dateOfBirth`,`phoneNumber`,`email`,`password`,`accessStatus`,`country`,`roleId`,`points`) 
                    VALUES (:firstName, :lastName, :nickName, :dateOfBirth, :phoneNumber, :email, :password, :accessStatus, :country, :roleId, :points)");
					$stmt->bindParam(':firstName', $firstName);
					$stmt->bindParam(':lastName', $lastName);
					$stmt->bindParam(':nickName', $nickName);
					$stmt->bindParam(':dateOfBirth', $dateOfBirth);
					$stmt->bindParam(':phoneNumber', $phoneNumber);
					$stmt->bindParam(':email', $email);
					$stmt->bindParam(':password', $password);
					$stmt->bindParam(':accessStatus', $accessStatus);
					$stmt->bindParam(':country', $country);
					$stmt->bindParam(':roleId', $roleId);
					$stmt->bindParam(':points', $points);

                $stmt->execute();

                http_response_code(200);
				$response->error = "Successfully registered";
            }
        } else {
            http_response_code(400); // Bad Request
            $response->error = "Missing required parameters";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}
else if($_SERVER["REQUEST_METHOD"]== "GET"){
	
	try{
		$stmt = $db->prepare("SELECT * FROM appuser WHERE email=email and password=password");
		$stmt->execute();
		$response = $stmt->fetchAll(PDO::FETCH_ASSOC);
		http_response_code(200);
	
	}catch(Exception $ee){
		http_response_code(500);
		//$response['error'] = "Error occured". $ee->getMessage();
		$response->error = "Error occured ". $ee->getMessage();
	}
}
else if ($_SERVER["REQUEST_METHOD"] == "PUT")
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
