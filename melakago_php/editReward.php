<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");

$hostname = "localhost";
$database = "melakago";
$username = "root";
$password = "@Idris123";

$db = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);

// initial response code
http_response_code(404);
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the rewardId from the query parameters
    $rewardId = isset($_GET['rewardId']) ? $_GET['rewardId'] : null;

    // Get JSON data from the request body
    $json_data = file_get_contents('php://input');
    $modifiedData = json_decode($json_data, true);

    try {
        // Update data for the specified reward in the database
        $stmt = $db->prepare("UPDATE reward SET rewardName = ?, rewardPoint = ?, rewardCode = ?, tnc = ? WHERE rewardId = ?");
        $stmt->execute([$modifiedData['rewardName'], $modifiedData['rewardPoint'], $modifiedData['rewardCode'], $modifiedData['tnc'], $rewardId]);

        http_response_code(200);
        $response->message = "Reward updated successfully";
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
