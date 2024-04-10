<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['verify_captcha'])) {
        $captcha_response = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';
        if (empty($captcha_response)) {
            echo json_encode(array("success" => false, "message" => "Please complete the CAPTCHA verification"));
            exit();
        }

        $secret_key = 'your_secret_key';

        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => "6LejK4IpAAAAAKXslF0P_Hm2yFdzWMSHIrT9dbY2",
            'response' => $captcha_response
        );
        $options = array(
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false
        );
        $ch = curl_init($url);
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);

        $captcha_result = json_decode($response);
        if (!$captcha_result->success) {
            echo json_encode(array("success" => false, "message" => "CAPTCHA verification failed. Please try again."));
            exit();
        }

        echo json_encode(array("success" => true, "message" => "CAPTCHA verification successful"));
    } else {
        echo json_encode(array("success" => false, "message" => "Invalid request"));
    }
} else {
    echo json_encode(array("success" => false, "message" => "Invalid request method"));
}
?>
