<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: March 18, 2014
 * File: login.php
 * File description: Comp 1920 Assignment 2 file used for login page. 
 * This file handles the logging activities, including setting the login cookie, 
 * session, keeping track of the number of login attempts, and processing excessive attempts.
 */
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
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>
    <section class="main">
        <?php
		// Import the various functions used for the different operations from functions.php
        require_once("functions.php");
		
        // Display login form. If cookie exists, then populate login form with user name
        if (isset($_COOKIE['username'])){
            showForm($_COOKIE['username']);
        }
        // Show a blank form if no cookie stored
        else {
            showForm("");
        }

        // If the form is being submitted
        if ($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Store database info. NOT SECURE AT ALL!
//            $username = "root";
//            $password = "nyuen123";
//            $database = "comp1920";
            $username = "yuenwork_dbuser";
            $password = "yuenwork_dbuser123";
            $database = "yuenwork_dB";

            // Establish connection to 'comp1920' database
            $dB = mysqli_connect('localhost', $username, $password) or die(mysqli_connect_error());
            $dB->select_db($database) or die("<p class='error'>Unable to access database.</p>");

            // MySql query to get all the usernames from table
            $usernameQuery = "SELECT username FROM personalinfo";

            // Get the results from running query
            $result = $dB->query($usernameQuery) or die($dB->error);

            // Define array to store the results of query
            $usernameArr = array();
            while ($row = $result->fetch_assoc()) {
                $usernameArr[] = $row['username'];
            }

            // If username or password field is empty, then show an error and halt execution
            if (empty($_POST['u']) || empty($_POST['p'])){
                echo "<p class='error'>Username and password cannot be blank.</p>";
                die();
            }
            // Username and password fields are not empty
            else if (!empty($_POST['u']) && !empty($_POST['p'])){
                $u = $_POST['u'];
                $p = $_POST['p'];

                // Validate login credentials
                if (checkLogin($dB, $usernameArr, $u, $p)){
                    // Successful login, redirect to login.php
                    session_start();

                    // Set cookie for username (5 minutes)
                    setcookie('username', $u, time()+300);

                    // Set session variables
                    $_SESSION['loggedIn'] = true;
                    $_SESSION['user'] = $u;
                    $_SESSION['pass'] = $p;

                    // Reset the login counter (this is initially set in the checkLogin() function after a successful login
                    unset($_SESSION['attempts']);

                    // After setting all session variables, redirect to the success.php page
                    header("Location: success.php");
                    exit();
                }
                // Unsuccessful login
                else {
                    // Calculate the remaining number of attempts available
                    $loginAttemptsRemaining = 3 - $_SESSION['attempts'];
                    echo "<p class='error'>You have ".$loginAttemptsRemaining." login attempts remaining. Use them wisely.</p>";

                    // If three unsuccessful login attempts were made
                    if ($_SESSION['attempts'] == 3){
                        
						// Clear session variables
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
                        <script>
                            // Disable the login button via Javascript
                            document.getElementById("loginButton").disabled = true;
                        </script>
                        <?php
                        // Three strikes error message, and then redirect user after 3 seconds
                        echo "<h2 class='error'>Three strikes!</h2>";
                        header("Refresh:3; url=uhoh.html");
                        exit();
                    }
                }
            }
        }
        ?>
    </section>
    <footer class="footer">
        <p>Comp 1920 - Assignment 2</p>
        <p>By Nicky Yuen</p>
        <p>Copyright &copy; 2014</p>
    </footer>
</body>
</html>
