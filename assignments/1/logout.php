<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: February 26, 2014
 * File: logout.php
 * File description: Comp 1920 Assignment 1 file. This page completely deletes
 * all of the cookies and shows a link to sign in again
 */

ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Comp 1920 Assignment 1 by Nicky Yuen</title>
    <meta charset="utf-8">
</head>
<body>

<?php

// Delete all cookies related to 'uname'

// Define variable to store past time
$past = time()-1;

// If the user cookie is set, then delete cookies
if (isset($_COOKIE['uname'])){
    setcookie("uname", $_COOKIE['uname'], $past);
    setcookie("loggedintime", '', $past);
    setcookie("remember", '', $past);
}

// Print out message to log in
echo "<p>You can <a href='index.php'>log in</a> again</p>";
ob_end_flush();
?>