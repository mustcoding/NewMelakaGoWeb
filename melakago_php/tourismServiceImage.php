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

{
    $jsonbody = json_decode(file_get_contents('php://input'));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($jsonbody->image)) {
            // Check if the image is an array
            if (is_array($jsonbody->image)) {
                // Loop through each image in the array and insert into the database
                foreach ($jsonbody->image as $base64Image) {
                    $stmt = $db->prepare("INSERT INTO tourismserviceimage (`image`,`tourismServiceId`) 
                        VALUES (:image, :tourismServiceId)");

                    $stmt->execute([
                        ':image' => $base64Image,
                        ':tourismServiceId' => $jsonbody->tourismServiceId
                    ]);
                }

                http_response_code(200);
                $response->error = "Successfully inserting data";
            } else {
                http_response_code(400);  // Bad Request
                $response->error = "Invalid image data. Expecting an array.";
            }
        }
        else if(isset($jsonbody->tourismServiceId)){

            $tourismServiceId = $jsonbody->tourismServiceId;

            $stmt = $db->prepare("SELECT image FROM tourismserviceimage WHERE tourismServiceId=:tourismServiceId");
            $stmt->bindParam(':tourismServiceId', $tourismServiceId);
            $stmt->execute();
            if ($stmt->rowCount() > 0) {
                $userData = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all rows
                http_response_code(200);
                $response->images = $userData;  // Use a plural name to indicate multiple images
            } else {
                http_response_code(401);  // Unauthorized
                $response->error = "Invalid username or password.";
            }
    
        } 
        else {
            http_response_code(400);  // Bad Request
            $response->error = "No Images Selected.";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
}

echo json_encode($response);
exit();
?>