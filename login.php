<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    if (empty($phone_number) || empty($password)) {
        echo "Please fill in all required fields.";
        exit();
    }

    $db_host = 'localhost';
    $db_username = 'your_username';
    $db_password = 'your_password';
    $db_name = 'your_database';

    $conn = new mysqli($db_host, $db_username, $db_password, $db_name);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $check_query = "SELECT * FROM users WHERE phone_number = ?";
    $stmt_check = $conn->prepare($check_query);
    $stmt_check->bind_param("s", $phone_number);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 1) {
        $row = $result_check->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['phone_number'] = $phone_number;
            $_SESSION['logged_in'] = true;
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Incorrect phone number or password. Please try again.";
        }
    } else {
        echo "Incorrect phone number or password. Please try again.";
    }

    $stmt_check->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
