<?php
//Connects to DB
$conn = new mysqli("localhost", "root", "root", "Banksys");
//Connection error handling
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
//Collection of user provided data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pnum = trim($_POST['pnum']);
    $passport = strtoupper(trim($_POST['passport']));
    $password = ($_POST['pass']);
    //Checks for said data
    if (strlen($name) < 3 || strlen($name) > 50) {
        die("Name must be between 3 and 50 characters");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if (strlen($email) < 5 || strlen($email) > 50) {
        die("Email length invalid");
    }

    if (!preg_match('/^[0-9]{10,13}$/', $pnum)) {
        die("Phone must be 10–13 digits");
    }

    if (!preg_match('/^[A-Z]{2}[0-9]{6}$/', $passport)) {
        die("Passport must be like AB123456");
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,25}$/', $password)) {
        die("Password must be 8–25 chars, include upper, lower and number");
    }

    //Password hashing
    $password = password_hash($password, PASSWORD_DEFAULT);

    $conn->begin_transaction();

    try {
        //Safe statements to reduce probability of injections
        $stmt = $conn->prepare("
            INSERT INTO customer (name, email, pnum, password, passport)
            VALUES (?, ?, ?, ?, ?)
        ");
        //If failed shows error message
        if (!$stmt) {
            throw new Exception("Prepare failed");
        }
        //Binding of parameters
        $stmt->bind_param("ssiss", $name, $email, $pnum, $password, $passport);
        //Shows error if operation was unable to execute
        if (!$stmt->execute()) {
            throw new Exception("Insert failed: " . $stmt->error);
        }
        //Gets user id to later use in cookies(user id is automatic)
        $user_id = $conn->insert_id;

        $conn->commit();
        //Cookies set for index navigation panel and any future references of customer currently logged in
        setcookie('user_cookie', $user_id, time() + 6000, "/", "", false, true);
        //Returns to index
        header('Location: ../HTML/index.php');
        exit();

    }
    //Catches exceptions and shows them
    catch (Exception $e) {
        $conn->rollback();
        die("Registration failed: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="../CSS/reg_styles.css">
</head>
<body>
<!-- Collects data from user for future insertion to db -->
<form class="card" action="" method="post">
    <input type="text" class="form-control" name="name" placeholder="Name" required>
    <input type="number" class="form-control" name="pnum" placeholder="Phone number" required>
    <input type="text" class="form-control" name="email" placeholder="Email" required>
    <input type="text" class="form-control" name="passport" placeholder="Passport (AB123456)" required>
    <input type="password" class="form-control" name="pass" placeholder="Password" required>

    <button class="btn btn-success" type="submit">Register</button>
    <!-- Link in case if user already has an account -->
    <p>Have an account already? <a href="?section=login">Enter</a></p>
</form>
</body>
</html>