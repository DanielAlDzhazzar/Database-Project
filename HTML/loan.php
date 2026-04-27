<?php
//If user doesn't have cookies - sends them to index(in case of session expiry)
if (!isset($_COOKIE['user_cookie'])) {
    header('Location: ../HTML/index.php');
    exit();
}
//Gets userid from cookie
$user_id = $_COOKIE['user_cookie'];
//Connects to db
$conn = new mysqli("localhost", "root", "root", "Banksys");
//Connection error displayer
if ($conn->connect_error) {
    die("Error: " . $conn->connect_error);
}
$amount = 0;
$interest = 0;
$terms = [];
$selected_term = null;
$total = 0;
$loan_limit_reached = false;
//Calculates interest based on amount
function calculateInterest($amount) {
    if ($amount < 10000) return 5;
    if ($amount < 50000) return 3;
    return 2;
}
//Calculates terms based on amount
function getTerms($amount) {
    if ($amount < 10000) return [6];
    if ($amount < 50000) return [6, 12];
    return [6, 12, 18, 24];
}

//Gets amount from user
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = floatval($_POST['amount']);

    //Cant take less than a 100 in loan
    if ($amount <= 1000) {
        die("Invalid loan amount");
    }

    //Prepares a statement for safety
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total
        FROM loan
        WHERE customerID = ? AND status = 'active'
    ");
    //Binding and execution
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    //Checks if client has 3 active credits, as it would be a limit
    if ($data['total'] >= 3) {
        $loan_limit_reached = true;
    }

    //Calculation
    $interest = calculateInterest($amount);
    $terms = getTerms($amount);

    //Gets user selected term
    if (isset($_POST['sign']) && isset($_POST['agree'])) {
        $term = intval($_POST['term']);
        //Error if user selected wrong term
        if (!in_array($term, $terms)) {
            die("Invalid term selected");
        }

        $conn->begin_transaction();

        try {
            //Safe statements to reduce probability of injections
            $stmt = $conn->prepare("
                INSERT INTO loan (customerID, amount, interestRate, termMonths, status)
                VALUES (?, ?, ?, ?, 'active')
            ");
            //Displays if preparations failed
            if (!$stmt) {
                throw new Exception("Prepare failed");
            }
            //Binding
            $stmt->bind_param("idii", $user_id, $amount, $interest, $term);
            //Insertion error displayer
            if (!$stmt->execute()) {
                throw new Exception("Insert failed");
            }

            $conn->commit();
            //Return to index
            header("Location: ../HTML/index.php");
            exit();

        }
        //Exception display
        catch (Exception $e) {
            $conn->rollback();
            die("Loan creation failed: " . $e->getMessage());
        }
    }
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Loan</title>
    <link rel="stylesheet" href="../CSS/reg_styles.css">
</head>

<body>
<div class="container">
    <div class="form-container">
        <header>
            <a href="../HTML/index.php" class="btn btn-back">Back</a>
            <h1>Loan Application</h1>
        </header>
        <!-- Customer inputs desired amount -->
        <form class="card" action="" method="post">
            <label>Enter loan amount (€):</label>
            <input type="number" name="amount" class="form-control" required
                   value="<?php echo htmlspecialchars($amount); ?>">

            <br>
            <!-- Message if customer has 3 active loans already -->
            <?php if ($loan_limit_reached): ?>
                <p>You already have 3 active loans. You cannot take a new loan.</p>
            <!-- Displays interest rate -->
            <?php elseif ($amount > 1000): ?>
                <p>Interest rate: <?php echo $interest; ?>%</p>
                <!-- Selection of terms from provided -->
                <label>Select term:</label>
                <select name="term" class="form-control" required>
                    <?php foreach ($terms as $t): ?>
                        <option value="<?php echo $t; ?>">
                            <?php echo $t; ?> months
                        </option>
                    <?php endforeach; ?>
                </select>

                <br><br>
                <!-- Submit button -->
                <button class="btn btn-success" type="submit" name="sign" value="1" id="signBtn">Sign Loan</button>

            <?php else: ?>
                <!-- Initially shows calculate button -->
                <button class="btn btn-success" type="submit">
                    Calculate
                </button>
            <?php endif; ?>
        </form>
    </div>
</div>

</body>
</html>