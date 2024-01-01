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
// response code will be changed if the request goes into any of the processes
http_response_code(404);
$response = new stdClass();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $jsonbody = json_decode(file_get_contents('php://input'));

       if (isset($jsonbody->tourismServiceId)){
            $tourismServiceId = $jsonbody->tourismServiceId;

			$stmt = $db->prepare("SELECT a.qrId
            FROM qrspot a
            LEFT JOIN quizquestion b ON a.qrId = b.qrId
            JOIN tourismservice c ON c.tourismServiceId = a.tourismServiceId
            WHERE c.tourismServiceId = :tourismServiceId
            GROUP BY a.qrId
            HAVING COUNT(b.qrId) < 20");
			$stmt->bindParam(':tourismServiceId', $tourismServiceId);
			$stmt->execute();
			 
			 if ($stmt->rowCount() > 0) {
				 
				$response = $stmt->fetchAll(PDO::FETCH_ASSOC);
				http_response_code(200);
			}
			else{
				http_response_code(400);
			}

        }
		
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}
 

echo json_encode($response);
exit();
?>
