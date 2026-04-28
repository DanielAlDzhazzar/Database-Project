<?php
//Connection to db
$conn = new mysqli("localhost", "root", "root", "Banksys");
//Connection error displayer
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
//Returns expired users to index
if (!isset($_COOKIE['user_cookie'])) {
    header('Location: ../HTML/index.php');
    exit();
}
//Extracts user cookies
$user_id = $_COOKIE['user_cookie'];
//Prepares a statement
$stmt = $conn->prepare("SELECT name, pnum, email, passport FROM customer WHERE customerID = ?");
if (!$stmt) {
    die("Prepare failed");
}
//Binds and Executes
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
//No user = no data
if (!$user) {
    die("User not found");
}

$stmt->close();
$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="../CSS/profile_styles.css">
</head>
<body>
<!-- Displays user data -->
<div class="loan-card">
    <p><b>Name:</b> <?php echo $user['name']; ?></p>
    <p><b>Phone number:</b> <?php echo $user['pnum']; ?></p>
    <p><b>Email:</b> <?php echo $user['email']; ?></p>
    <p><b>Passport:</b> <?php echo $user['passport']; ?></p>
</div>
</body>
</html>