<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: March 15, 2014
 * File: success.php
 * File description: Comp 1920 Assignment 2 file. Landing page after successful login.
 */


// Import the various functions used for the different operations from functions.php
require_once("functions.php");
ob_start();
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
            <li><a href="files.php">Process Image</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <section class="main">
        <?php
        session_start();

        // Come here only after first time successful login with a new session
        $u = $_SESSION['user'];
        $p = $_SESSION['pass'];

        // Store database info. NOT SECURE AT ALL!
//        $username = "root";
//        $password = "nyuen123";
//        $database = "comp1920";
        $username = "yuenwork_dbuser";
        $password = "yuenwork_dbuser123";
        $database = "yuenwork_dB";

        // Establish connection to 'comp1920' database
        $dB = mysqli_connect('localhost', $username, $password) or die(mysqli_connect_error());
        $dB->select_db($database) or die("<p class='error'>Unable to access database.</p>");

        // Get current IP address
        $ip = $_SERVER['REMOTE_ADDR'];

//        echo "<p>".session_id()."</p>";
//        echo "<p>".$_SESSION['id']."</p>";

        // Don't need to update the number of visits if already logged in
        setVisits($dB, $u);

        // Login successful, so get user details
        $userDetails = getUserInfo($dB, $u);

        echo "<p>Welcome, <span style='color:green'>".trim($userDetails['firstname'])." ".trim($userDetails['lastname'])."</span>, this is visit number ".$userDetails['numberofvisits']." for you!</p>";
        echo "<p>Last time you came from IP address ".$userDetails['ipaddress']." and this time you're coming from IP address $ip.</p>";

        // Update IP address in database
        setIPAddress($dB, $u, $ip);
        ?>

    </section>
    <footer class="footer">
        <p>Comp 1920 - Assignment 2</p>
        <p>By Nicky Yuen</p>
        <p>Copyright &copy; 2014</p>
    </footer>
</body>
</html>