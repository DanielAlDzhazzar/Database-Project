<?php
//Returns users to index if session is expired
if (!isset($_COOKIE['user_cookie'])) {
    header('Location: ../HTML/index.php');
    exit();
}
//Extraction of iser cookies
$id = $_COOKIE['user_cookie'];
//Connection to DB
$conn = new mysqli("localhost", "root", "root", "Banksys");
//Handles database connection error display
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
//Gets everything from customer table based on id
$stmt = $conn->prepare("SELECT * FROM customer WHERE customerID = ?");
//Displays if preparation failed
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
//Binding and Execution
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();
//No user = no data
if (!$user) {
    die("No data.");
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
<div class="profile-container">

    <div class="header">
        <a href="../HTML/index.php" class="btn btn-back">Back</a>
        <h1>Profile</h1>
    </div>

    <div style="display: flex;">
        <!-- Links to various operations -->
        <nav class="nav">
            <a href="?section=profile_display">Profile</a>
            <a href="?section=loan_display">Loan</a>
            <a href="?section=payments">Payments</a>
            <a href="?section=statistics">Statistics</a>
            <a href="?section=delete_account">Delete Account</a>
        </nav>

        <div class="content" id="content">
            <!-- Uses sections system to not update page everytime user clicks on needed option -->
            <?php
            if (isset($_GET['section'])) {
                $section = basename($_GET['section']);
                $sectionFile = "../Sections/$section.php";
                //Searches for a section, if successfully - includes it
                if (file_exists($sectionFile)) {
                    include $sectionFile;
                } else {
                    //If not found - shows which section is not found
                    echo "Section not found.";
                }
            }
            else {
                //Standard section - registration
                include '../Sections/profile_display.php';
            }
            ?>
        </div>
    </div>
</div>
</body>
</html>