<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BankSys</title>
    <link rel="stylesheet" href="../CSS/styles.css">
</head>
<body>

    <header>
        <h1 class="company-name">BankSys</h1>
        <!-- Standard navigation panel -->
        <nav class="navbar">
            <ul>
                <li><a href="../HTML/index.php">Home</a></li>
                <!-- System provides different links based on cookies -->
                <?php
                if(!isset($_COOKIE['user_cookie']) || $_COOKIE['user_cookie'] == ''):?>
                    <li><a href="../HTML/reg_log.php">Registration</a></li>
                <?php else: ?>
                    <li><a href="../HTML/loan.php">Take a Loan</a></li>
                    <li><a href="../HTML/profile.php">Profile</a></li>
                    <li><a href="../PHP/user_logout.php">Logout</a></li>
                <?php endif; ?>
                <li><a href="../HTML/info.php">About us</a></li>
                <li><a href="../HTML/contacts.php">Contact data</a></li>
            </ul>
        </nav>
    </header>

<footer>
    <p class="slogan">Best bank in the world</p>
</footer>

</body>
</html>
