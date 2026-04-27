<?php
//Connects to db
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
//Extracts user cookie
$user_id = $_COOKIE['user_cookie'];
//Prepares a statement
$stmt = $conn->prepare("
    SELECT amount, interestRate, status
    FROM loan
    WHERE customerID = ?
");
//Binds
$stmt->bind_param("i", $user_id);
//Executes
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

$total_loans = 0;
$active = 0;
$paid = 0;
$total_borrowed = 0;
$total_repayable = 0;
//Inputs amount of loans to variables, counting by rows and checks status
while ($row = $result->fetch_assoc()) {
    $total_loans++;

    if ($row['status'] == 'paid') {
        $paid++;
    } else {
        $active++;
    }

    $total_borrowed += $row['amount'];
    $total_repayable += $row['amount'] + ($row['amount'] * $row['interestRate'] / 100);
}
//Prepares an insert statement
$pay_stmt = $conn->prepare("
    SELECT SUM(amountPaid) as total_paid
    FROM payments p
    JOIN loan l ON p.loanID = l.loanID
    WHERE l.customerID = ?
");
//Binds and Executes
$pay_stmt->bind_param("i", $user_id);
$pay_stmt->execute();
$paid_data = $pay_stmt->get_result()->fetch_assoc();
//Assign variables
$total_paid = $paid_data['total_paid'] ?? 0;

$remaining = $total_repayable - $total_paid;

$avg_loan = ($total_loans > 0) ? $total_borrowed / $total_loans : 0;

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Statistics</title>
        <link rel="stylesheet" href="../CSS/profile_styles.css">
    </head>
    <body>
    <div>
        <div class="loan-card">
            <!-- Shows statistics -->
            <p><b>Total Loans:</b> <?php echo $total_loans; ?></p>
            <p><b>Active Loans:</b> <?php echo $active; ?></p>
            <p><b>Paid Loans:</b> <?php echo $paid; ?></p>
            <p><b>Total Borrowed:</b> <?php echo number_format($total_borrowed, 2); ?> €</p>
            <p><b>Total Repayable:</b> <?php echo number_format($total_repayable, 2); ?> €</p>
            <p><b>Total Paid:</b> <?php echo number_format($total_paid, 2); ?> €</p>
            <p><b>Remaining Debt:</b> <?php echo number_format($remaining, 2); ?> €</p>
            <p><b>Average Loan:</b> <?php echo number_format($avg_loan, 2); ?> €</p>
        </div>
    </div>
    </body>
    </html>
<?php
$stmt->close();
$conn->close();
?>