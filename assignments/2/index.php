<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: March 18, 2014
 * File: index.php
 * File description: Comp 1920 Assignment 2 Home page. This page shows some assignment goals and 
 * gives the user the option to either login to the system or return to the Home page.
 */

// Import the various functions used for the different operations from functions.php
require_once("functions.php");

// Start new, or resume any existing, session
session_start();
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
            <?php
            // Decide which links to show depending on the login (or session) status
            // If a session already exists, then show different links as the user is already logged in
            if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
                ?>
                <li><a href="files.php">Process Image</a></li>
                <li><a href="logout.php">Logout</a></li>
                <?php
            }
            // Otherwise if no session exists, then show just the login link
            else {
                ?>
                <li><a href="login.php">Login</a></li>
                <?php
            }
            ?>

        </ul>
    </nav>
    <section class="main">
        <?php
        // Customise the welcome message by checking if a user is already logged in with a valid session
        if (isset($_SESSION['loggedIn']) && $_SESSION['loggedIn']){
            // Come here only after successful login
            $u = $_SESSION['user'];
            $p = $_SESSION['pass'];

            // Store database info. NOT SECURE AT ALL!
//            $username = "root";
//            $password = "nyuen123";
//            $database = "comp1920";
            $username = "yuenwork_dbuser";
            $password = "yuenwork_dbuser123";
            $database = "yuenwork_dB";

            // Establish connection to 'comp1920' database
            $dB = mysqli_connect('localhost', $username, $password) or die(mysqli_connect_error());;
            $dB->select_db($database) or die("<p class='error'>Unable to access database.</p>");

            // Get current IP address
            $ip = $_SERVER['REMOTE_ADDR'];

            // DON'T NEED TO UPDATE THE NUMBER OF VISITS IF ALREADY LOGGED IN
			// setVisits($dB, $u);

            // Get user details and display a customised message with login count and IP address
            $userDetails = getUserInfo($dB, $u);
            echo "<p>Welcome back, <span style='color:green'>".trim($userDetails['firstname'])." ".trim($userDetails['lastname'])."</span>!</p>";
            echo "<p>This is visit number ".$userDetails['numberofvisits']." for you!</p>";
            echo "<p>Last time you came from IP address ".$userDetails['ipaddress']." and this time you're coming from IP address $ip.</p>";
        }
        else {
            // No existing users logged in and no current sessions, so show generic login message
            echo "<p>Welcome!</p>";
        }
        ?>
		<!-- Assignment 2 goals -->
        <p>The deliverables and features for this assignment include the following:</p>
            <ul>
                <li>Log in (up to 3 attempts) using valid credentials</li>
				<li>Compare the username and password entered into the form, via MySQL search</li>
				<li>Report the user's full name, number of visits, and the IP address of the current and previous visits</li>
				<li>Set a cookie for a valid login, used in future logins, and populating login page as necessary</li>
                <li>Upload up to 10 files: up to 9 text (.txt) and exactly 1 jpeg (.jpg or .JPG)</li>
				<li>Detect whether .txt or .jpg files are missing</li>
				<li>Parse through all text files and capture email addresses (assuming the email string is the ONLY content in the file)</li>
                <li>Resize the uploaded jpeg as a thumbnail, ensuring the same dimensions</li>
                <li>Email the thumbnail file to any email address contained in the text files</li>
				<li>Inform user of various operations via descriptive messages for various errors or successes</li>
                <li>Logout (including purging of session variables)</li>
				<li>Re-login without needing to input user name (only within first 5 minutes of initial login)</li>
				<li>Keep track of the number of incorrect logins and disable login and redirect the user to an error page if excessive login attempts are made</li>
            </ul>
    </section>
    <footer class="footer">
        <p>Comp 1920 - Assignment 2</p>
        <p>By Nicky Yuen</p>
        <p>Copyright &copy; 2014</p>
    </footer>
</body>
</html>