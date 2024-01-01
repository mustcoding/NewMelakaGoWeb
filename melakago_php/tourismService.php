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


$jsonbody = json_decode(file_get_contents('php://input'));


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    try {
        if (isset($jsonbody->companyName) &&
			isset($jsonbody->companyAddress) &&
			isset($jsonbody->businessContactNumber) &&
			isset($jsonbody->businessStartHour) &&
			isset($jsonbody->businessEndHour) &&
			isset($jsonbody->businessLocation) &&
			isset($jsonbody->businessDescription)) {
			
            // Check if the email already exists
            $stmt = $db->prepare("SELECT * FROM tourismservice WHERE companyName=:companyName 
			AND businessLocation=:businessLocation");
            $stmt->execute([':companyName' => $jsonbody->companyName,  
			':businessLocation'=>$jsonbody->businessLocation]);
			
			
            if ($stmt->rowCount() > 0) {
                http_response_code(400);
               // Bad Request
                $response->error = "Tourism Service is already registered";
            } else {
                // Insert the new user
                $stmt = $db->prepare("INSERT INTO tourismservice (`companyName`,`companyAddress`,`businessContactNumber`,`email`,
				`businessStartHour`,`businessEndHour`,`faxNumber`,`instagramURL`,`xTwitterURL`,
				`threadURL`,`facebookURL`,`businessLocation`,`starRating`,`businessDescription`,`tsId`, `isDelete`) 
                    VALUES (:companyName, :companyAddress, :businessContactNumber, :email, :businessStartHour, 
					:businessEndHour, :faxNumber, :instagramURL,
					:xTwitterURL, :threadURL, :facebookURL, :businessLocation, :starRating, :businessDescription, :tsId, 0)");

				$stmt->execute([
					':companyName' => $jsonbody->companyName,
					':companyAddress' => $jsonbody->companyAddress,
					':businessContactNumber' => $jsonbody->businessContactNumber,
					':email' => $jsonbody->email,
					':businessStartHour' => $jsonbody->businessStartHour,
					':businessEndHour' => $jsonbody->businessEndHour,
					':faxNumber' => $jsonbody->faxNumber,
					':instagramURL' => $jsonbody->instagramURL,
					':xTwitterURL' => $jsonbody->xTwitterURL,
					':threadURL' => $jsonbody->threadURL,
					':facebookURL' => $jsonbody->facebookURL,
					':businessLocation' => $jsonbody->businessLocation,
					':starRating' => $jsonbody->starRating,
					':businessDescription' => $jsonbody->businessDescription,
					':tsId' => $jsonbody->tsId
				]);

                // Get the last inserted id (qrId)
				$lastInsertId = $db->lastInsertId();
    
				http_response_code(200);
				$response->tourismServiceId = $lastInsertId;
				$response->error = "Successfully registered";
            }
        }
		else if(isset($jsonbody->tourismServiceId)){
			$jsonbody = json_decode(file_get_contents('php://input'));

			
		    $tourismServiceId = $jsonbody->tourismServiceId;
	
			$stmt = $db->prepare("SELECT * FROM tourismservice WHERE tourismServiceId=:tourismServiceId and isDelete=0");
			$stmt->bindParam(':tourismServiceId', $tourismServiceId);
	
			$stmt->execute();
	
			if ($stmt->rowCount() > 0) {
				$userData = $stmt->fetch(PDO::FETCH_ASSOC);
                http_response_code(200);
				$response = [
					'tourismServiceId' => $userData['tourismServiceId'],
					'companyName' => $userData['companyName'],
					'companyAddress' => $userData['companyAddress'],
					'businessContactNumber' => $userData['businessContactNumber'],
					'email' => $userData['email'],
					'businessStartHour' => $userData['businessStartHour'],
					'businessEndHour' => $userData['businessEndHour'],
					'faxNumber' => $userData['faxNumber'],
					'instagramURL' => $userData['instagramURL'],
					'xTwitterURL' => $userData['xTwitterURL'],
					'threadURL' => $userData['threadURL'],
					'facebookURL' => $userData['facebookURL'],
					'businessLocation' => $userData['businessLocation'],
					'starRating' => $userData['starRating'],
					'businessDescription' => $userData['businessDescription'],
					'tsId' => $userData['tsId'],
					'isDelete' => $userData['isDelete']
				];
			} else {
				http_response_code(401);  // Unauthorized
				$response->error = "Tourism Service not exist.";
			}
		
		} 
		else {
            http_response_code(400); // Bad Request
            $response->error = "Missing required parameters";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
		echo json_encode($response);
		exit();
    }
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {

        $stmt = $db->prepare("SELECT a.serviceCategory, b.* 
        FROM tourismservicecode a
        JOIN tourismservice b ON a.tsId = b.tsId
        WHERE b.isDelete = 0");
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
    } catch (Exception $ee) {
        http_response_code(500);
        $response = ['error' => "Error occurred: " . $ee->getMessage()];
    }
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
				
			$tourismServiceId = $jsonbody->tourismServiceId;
			$companyName = $jsonbody->companyName;
			$companyAddress = $jsonbody->companyAddress;
			$businessContactNumber = $jsonbody->businessContactNumber;
			$email = $jsonbody->email;
			$businessStartHour = $jsonbody->businessStartHour;
			$businessEndHour = $jsonbody->businessEndHour;
			$faxNumber = $jsonbody->faxNumber;
			$instagramURL = $jsonbody->instagramURL;
			$xTwitterURL = $jsonbody->xTwitterURL;
			$threadURL = $jsonbody->threadURL;
			$facebookURL = $jsonbody->facebookURL;
			$businessLocation = $jsonbody->businessLocation;
			$starRating = $jsonbody->starRating;
			$businessDescription = $jsonbody->businessDescription;
			
					 

			$stmt = $db->prepare("UPDATE tourismservice SET companyName = :companyName, companyAddress=:companyAddress,
            businessContactNumber=:businessContactNumber, email=:email, businessStartHour=:businessStartHour,
			businessEndHour=:businessEndHour, faxNumber=:faxNumber, instagramURL=:instagramURL,
			xTwitterURL=:xTwitterURL, threadURL=:threadURL, facebookURL=:facebookURL, businessLocation=:businessLocation,
			starRating=:starRating, businessDescription=:businessDescription, isDelete=0 WHERE tourismServiceId = :tourismServiceId");
			
			$stmt->bindParam(':tourismServiceId', $tourismServiceId);
			$stmt->bindParam(':companyName', $companyName);
			$stmt->bindParam(':companyAddress', $companyAddress);
			$stmt->bindParam(':businessContactNumber', $businessContactNumber);
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':businessStartHour', $businessStartHour);
			$stmt->bindParam(':businessEndHour', $businessEndHour);
			$stmt->bindParam(':faxNumber', $faxNumber);
			$stmt->bindParam(':instagramURL', $instagramURL);
			$stmt->bindParam(':xTwitterURL', $xTwitterURL);
			$stmt->bindParam(':threadURL', $threadURL);
			$stmt->bindParam(':facebookURL', $facebookURL);
			$stmt->bindParam(':businessLocation', $businessLocation);
			$stmt->bindParam(':starRating', $starRating);
			$stmt->bindParam(':businessDescription', $businessDescription);
			$stmt->execute();

			if ($stmt->rowCount() == 1) {
				http_response_code(200);
				$response->success = "Tourism Service updated successfully.";
			} else {
				http_response_code(400);  // Bad Request
				$response->error = "Failed to update Tourism Service.";
			}
		} 
		else if(isset($jsonbody->tourismServiceId)){
			$jsonbody = json_decode(file_get_contents('php://input'));

			
		    $tourismServiceId = $jsonbody->tourismServiceId;
	
			$stmt = $db->prepare("UPDATE tourismservice SET isDelete=1 where tourismServiceId=:tourismServiceId");
			$stmt->bindParam(':tourismServiceId', $tourismServiceId);
	
			$stmt->execute();
	
			if ($stmt->rowCount() > 0) {
				$userData = $stmt->fetch(PDO::FETCH_ASSOC);
                http_response_code(200);
				$response->error = "Successfully delete tourism services.";
			} else {
				http_response_code(401);  // Unauthorized
				$response->error = "Tourism Service not exist.";
			}
		
		}
		else 
		{
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
