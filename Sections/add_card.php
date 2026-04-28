<?php
//Connection to db
$conn = new mysqli("localhost", "root", "root", "Banksys");
//Connection error displayer
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//Returns to index if there is no cookies
if (!isset($_COOKIE['user_cookie'])) {
    header('Location: ../HTML/index.php');
    exit();
}
//Extracts user id from cookie
$user_id = $_COOKIE['user_cookie'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $cardHolder = trim($_POST['cardHolder']);
    //Deletes spaces
    $cardNumber = preg_replace('/\s+/', '', $_POST['cardNumber']);
    $expiryInput = trim($_POST['expiry']);
    $cvv = trim($_POST['cvv']);
    //Validation
    if (!preg_match('/^.{3,50}$/', $cardHolder)) {
        die("Invalid card holder name");
    }

    if (!preg_match('/^[0-9]{16}$/', $cardNumber)) {
        die("Card number must be 16 digits");
    }

    if (!preg_match('/^(0[1-9]|1[0-2])\/[0-9]{2}$/', $expiryInput)) {
        die("Expiry must be MM/YY");
    }

    if (!preg_match('/^[0-9]{3,4}$/', $cvv)) {
        die("CVV must be 3–4 digits");
    }
    //Date handler(user only puts month and year, day is added here)
    $parts = explode("/", $expiryInput);
    $month = $parts[0];
    $year = "20" . $parts[1];
    $expiryDate = $year . "-" . $month . "-01";

    //Data hashing
    $cardNumber = password_hash($cardNumber, PASSWORD_DEFAULT);
    $cvv = password_hash($cvv, PASSWORD_DEFAULT);

    $conn->begin_transaction();

    try {
        //Prepares a statement
        $stmt = $conn->prepare("
            INSERT INTO card (customerID, cardHolder, cardNumber, expiry, cvv)
            VALUES (?, ?, ?, ?, ?)
        ");
        //Preparation error message displayer
        if (!$stmt) {
            throw new Exception("Prepare failed");
        }
        //Binding
        $stmt->bind_param(
            "issss",
            $user_id,
            $cardHolder,
            $cardNumber,
            $expiryDate,
            $cvv
        );
        //Execution with error display
        if (!$stmt->execute()) {
            throw new Exception("Insert failed: " . $stmt->error);
        }

        $conn->commit();
        //Returns to profile
        header("Location: ../HTML/profile.php");
        exit();

    }
    //Exception handler
    catch (Exception $e) {
        $conn->rollback();
        die("Card creation failed: " . $e->getMessage());
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Card</title>
    <link rel="stylesheet" href="../CSS/reg_styles.css">
</head>
<body>
<div class="container">
    <div class="form-container">
        <form class="card" action="" method="post">
            <!-- Fields to put data in -->
            <p><a href="../HTML/profile.php" class="btn btn-back">Back</a></p>
            <input type="text" class="form-control" name="cardHolder" placeholder="Card Holder" required>
            <input type="text" class="form-control" name="cardNumber" placeholder="Card Number" required>
            <input type="text" class="form-control" name="expiry" required>
            <input type="password" class="form-control" name="cvv" placeholder="CVV" required>

            <button class="btn btn-success" type="submit">Add Card</button>
    </div>
</div>
</form>
</body>
</html>