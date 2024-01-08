<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "melakago";
$username = "root";
$password = "@Idris123";

$db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);

// Initial response code
// Response code will be changed if the request goes into any of the processes
http_response_code(404);
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "DELETE") {
    try {
        // Get rewardId from the request parameters
        $requestData = json_decode(file_get_contents("php://input"), true);
        $rewardId = $requestData['rewardId'] ?? null;

        // Check if rewardId is provided
        if ($rewardId !== null) {
            // Use prepared statements to prevent SQL injection
            $stmt = $db->prepare("DELETE FROM reward WHERE rewardId = ?");
            $stmt->execute([$rewardId]);

            // Check if the deletion was successful
            if ($stmt->rowCount() > 0) {
                http_response_code(200);
                $response->success = true;
                $response->message = "Reward deleted successfully";
            } else {
                http_response_code(404);
                $response->success = false;
                $response->message = "Reward not found or deletion failed";
            }
        } else {
            http_response_code(400);
            $response->success = false;
            $response->message = "RewardId not provided";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->success = false;
        $response->message = "Error occurred " . $ee->getMessage();
    }
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
