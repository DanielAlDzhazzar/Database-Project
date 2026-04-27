<?php
//Returns user to main if session expired
if (!isset($_COOKIE['user_cookie'])) {
    header('Location: ../HTML/index.php');
    exit();
}
//Extraction of id from cookies
$id = $_COOKIE['user_cookie'];
//Connection to db
$conn = new mysqli("localhost", "root", "root", "Banksys");
//Connection error handler
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}

$conn->begin_transaction();

try {
    //Statement preparation
    $stmt = $conn->prepare("DELETE FROM `customer` WHERE customerID = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    //Parameter binding
    $stmt->bind_param("i", $id);
    //Execution and error checker
    if (!$stmt->execute()) {
        throw new Exception("Delete failed: " . $stmt->error);
    }

    $stmt->close();
    $conn->commit();
    //Destroys cookies
    setcookie('user_cookie', '', time() - 3600, "/");
    //Returns to main
    header('Location: ../HTML/index.php');
    exit();

}
//Exception handler
catch (Exception $e) {
    $conn->rollback();
    die("Error: " . $e->getMessage());
}

?>