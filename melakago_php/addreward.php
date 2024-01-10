<?php
file_put_contents('log.txt', print_r(file_get_contents('php://input'), true));

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "melakago";
$username = "root";
$password = "@Idris123";

$db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
if (!$db) {
    http_response_code(500);
    $response->error = "Database connection failed";
    exit();
}

// initial response code
// response code will be changed if the request goes into any of the process
http_response_code(404);
$response = new stdClass();

$jsonbody = json_decode(file_get_contents('php://input'));

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if ($jsonbody &&
            isset($jsonbody->rewardName) &&
            isset($jsonbody->rewardPoint) &&
            isset($jsonbody->rewardCode) &&
            isset($jsonbody->tnc) &&
            isset($jsonbody->tourismServiceId)) {

            $rewardName = $jsonbody->rewardName;
            $rewardPoint = $jsonbody->rewardPoint;  
            $rewardCode = $jsonbody->rewardCode;
            $tnc = $jsonbody->tnc; 
            $tourismServiceId = $jsonbody->tourismServiceId; 

            // Perform database insertion
            $stmt = $db->prepare("INSERT INTO reward (rewardname, rewardpoint, rewardcode, tnc, tourismServiceId) 
                                   VALUES  (:rewardName, :rewardPoint, :rewardCode, :tnc, :tourismServiceId)");

            $stmt->execute([
                'rewardName' => $rewardName,
                'rewardPoint' => $rewardPoint,
                'rewardCode' => $rewardCode,
                'tnc' => $tnc,
                'tourismServiceId' => $tourismServiceId,
            ]);

            http_response_code(200);
            $response->success = "Reward added successfully";
        } else {
            http_response_code(400); // Bad Request
            $response->error = "Invalid JSON or missing required parameters";
        }
    } catch (Exception $ee) {
        error_log("Error occurred: " . $ee->getMessage(), 0);
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
