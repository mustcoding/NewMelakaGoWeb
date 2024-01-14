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
   
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {

        $stmt = $db->prepare("SELECT
        a.companyName,
        COUNT(DISTINCT b.qrId) AS totalqrSpot,
        COUNT(c.questionId) AS totalQuestion
    FROM
        tourismservice a
    JOIN
        qrspot b ON b.tourismServiceId = a.tourismServiceId
    JOIN
        quizquestion c ON b.qrId = c.qrId
        
    where c.isDelete=0

    GROUP BY
        a.companyName
        ");
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
	
}

echo json_encode($response);
exit();
?>
