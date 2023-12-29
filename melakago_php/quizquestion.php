<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Credentials: true");
// Your other PHP code...


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
		
		 if (isset($jsonbody->qrId) && !isset($jsonbody->questionText) && !isset($jsonbody->answerOption1) && !isset($jsonbody->answerOption2)
            && !isset($jsonbody->answerOption3) && !isset($jsonbody->answerOption4) && !isset($jsonbody->correctAnswer) && !isset($jsonbody->point))
	    {
			 
			$qrId = $jsonbody->qrId;
			$stmt = $db->prepare("SELECT Count(*) as count FROM quizquestion where qrId = :qrId");
			$stmt->bindParam(':qrId', $qrId);
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
			http_response_code(200);
		}
		else if (isset($jsonbody->questionText)&& isset($jsonbody->answerOption1)&& isset($jsonbody->answerOption2)&& isset($jsonbody->answerOption3)&& 
		isset($jsonbody->answerOption4)&& isset($jsonbody->correctAnswer)&& isset($jsonbody->point)&& isset($jsonbody->qrId)) {
			
			$stmt = $db->prepare("INSERT INTO quizquestion (`questionText`,`answerOption1`,`answerOption2`,`answerOption3`,
				`answerOption4`,`correctAnswer`,`point`,`qrId`) 
                    VALUES (:questionText, :answerOption1, :answerOption2, :answerOption3, :answerOption4, 
					:correctAnswer, :point, :qrId)");

                $stmt->execute([
                    ':questionText' => $jsonbody->questionText,
                    ':answerOption1' => $jsonbody->answerOption1,
                    ':answerOption2' => $jsonbody->answerOption2,
                    ':answerOption3' => $jsonbody->answerOption3,
                    ':answerOption4' => $jsonbody->answerOption4,
                    ':correctAnswer' => $jsonbody->correctAnswer,
                    ':point' => $jsonbody->point,
                    ':qrId' => $jsonbody->qrId
                ]);

                http_response_code(200);
				$response->error = "Successfully registered";
		}
		
	
		
    } catch (Exception $ee) {
        http_response_code(500);
        $response = ['error' => "Error occurred: " . $ee->getMessage()];
    }
   
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    try {

        $stmt = $db->prepare("SELECT Count(*) as count FROM quizquestion");
        $stmt->execute();
		if ($stmt->rowCount() > 0) {
				http_response_code(200);
				$userData = $stmt->fetch(PDO::FETCH_ASSOC);
				$response = ['count' => $userData['count']];
			
			} else {
				http_response_code(404); // Not Found
				$response['count'] = 0;
			}
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
		if (isset($jsonbody->questionText) &&
			isset($jsonbody->answerOption1) &&
			isset($jsonbody->answerOption2) &&
			isset($jsonbody->answerOption3) &&
			isset($jsonbody->answerOption4) &&
			isset($jsonbody->correctAnswer) &&
			isset($jsonbody->point) && 
			isset($jsonbody->qrId) && 
			isset($jsonbody->isDelete)) {
				
		    $questionId = $jsonbody->questionId;
			$questionText = $jsonbody->questionText;
			$answerOption1 = $jsonbody->answerOption1;
			$answerOption2 = $jsonbody->answerOption2;
			$answerOption3 = $jsonbody->answerOption3;
			$answerOption4 = $jsonbody->answerOption4;
			$correctAnswer = $jsonbody->correctAnswer;
			$point = $jsonbody->point;
			$qrId = $jsonbody->qrId;
			$isDelete = $jsonbody->isDelete;
		
					 

			$stmt = $db->prepare("UPDATE quizquestion SET questionText = :questionText, answerOption1=:answerOption1,
            answerOption2=:answerOption2, answerOption3=:answerOption3, answerOption4=:answerOption4,
			correctAnswer=:correctAnswer, point=:point, qrId=:qrId,
			isDelete=:isDelete WHERE questionId = :questionId");
			
			$stmt->bindParam(':questionId', $questionId);
			$stmt->bindParam(':questionText', $questionText);
			$stmt->bindParam(':answerOption1', $answerOption1);
			$stmt->bindParam(':answerOption2', $answerOption2);
			$stmt->bindParam(':answerOption3', $answerOption3);
			$stmt->bindParam(':answerOption4', $answerOption4);
			$stmt->bindParam(':correctAnswer', $correctAnswer);
			$stmt->bindParam(':point', $point);
			$stmt->bindParam(':qrId', $qrId);
			$stmt->bindParam(':isDelete', $isDelete);
			$stmt->execute();

			if ($stmt->rowCount() == 1) {
				http_response_code(200);
				$response->success = "Quiz Question updated successfully.";
			} else {
				http_response_code(400);  // Bad Request
				$response->error = "Failed to update Quiz Question.";
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
echo json_encode($response);
exit();
?>

