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
		if (isset($_POST['tsId'])){
			
			$tsId = $_POST['tsId'];
			$stmt = $db->prepare("SELECT COUNT(*) as count FROM tourismservice WHERE tsId = :tsId and isDelete=0");
			
			$stmt->bindParam(':tsId', $tsId);
			$stmt->execute();
			if ($stmt->rowCount() > 0) {
				http_response_code(200);
				$userData = $stmt->fetch(PDO::FETCH_ASSOC);
				$response = ['count' => $userData['count']];
			
			} else {
				http_response_code(404); // Not Found
				$response['error'] = "No tourism services found with tsId = $tsId";
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
	try 
	{
		if (isset($jsonbody->companyName) &&
			isset($jsonbody->companyAddress) &&
			isset($jsonbody->businessContactNumber) &&
			isset($jsonbody->businessStartHour) &&
			isset($jsonbody->businessEndHour) &&
			isset($jsonbody->businessLocation) &&
			isset($jsonbody->businessDescription)) {
				
			$appUserId = $jsonbody->appUserId;
			$accessStatus = $jsonbody->accessStatus;
					 
			error_log("appUserId received: " . $appUserId);

			$stmt = $db->prepare("UPDATE tourismservice SET companyName = :companyName, companyAddress=:companyAddress,
            businessContactNumber=:businessContactNumber, email=:email, businessStartHour=:businessStartHour,
			businessEndHour=:businessEndHour, faxNumber=:faxNumber, instagram=:instagram,
			xTwitter=:xTwitter, thread=:thread, facebook=:facebook, businessLocation=:businessLocation,
			starRating=:starRating, businessDescription=:businessDescription, tsId=:tsId, isDelete=:isDelete WHERE tourismServiceId = :tourismServiceId");
			
			$stmt->bindParam(':tourismServiceId', $tourismServiceId);
			$stmt->bindParam(':companyName', $companyName);
			$stmt->bindParam(':companyAddress', $companyAddress);
			$stmt->bindParam(':businessContactNumber', $businessContactNumber);
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':businessStartHour', $businessStartHour);
			$stmt->bindParam(':businessEndHour', $businessEndHour);
			$stmt->bindParam(':faxNumber', $faxNumber);
			$stmt->bindParam(':instagram', $instagram);
			$stmt->bindParam(':xTwitter', $xTwitter);
			$stmt->bindParam(':thread', $thread);
			$stmt->bindParam(':facebook', $facebook);
			$stmt->bindParam(':businessLocation', $businessLocation);
			$stmt->bindParam(':starRating', $starRating);
			$stmt->bindParam(':businessDescription', $businessDescription);
			$stmt->bindParam(':tsId', $tsId);
			$stmt->bindParam(':isDelete', $isDelete);
			$stmt->execute();

			if ($stmt->rowCount() == 1) {
				http_response_code(200);
				$response->success = "AccessStatus updated successfully.";
			} else {
				http_response_code(400);  // Bad Request
				$response->error = "Failed to update accessStatus.";
			}
		} else {
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

// Then send the JSON response
echo json_encode($response);
exit();
?>
