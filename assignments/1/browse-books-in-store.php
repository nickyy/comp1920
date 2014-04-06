<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: February 26, 2014
 * File: browse-books-in-store.php
 * File description: Comp 1920 Assignment 1 file. The goal of this lab is to use the ideas and code from
 * the first two lectures and create a PHP script that uses dates, operators, strings,
 * conditionals, and loops.
 */

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
?>
<!DOCTYPE html>
<html>
<head>
    <title>Comp 1920 Assignment 1 by Nicky Yuen</title>
    <meta charset="utf-8">
    <style>
        table, td, th {
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
</head>
<body>


<?php
if (!isset($_COOKIE['uname'])){
    // If the user has not logged in (i.e., there is no cookie storing their username),
    // then do NOTHING except show a message and a link to index.php
    echo "<p>You must <a href='index.php'>log in</a> first.</p>";
}
else {
    // If the user has logged in, then create a text file named “books.txt”
    $fhBooks = @fopen("books.txt", "r") or exit("<p>File error.</p>");

    // Put all the contents of the books.txt file into an array
    $booksContent = file("books.txt");

    // Create array to contain books with valid formatting
    $validBooks = array();

    /**********************************************************/
    /* CHECK BOOK FORMATTING
    /**********************************************************/
    // Loop through each book and check the formatting
    foreach($booksContent as $singleBook){
        // Set a flag for book errors (missing dates, etc.)
        $bookError = false;

        if (preg_match("/^.*by.*published.*$/", $singleBook)){

            // Get parsed title array
            $title = preg_split("/by/", $singleBook);

            // Title is first array element of parsed string (ie. element 0)
            $title = trim($title[0]);

            // Get parsed "published and born year" array
            $publishedAndBorn = preg_split("/published/", $singleBook);

            // Trim white space for each element, using array_map, after splitting by word "born"
            $publishedAndBorn = array_map("trim", preg_split("/born/", $publishedAndBorn[1]));

            // Check the published year and birth year. If either isn't present, then assign -1 to it
            if (empty($publishedAndBorn[0])){
                $publishedAndBorn[0] = -1;
                $bookError = true;

            }
            if (empty($publishedAndBorn[1])){
                $publishedAndBorn[1] = -1;
                $bookError = true;
            }

            // Finally get the published year and birth year
            $publishedYear = trim($publishedAndBorn[0]);
            $bornYear = trim($publishedAndBorn[1]);

            // Detect the following two types of errors:
            // a. If the published year comes BEFORE the born year
            // b. Missing any year
            // and write the contents of that book entry into the appropriate file, depending on the error

            // Check if both published year and born year are present
            if (($publishedYear != -1) && ($bornYear != -1)){
                // Check if published year is earlier than birth year
                if ($publishedYear < $bornYear){
                    $bookError =  true;
                    $fhPublishedBeforeBorn = fopen("published-before-born.txt", "a") or exit("<p>File error</p>");
                    fwrite($fhPublishedBeforeBorn, $singleBook);
                    fclose($fhPublishedBeforeBorn);
                }
            }

            // Check if birth year is missing
            if ($bornYear == 0){
                $bookError = true;
                $fhMissingBornYear = fopen("missing-born-year.txt", "a") or exit("<p>File error</p>");
                fwrite($fhMissingBornYear, $singleBook);
                fclose($fhMissingBornYear);
            }

            // Check if published year is missing
            if ($publishedYear == 0){
                $bookError = true;
                $fhMissingPublishedYear = fopen("missing-published-year.txt", "a") or exit("<p>File error</p>");
                fwrite($fhMissingPublishedYear, $singleBook);
                fclose($fhMissingPublishedYear);
            }

            // Create an array of only the contents which have no errors
            if (!$bookError){
                array_push($validBooks, $singleBook);
            }
        }
    }
    fclose($fhBooks);

    // $validBooks now contains array of books with valid formatting
    // Now need to sort the $validBooks by published year and then 
    // break into pieces to be formatted into table


    /**********************************************************/
    /* SORT VALID BOOKS
    /**********************************************************/
    // Sort the books by published year by breaking it down into pieces first
    $validBooksPieces = array();
    foreach($validBooks as $validBook) {
        $validBooksPieces[] = explode(" ", $validBook);
    }
    
    // Compare the year published value (which is the 3rd last string)
    function compare($a, $b){
        return strcmp($a[count($a)-3], $b[count($b)-3]);
    }

    // Sort the books by published year, using 'compare' function
    usort($validBooksPieces, "compare");

    // Implode the array back into original string (this time it's sorted!)
    $validBooksSorted = array();
    foreach($validBooksPieces as $book){
        $validBooksSorted[] = implode(" ", $book);   
    }

    /**********************************************************/
    /* FORMAT INTO TABLE
    /**********************************************************/
    // Start table html string
    ?>
    <table>
        <thead style="background-color:#bbb">
        <tr>
            <th>Title</th><th>Published</th><th>Author Last Name</th><th>Author First Name</th><th>Birth Year</th>
        </tr>
        </thead>
        <tbody>

    <?php

    // Split book into components
    $rowCount = 0;
    foreach($validBooksSorted as $book){
        $rowCount++;
        // Split by "by"
        $splitArr = preg_split("/by/", $book);

        // First part of split string is title
        $title = trim($splitArr[0]);

        // Split by "published"
        $splitArr = preg_split("/published/", $splitArr[1]);

        /// First part of split string is name (first and last)
        $nameArr = preg_split("/\s/", trim($splitArr[0]));

        // Last name is the last element of $nameArr
        $lastName = $nameArr[count($nameArr)-1];

        // First name is everything BUT the last part of $nameArr
        // Pop the last part of array (ie. the last name)
        array_pop($nameArr);
        $firstNameArr = $nameArr;
        
        // Join all first name array elements
        $firstName = implode(" ", $firstNameArr);

        // Split by "born"
        $splitArr = preg_split("/born/", $splitArr[1]);

        // First part of split string is published year
        $published = trim($splitArr[0]);

        // Remaining part of split string is birth year
        $born = trim($splitArr[1]);

        // Apply alternating background colour striping
        ($rowCount%2) ? $bgColor = "#fff" : $bgColor = "#bbb";
        echo "<tr style='background-color:".$bgColor."'><td>".$title."</td><td>".$published."</td><td>".$lastName."</td><td>".$firstName."</td><td>".$born."</td></tr>";
    }
    ?>
        </tbody>
    </table>
    <?php
    echo "<hr>";
    echo "<p>Welcome, ".$_COOKIE['uname']."!</p>";

    if (isset($_COOKIE['loggedintime'])){
        $loggedInFor = time() - $_COOKIE['loggedintime'];
        echo "<p>You have been logged in for ".$loggedInFor." seconds</p>";
    }
    // Print out links
    printLinks();
}
?>

</body>
</html>