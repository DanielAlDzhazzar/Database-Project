<?php
//Gets user input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['pass'];
    //Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email");
    }
    //Connection
    $conn = new mysqli("localhost", "root", "root", "Banksys");
    //Error message if connection failed
    if ($conn->connect_error) {
        die("Error: " . $conn->connect_error);
    }

    $conn->begin_transaction();

    try {
        //Safe statements to reduce probability of injections
        $stmt = $conn->prepare("SELECT customerID, password FROM customer WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        //Error if user not found
        if (!$user) {
            throw new Exception("User not found");
        }
        //Error if password is not matching
        if ($user['password'] !== $password) {
            throw new Exception("Wrong password");
        }

        $conn->commit();
        //Sets user cookie on successful login
        setcookie('user_cookie', $user['customerID'], time() + 6000, "/");
        //Returns to index
        header('Location: ../HTML/index.php');
        exit();

    }
    //Catches exceptions and shows them
    catch (Exception $e) {
        $conn->rollback();
        die($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../CSS/reg_styles.css">
</head>
<body>
<div class="form-container">
    <!-- Collects data from user for future insertion to db -->
    <form class="card" action="" method="POST">
        <input type="text" class="form-control" name="email" id="email" placeholder="Email">
        <input type="password" class="form-control" name="pass" id="pass" placeholder="Password">
        <button class="btn btn-success" type="submit">Login</button>
    </form>
</div>
</body>
