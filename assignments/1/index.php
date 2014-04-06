<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: February 26, 2014
 * File: index.php
 * File description: Comp 1920 Assignment 1 file. The goal of this lab is to use the ideas and code from
 * the first two lectures and create a PHP script that uses dates, operators, strings,
 * conditionals, and loops.
 */

ob_start();

/******************************************************
 * Create the HTML form
 *****************************************************/
?>
<!DOCTYPE html>
<html>
<head>
    <title>Comp 1920 Assignment 1 by Nicky Yuen</title>
    <meta charset="utf-8">
    <style>
        .formField {
            padding: 10px 0;
        }

        .formField label {
            display: inline-block;
            width: 100px;
        }
        .formField input {
            display: inline-block;
        }
        #submitButton {
            margin-left: 100px;
        }
    </style>
</head>
<body>

<?php
/******************************************
 * Function to display the HTML form
 * @param $user
 * @param $remember
 ******************************************/
function drawForm($user, $remember){
    echo "<form method='POST' action='index.php' name='assignment1_form'>";
        echo "<div class='formField'>";
            echo "<label for='u'>Username:</label>";
            echo "<input type='text' id='u' name='u' value='$user'>";
        echo "</div>";

        echo "<div class='formField'>";
            echo "<label for='p'>Password:</label>";
            echo "<input type='password' id='p' name='p'>";
        echo "</div>";

        echo "<div class='formField'>";
            echo "<label for='remember'>Remember me:</label>";
            echo "<input type='checkbox' id='remember' name='remember' $remember>";
        echo "</div>";

        echo "<input id='submitButton' type='submit' value='Submit Query'>";
    echo "</form>";
    echo "</body>";
    echo "</html>";
}

/******************************************
 * Function to validate the user name and password formats
 * @param $u
 * @param $p
 * @return bool
 ******************************************/
function hasValidFormat($u, $p){
    $pattern = "/[<>]/";
    if (preg_match($pattern, $u)){
        echo "<p style='color:red;font-style:italic'>".htmlentities($u)." contains illegal characters. Try again.</p>";
        return false;
    }
    else if (preg_match($pattern, $p)){
        echo "<p style='color:red;font-style:italic'>Password contains illegal characters. Try again.</p>";
        return false;
    }
    else {
        return true;
    }
}

/******************************************
 * Function to log all invalid login attempts with IP address
 * @param $u
 * @param $p
 ******************************************/
function logInvalidLogin($u, $p){
    // Get IP address
    $ip = $_SERVER['REMOTE_ADDR'];

    // Create file handler resource
    $fh = @fopen("invalid-logins.txt", "a") or exit("<p>File error</p>");

    // Write the input username, password, and IP address to file
    fwrite($fh, $u.",".$p.",".$ip.PHP_EOL);

    // Close the file handler
    fclose($fh);

    // Output an error message
    echo "<p style='font-style:italic;color:red'>Invalid user/login. This has been logged along with your IP address $ip.</p>";
}

/******************************************
 * Function to print out navigation links
 *****************************************/
function printLinks(){
    echo "<ul>";
    echo "<li><a href='browse-books-in-store.php'>Browse books in store</a></li>";
    echo "<li><a href='book-analytics.php'>Book analytics</a></li>";
    echo "<li><a href='logout.php'>Log out</a></li>";
    echo "</ul>";
}

/******************************************
 * Function to check that username and password
 * match valid credentials
 * @param $u
 * @param $p
 *****************************************/
function isValid($u, $p){
    $validLogin = false;
    if (hasValidFormat($u, $p)){
        $validCredentials = file("passwords.txt", FILE_IGNORE_NEW_LINES);
        foreach($validCredentials as $validCredential){
            $validPair = preg_split("/,/", $validCredential);
            // Check if username and password are valid
            if ((trim($u) == $validPair[0]) && (trim($p) == $validPair[1])){
                $validLogin = true;
                setcookie("uname", $u, time()+1200);
                setcookie("loggedintime", time());
                if (isset($_POST['remember'])){
                    setcookie("remember", $u, time()+1200);
                }
                // Print out welcome message and logged in time
                echo "<p>Welcome, ".$u."!</p>";
                if (isset($_COOKIE['loggedintime'])){
                    $loggedInFor = time() - $_COOKIE['loggedintime'];
                    echo "<p>You have been logged in for ".$loggedInFor." seconds</p>";
                }
                // Print out links
                printLinks();

                // End script immediately
                die();
            }
            else {
                $validLogin = false;
            }
        }
        if (!$validLogin){
            logInvalidLogin($u, $p);
        }
    }
}
/*****************************************************************************************************************************
 * Requirement 3: If the user is neither providing cookies nor POST data to the form, then the form must be displayed empty
 ****************************************************************************************************************************/

// http://www.downwithdesign.com/web-development-tutorials/adding-remember-feature-php-login-script/

// Define some time variables for use in cookies
$plus20 = time()+1200;
$past = time()-1;

// "Remember Me" is checked
if (isset($_POST['remember'])){
    // U present, CU absent
    if (isset($_POST['u']) && !isset($_COOKIE['uname'])){
//        echo "a<br>";
        // Create or extend both cookies
        setcookie('uname', $_POST['u'], $plus20);
        setcookie('remember', $_POST['u'], $plus20);

        if (!empty($_POST['u']) && !empty($_POST['p'])){
            isValid($_POST['u'], $_POST['p']);
        }
        else {
            // Create form with U filled in from user input and checkbox checked
            drawForm($_POST['u'], "checked");
        }
    }
    // U present, CU present
    else if (isset($_POST['u']) && isset($_COOKIE['uname'])){
//        echo "b<br>";
        // Create or extend both cookies
        setcookie('uname', $_COOKIE['uname'], $plus20);
        setcookie('remember', $_COOKIE['uname'], $plus20);

        if (!empty($_POST['u']) && !empty($_POST['p'])){
            isValid($_POST['u'], $_POST['p']);
        }
        else {
            // Create form with U filled in from CR and checkbox checked
            drawForm($_COOKIE['remember'], "checked");
        }
    }
    // U absent, CU absent
    else if (!isset($_POST['u']) && !isset($_COOKIE['uname'])){
//        echo "c<br>";
        // Create a blank form with nothing filled in
        drawForm("", "");
    }
    // U absent, CU present
    else if (!isset($_POST['u']) && isset($_COOKIE['uname'])){
//        echo "d<br>";
        // Create or extend both cookies
        setcookie('uname', $_COOKIE['uname'], $plus20);
        setcookie('remember', $_COOKIE['uname'], $plus20);

        // Create form with U filled in from CU and checkbox checked
        drawForm($_COOKIE['uname'], "checked");
    }
}
// "Remember Me" is not checked
else if (!isset($_POST['remember'])){

    // CR present
    if (isset($_COOKIE['remember'])){

        // U absent, CU absent
        if (!isset($_COOKIE['uname']) && !isset($_POST['u'])){
//            echo "e<br>";
            // Create a blank form with nothing filled in
            drawForm("", "");
        }
        // U present, CU absent
        else if (!isset($_COOKIE['uname']) && isset($_POST['u'])){
//            echo "f<br>";
            // Create a blank form with nothing filled in
            drawForm("", "");
        }
        // U absent, CU present
        else if (isset($_COOKIE['uname']) && !isset($_POST['u'])){
//            echo "g<br>";
            // Create form with U filled in from CU and since CR present, then check the checkbox
            drawForm($_COOKIE['uname'], "checked");

            // Create or extend both cookies
            setcookie('uname', $_COOKIE['uname'], $plus20);
            setcookie('remember', $_COOKIE['uname'], $plus20);
        }
        // U present, CU present
        else if (isset($_COOKIE['uname']) && isset($_POST['u'])){
//            echo "h<br>";

            // Set the CU, but delete the CR
            setcookie('uname', $_POST['u'], $plus20);
            setcookie('remember', $_POST['u'], $past);

            if (!empty($_POST['u']) && !empty($_POST['p'])){
                isValid($_POST['u'], $_POST['p']);
            }
            else {
                // Create form with U filled in
                drawForm($_POST['u'], "");
            }
        }
    }
    // "Remember Me" not checked, CR absent
    else if (!isset($_COOKIE['remember'])){
//        echo "x<br>";
        // Delete U and CR cookies
        setcookie('uname', '', $past);
        setcookie('remember', '', $past);

        if (isset($_POST['u'])){
//            echo "y<br>";
            if (!empty($_POST['u']) && !empty($_POST['p'])){
                isValid($_POST['u'], $_POST['p']);
            }
            else {
                // Create form with U filled in
                drawForm("", "");
            }
        }
        else {
//            echo "z<br>";
            drawForm("", "");
        }
    }
}

if (0){
if (!empty($_POST['u']) && !empty($_POST['p'])){
    isValid($_POST['u'], $_POST['p']);
}
}


if (0){
if (isset($_POST['remember'])){
    if (!isset($_POST['u']) && (!isset($_COOKIE['uname']))){
        echo "a<br>";
        drawForm("", "checked");
    }
    else if (!isset($_POST['u']) && isset($_COOKIE['uname'])){
        echo "b<br>";
        drawForm($_COOKIE['uname'], "checked");
        setcookie('uname', $_COOKIE['uname'], time()+1200);
        setcookie('remember', $_COOKIE['uname'], time()+1200);
    }
    else {
        echo "c<br>";
        drawForm($_POST['u'], "checked");
        setcookie('uname', $_POST['u'], time()+1200);
        setcookie('remember', $_POST['u'], time()+1200);
    }
}
// "Remember Me" is not checked
else {
    // "Remember Me" cookie exists
    if (isset($_COOKIE['remember'])){
        echo "d<br>";
        if (!isset($_COOKIE['uname']) && !isset($_POST['u'])){
            drawForm("", "");
        }
        else if (isset($_COOKIE['uname']) && !isset($_POST['u'])){
            echo "e<br>";
            drawForm($_COOKIE['uname'], "");
            setcookie('uname', $_COOKIE['uname'], time()+1200);
        }
        else {
            echo "f<br>";
            drawForm($_POST['u'], "");
            setcookie('uname', $_POST['u'], time()+1200);
        }
    }
    // "Remember Me" cookie does not exist
    else {
        echo "else else<br>";
        @setcookie('uname', '', time()-1);
        @setcookie('remember', '', time()-1);
        if (isset($_POST['u'])){
            drawForm($_POST['u'], "");
        }
        else {
            drawForm("", "");
        }
    }
}
}

if (0) {
// Validate the username and password
if (!empty($_POST['u']) && !empty($_POST['p'])){
    if (hasValidFormat($_POST['u'], $_POST['p'])){
        $validCredentials = file("passwords.txt", FILE_IGNORE_NEW_LINES);
        foreach($validCredentials as $validCredential){
            echo "<pre>";
            print_r($validCredential);
            echo "</pre>";
            $validPair = preg_split("/,/", $validCredential);
            // Check if username and password are valid
            if (trim(($_POST['u']) == $validPair[0]) && (trim($_POST['p']) == $validPair[1])){
                setcookie("uname", $_POST['u'], time()+1200);
                setcookie("loggedintime", time());
                if (isset($_POST['remember'])){
                    setcookie("remember", $_POST['u'], time()+1200);
                }

                echo "<p>Welcome, ".$_POST['u']."!</p>";
                if (isset($_COOKIE['loggedintime'])){
                    $loggedInFor = time() - $_COOKIE['loggedintime'];
                    echo "<p>You have been logged in for ".$loggedInFor." seconds</p>";
                }
                // Print out links
                printLinks();
                die();
            }
            else {
                logInvalidLogin($_POST['u'], $_POST['p']);
                die();
            }
        }
    }
}
}

if (0){
    if (!(isset($_POST['u'])) && !(isset($_COOKIE['uname']))){
        drawForm("", "");
    }
    else if (!(isset($_POST['u'])) && isset($_COOKIE['uname'])){
        setcookie('uname', $_COOKIE['uname'], time()+(60*20));
        drawForm($_COOKIE['uname'], "checked");
    }
    else {
        if (isset($_POST['remember'])){
            if (hasValidFormat($_POST['u'], $_POST['p'])){
                $validCredentials = file("passwords.txt", FILE_IGNORE_NEW_LINES);
                foreach($validCredentials as $validCredential){
                    $validPair = preg_split("/,/", $validCredential);
                    // Check if username and password are valid
                    if (trim(($_POST['u']) == $validPair[0]) && (trim($_POST['p']) == $validPair[1])){
                        setcookie("uname", $_POST['u'], time()+(60*20));
                        setcookie("loggedintime", time());
                        echo "<p>Welcome, ".$_POST['u']."!</p>";
                        if (isset($_COOKIE['loggedintime'])){
                            $loggedInFor = time() - $_COOKIE['loggedintime'];
                            echo "<p>You have been logged in for ".$loggedInFor." seconds</p>";
                        }
                        // Print out links
                        printLinks();
                        die();
                    }
                    else {
                        logInvalidLogin($_POST['u'], $_POST['p']);
                        die();
                    }
                }
            }
        }
        else { // "Remember Me" is not checked
            if (hasValidFormat($_POST['u'], $_POST['p'])){
                $validCredentials = file("passwords.txt", FILE_IGNORE_NEW_LINES);
                foreach($validCredentials as $validCredential){
                    $validPair = preg_split("/,/", $validCredential);
                    // Check if user name is valid
                    if (trim(($_POST['u']) == $validPair[0]) && (trim($_POST['p']) == $validPair[1])){
                        setcookie("uname", $_POST['u'], time()-1);
                        setcookie("loggedintime", time()-1);

    //                    echo "<p>Welcome, ".$_POST['u']."!</p>";
    //
    //                    if (isset($_COOKIE['loggedintime'])){
    //                        $loggedInFor = time() - $_COOKIE['loggedintime'];
    //                        echo "<p>You have been logged in for ".$loggedInFor." seconds</p>";
    //                    }
    //                    // Print out links
    //                    printLinks();
    //                    die();
                    }
                    else {
                        logInvalidLogin($_POST['u'], $_POST['p']);
                        die();
                    }
                }
            }
        }
    }
}


if (0){
// "Remember Me" is checked
if (isset($_POST['remember'])){
    // No user cookie, no user input
    if (!(isset($_COOKIE['uname'])) && !(isset($_POST['u']))){
        drawform('', '');
    }
    // User cookie present, no user input
    else if (isset($_COOKIE['uname']) && !(isset($_POST['u']))){
        drawForm($_COOKIE['uname'], 'checked');
        setcookie('uname', $_COOKIE['uname'], time()+(60*20));
    }
    // User cookie present, user input present
    else {
        drawForm($_POST['u'], 'checked');
        /*****************************************************************************************************************************
         * Requirement 4a: If the username or password contain the less-than character < or the greater-than character > give an
         * error message and do NOTHING else
         ****************************************************************************************************************************/
        if (hasValidFormat($_POST['u'], $_POST['p'])){

            // Read each username/password pair into an array. Since file() adds new line characters at the end
            // of each line, the FILE_IGNORE_NEW_LINES flag needs to be used
            $validCredentials = file("passwords.txt", FILE_IGNORE_NEW_LINES);

            // Set user validation flag
            $goodCredentials = false;
            foreach($validCredentials as $validCredential){
                $validPair = preg_split("/,/", $validCredential);
                // Check if user name is valid
                if ($_POST['u'] == $validPair[0]){
                    // Check if password is valid
                    if ($_POST['p'] == $validPair[1]){
                        $goodCredentials = true;
                        /*****************************************************************************************************************************
                         * Requirement 4b: If the POST username and password match data from passwords.txt, then create a cookie named “uname”
                         * with the value of the username, and also create a cookie named “loggedintime” which stores the time that they logged
                         * in (using the time() function), and show them a menu of options available to them
                         ****************************************************************************************************************************/
                        setcookie("uname", $_POST['u'], time()+(60*20));
                        setcookie("loggedintime", time());

                        echo "<p>Welcome, ".$_POST['u']."!</p>";

                        if (isset($_COOKIE['loggedintime'])){
                            $loggedInFor = time() - $_COOKIE['loggedintime'];
                            echo "<p>You have been logged in for ".$loggedInFor." seconds</p>";
                        }
                        // Print out links
                        printLinks();
                        die();
                    }
                    // For security reasons, DO NOT return a message if password is incorrect when the username is correct
                }
            }
            /*****************************************************************************************************************************
             * Requirement 4c: If the POST username and password do not match data from passwords.txt, append to (and/or create) a file
             * called invalid-logins.txt which stores the username, password, and IP Address of the attempted login
             ****************************************************************************************************************************/
            if (!$goodCredentials){
                logInvalidLogin($_POST['u'], $_POST['p']);
            }
        }
    }
}
// "Remember Me" is not checked
else {
    if (isset($_POST['u']))
    // Delete cookies
    @setcookie('uname', $_POST['uname'], time()-1);
    drawForm('', '');
}
}
ob_end_flush();
?>

