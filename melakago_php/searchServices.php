<?php

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

        // Check if the email and password are set in the POST request
        if (isset($_POST['keyword'])) {
            $keyword = $_POST['keyword'];
          
            $stmt = $db->prepare("SELECT a.serviceCategory, b.* 
            FROM tourismservicecode a
            JOIN tourismservice b ON a.tsId = b.tsId
            WHERE b.isDelete = 0 AND LOWER(b.companyName) LIKE LOWER(:keyword)
            OR LOWER(a.serviceCategory) LIKE LOWER(:keyword)");

            $keyword = '%' . strtolower($keyword) . '%';  // Add '%' to the lowercase keyword
            $stmt->bindParam(':keyword', $keyword);
            $stmt->execute();
                    

            if ($stmt->rowCount() > 0) {

                $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
                http_response_code(200);
				
            } else {
                http_response_code(401);  // Unauthorized
                $response->error = "Invalid username or password.";
            }
        } else {
            http_response_code(400);  // Bad Request
            $response->error = "keyword required.";
        }
    } catch (Exception $ee) {
        http_response_code(500);
        $response->error = "Error occurred " . $ee->getMessage();
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {

    // Handle GET requests

} else if ($_SERVER["REQUEST_METHOD"] == "PUT") {

    // Handle PUT requests

}

// Before sending the JSON response, set the content type header
header('Content-Type: application/json');

// Then send the JSON response
echo json_encode($response);
exit();
?>
