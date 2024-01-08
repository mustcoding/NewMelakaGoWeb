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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Get the rewardId from the query parameters
    $rewardId = isset($_GET['rewardId']) ? $_GET['rewardId'] : null;

    try {
        // Fetch data for the specified reward from the database
        $stmt = $db->prepare("SELECT * FROM reward WHERE rewardId = ?");
        $stmt->execute([$rewardId]);
        $reward = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the reward exists
        if ($reward) {
            http_response_code(200);
            $response->reward = $reward;
        } else {
            http_response_code(404);
            $response->error = "Reward not found";
        }
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
