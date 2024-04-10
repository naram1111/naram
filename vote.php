<?php
session_start();
include_once "config.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array(
        'secret' => '6LejK4IpAAAAAKXslF0P_Hm2yFdzWMSHIrT9dbY2',
        'response' => $recaptcha_response
    );
    $options = array(
        'http' => array (
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result_array = json_decode($result, true);
    if (!$result_array['success']) {
        echo json_encode(array("success" => false, "message" => "reCAPTCHA verification failed"));
        exit();
    }
    if (!isset($_SESSION["user_id"]) || !is_numeric($_SESSION["user_id"])) {
        echo json_encode(array("success" => false, "message" => "Invalid user ID"));
        exit();
    }
    $picture_id = filter_input(INPUT_POST, 'picture_id', FILTER_VALIDATE_INT);
    if ($picture_id === false || $picture_id === null) {
        echo json_encode(array("success" => false, "message" => "Invalid picture ID"));
        exit();
    }
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $db_username, $db_password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $conn->prepare("INSERT INTO likes (user_id, picture_id) VALUES (:user_id, :picture_id)");
        $stmt->bindParam(':user_id', $_SESSION["user_id"], PDO::PARAM_INT);
        $stmt->bindParam(':picture_id', $picture_id, PDO::PARAM_INT);
        $stmt->execute();
        echo json_encode(array("success" => true));
    } catch(PDOException $e) {
        echo json_encode(array("success" => false, "message" => "Database error: " . $e->getMessage()));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Invalid request method"));
}
?>
