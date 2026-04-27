<?php
//Connection
$conn = new mysqli("localhost", "root", "root", "Banksys");
//Error connection displayer
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
//Session expire user transportation to index
if (!isset($_COOKIE['user_cookie'])) {
    header('Location: ../HTML/index.php');
    exit();
}
//Extraction of userid from cookies
$user_id = $_COOKIE['user_cookie'];
//Statement preparation
$stmt = $conn->prepare("
    SELECT amount, interestRate, termMonths, status, createdAt
    FROM loan
    WHERE customerID = ?
    ORDER BY loanID DESC
");
//Preparation failed display
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
//Bind
$stmt->bind_param("i", $user_id);
//Execute with error checker
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Loan Details</title>
        <link rel="stylesheet" href="../CSS/profile_styles.css">
    </head>
    <body>
        <div>
            <!-- Tells of no loans found -->
            <?php if ($result->num_rows == 0): ?>
                <p>No loans found</p>
            <?php else: ?>
                <?php while ($loan = $result->fetch_assoc()): ?>
                    <?php
                    //Calculates total
                    $total = $loan['amount'] + ($loan['amount'] * $loan['interestRate'] / 100);
                    ?>
                    <div class="loan-card">
                        <!-- Displays loans -->
                        <p><b>Amount:</b> <?php echo $loan['amount']; ?> €</p>
                        <p><b>Interest Rate:</b> <?php echo $loan['interestRate']; ?>%</p>
                        <p><b>Total Repayable:</b> <?php echo number_format($total, 2); ?> €</p>
                        <p><b>Term:</b> <?php echo $loan['termMonths']; ?> months</p>
                        <p><b>Status:</b> <?php echo $loan['status']; ?></p>
                        <p><b>Created:</b> <?php echo $loan['createdAt']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </body>
    </html>
<?php
$stmt->close();
$conn->close();
?>