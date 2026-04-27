<?php
//Connects to db
$conn = new mysqli("localhost", "root", "root", "Banksys");
//Connection error handler
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
//Returns to index expired users
if (!isset($_COOKIE['user_cookie'])) {
    header("Location: ../HTML/index.php");
    exit();
}
//Extract userid from cookie
$user_id = $_COOKIE['user_cookie'];
//Prepares a statement
$loans_stmt = $conn->prepare("
    SELECT loanID, amount, createdAt
    FROM loan
    WHERE customerID = ?
    ORDER BY createdAt DESC
");
//Binds and executes
$loans_stmt->bind_param("i", $user_id);
$loans_stmt->execute();
$loans_result = $loans_stmt->get_result();

$loan_id = isset($_POST['loan_id']) ? intval($_POST['loan_id']) : 0;

$selected_loan = null;

if ($loan_id) {
    //Prepares a statement
    $stmt = $conn->prepare("
        SELECT amount, interestRate, termMonths, status
        FROM loan
        WHERE loanID = ? AND customerID = ?
    ");
    //Binds and executes
    $stmt->bind_param("ii", $loan_id, $user_id);
    $stmt->execute();
    $selected_loan = $stmt->get_result()->fetch_assoc();
}
//Prepares
$card_stmt = $conn->prepare("
    SELECT cardID FROM card WHERE customerID = ?
");
//Binds and execute
$card_stmt->bind_param("i", $user_id);
$card_stmt->execute();
$has_card = ($card_stmt->get_result()->num_rows > 0);

$paid = 0;
$total_due = 0;
$left = 0;
$percent = 0;

if ($selected_loan) {
    //Gets total due
    $total_due = $selected_loan['amount'] + ($selected_loan['amount'] * $selected_loan['interestRate'] / 100);
    //Prepares a statement
    $pay_stmt = $conn->prepare("
        SELECT SUM(amountPaid) as paid
        FROM payments
        WHERE loanID = ?
    ");
    //Binds and executes
    $pay_stmt->bind_param("i", $loan_id);
    $pay_stmt->execute();

    $paid_data = $pay_stmt->get_result()->fetch_assoc();
    $paid = $paid_data['paid'] ?? 0;
    //Amount left to pay
    $left = $total_due - $paid;
    //Percentage payed
    $percent = ($total_due > 0) ? ($paid / $total_due) * 100 : 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pay'])) {
    if (!$selected_loan) {
        die("Select loan first");
    }

    if (!$has_card) {
        die("No payment card found");
    }
    //Gets amoubt from user
    $pay_amount = floatval($_POST['pay_amount']);
    //Payment amount cant be lower then 0 or equal to it
    if ($pay_amount <= 0 || $pay_amount > $left) {
        die("Invalid amount");
    }

    $conn->begin_transaction();

    try {
        //Prepares a statement
        $stmt = $conn->prepare("
            INSERT INTO payments (loanID, amountPaid)
            VALUES (?, ?)
        ");
        //Binds and execution
        $stmt->bind_param("id", $loan_id, $pay_amount);
        $stmt->execute();
        $new_paid = $paid + $pay_amount;
        $left = $total_due - $paid;
        //Changes loan status based on payment
        if ($new_paid >= $total_due) {
            //Prepares a statement
            $update = $conn->prepare("
                UPDATE loan SET status = 'paid'
                WHERE loanID = ?
            ");
            //Binds and executes
            $update->bind_param("i", $loan_id);
            $update->execute();
        }

        $conn->commit();
        //Updates a section
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit();

    }
    //Exception handler
    catch (Exception $e) {
        $conn->rollback();
        die("Payment failed");
    }
}

?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Payments</title>
        <link rel="stylesheet" href="../CSS/profile_styles.css">
    </head>
    <body>
            <form method="POST">
                <select name="loan_id" onchange="this.form.submit()" class="form-control">
                    <!-- Lets user select from their loans -->
                    <option value="">Select loan</option>
                    <?php while ($loan = $loans_result->fetch_assoc()): ?>
                        <option value="<?php echo $loan['loanID']; ?>"
                            <?php if ($loan_id == $loan['loanID']) echo "selected"; ?>>
                            €<?php echo $loan['amount']; ?>
                            (<?php echo $loan['createdAt']; ?>)
                        </option>
                    <?php endwhile; ?>
                </select>
            </form>

            <br>

            <?php if (!$selected_loan): ?>
                <p>Select a loan to view details</p>
            <?php else: ?>
                <!-- After loan is selected - displays its data -->
                <p><b>Total Debt:</b> <?php echo $total_due; ?> €</p>
                <p><b>Paid:</b> <?php echo $paid; ?> €</p>
                <p><b>Left:</b> <?php echo $left; ?> €</p>
                <p><b>Progress:</b> <?php echo round($percent, 2); ?>%</p>

                <br>
                <!-- If loan is paid there is no button to pay -->
                <?php if ($selected_loan['status'] == 'paid'): ?>
                    <h3>Loan fully paid</h3>
                <?php elseif (!$has_card): ?>
                    <!-- If user doesn't have a card - links him to create it -->
                    <p>No payment card linked.</p>
                    <!-- Link -->
                    <a href="../Sections/add_card.php" class="btn btn-success">
                        Add Payment Card
                    </a>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="loan_id" value="<?php echo $loan_id; ?>">
                        <label>Pay amount:</label>
                        <!-- This input field contains a limit on payment amount, it must not exceed total loan amount -->
                        <input type="number" name="pay_amount" class="form-control" required max="<?php echo $left; ?>">

                        <br><br>
                        <!-- Submit button -->
                        <button type="submit" name="pay" class="btn btn-success">Pay</button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
    </body>
    </html>
<?php
$conn->close();
?>