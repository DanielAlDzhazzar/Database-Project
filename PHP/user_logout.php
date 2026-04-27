<?php
//Simply deletes active cookies and returns to index
    if (isset($_COOKIE['user_cookie']))
    {
        setcookie('user_cookie', '', time() - 3600, '/');
        unset($_COOKIE['user_cookie']);

        header('Location: ../HTML/index.php');
        exit();
    }

    else
    {
        header('Location: ../HTML/index.php');
        exit();
    }
?>