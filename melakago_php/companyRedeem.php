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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    try {

        $tourismServiceId = $jsonbody->tourismServiceId;

        $stmt = $db->prepare("SELECT
        CONCAT(a.firstName, ' ', a.lastName) AS fullName,
        b.rewardCode,
        b.rewardName,
        c.dateRedeem,
        c.pointRedeem
        FROM
            companyreward c
        JOIN
            appuser a ON c.appUserId = a.appUserId
        JOIN
            reward b ON c.rewardId = b.rewardId
        WHERE
        c.tourismServiceId = :tourismServiceId");
        $stmt->bindParam(':tourismServiceId', $tourismServiceId);
        $stmt->execute();
        $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
        http_response_code(200);
    } catch (Exception $ee) {
        http_response_code(500);
        $response = ['error' => "Error occurred: " . $ee->getMessage()];
    }

}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
   
}
else if ($_SERVER["REQUEST_METHOD"] == "PUT")
{
	
}

echo json_encode($response);
exit();
?>
