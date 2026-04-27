<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <link rel="stylesheet" href="../CSS/reg_styles.css">
</head>
<body>
<div class="container">
    <div class="form-container">
        <header>
            <h1>Registration | Login</h1>
        </header>
        <p>Please fill fields bellow.</p>
        <a href="../HTML/index.php" class="btn btn-back">Back</a>
        <br><br>
        <!-- Uses sections system to not update page everytime user clicks on needed option -->
        <?php
        if (isset($_GET['section'])) {
            $section = $_GET['section'];
            $sectionFile = "../Sections/$section.php";
            //Searches for a section, if successfully - includes it
            if (file_exists($sectionFile)) {
                include $sectionFile;
            } else {
                //If not found - shows which section is not found
                echo "Section '$section' not found.";
            }
        }
        else{
            //Standard section - registration
            include "../Sections/registration.php";
        }
        ?>
    </div>
</div>
</body>
</html>
