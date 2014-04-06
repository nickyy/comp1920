<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: March 18, 2014
 * File: logout.php
 * File description: Comp 1920 Assignment 2 file used for logging out, including destroying session variables.
 */
 

 ob_start();

 // Start new, or resume any existing, session
session_start();1
?>

<!DOCTYPE html>
<html>
<head>
    <title>Comp 1920 Assignment 2, Nicky Yuen</title>
    <meta charset="utf-8">
    <link href="style.css" rel="stylesheet" media="all">
</head>
<body class="site">
<header class="header">
    <h1>Comp 1920 Assignment 2</h1>
</header>
    <nav>
        <ul class="links">
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
    <section class="main">
        <?php

		// Display thank you message.
        echo "<p>Thank you for visiting. <a href='login.php'>Log in again.</a></p>";
		
        // Destroy any session variable
        $_SESSION = array();

        // Destroy any session cookies
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();
        ?>
    </section>
    <footer class="footer">
        <p>Comp 1920 - Assignment 2</p>
        <p>By Nicky Yuen</p>
        <p>Copyright &copy; 2014</p>
    </footer>
</body>
</html>