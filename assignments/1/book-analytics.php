<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: February 26, 2014
 * File: book-analytics.php
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
        .special {
            color: red;
        }
    </style>
</head>
<body>
<?php
// If the cookie for the logged in user doesn't exist, then prompt for login
if (!isset($_COOKIE['uname'])){
    echo "<p>You must <a href='index.php'>log in</a> first!</p>";
}

// The cookie for the logged in user exists, so show welcome message and logged in duration
else {
    // Print out welcome message
    echo "<p>Welcome, ".$_COOKIE['uname']."!</p>";

    // Show logged in duration
    if (isset($_COOKIE['loggedintime'])){
        $loggedInFor = time() - $_COOKIE['loggedintime'];
        echo "<p>You have been logged in for ".$loggedInFor." seconds</p>";
    }

    // Read the contents of books.txt
    $fhBooks = @fopen("books.txt", "r") or exit("<p>File error.</p>");

    // Put all the contents of the books.txt file into an array
    $booksContent = file("books.txt");

    // Create arrays to store various analytics, and initialise variables
    $authorCountArr = array();
    $titleLengthArr = array();
    $authorAgeArr = array();
    $booksBefore1950Arr = array();
    $specialBooksArr = array();
    $numBooksBefore1950 = 0;
    $numSpecialBooks = 0;

   // Loop through each item of the book.txt file
    foreach($booksContent as $book){
        // If the string has the keywords "by" and "published", then parse it
        if (preg_match("/^.*by.*published.*$/", $book)){

            /***************************************************
             * Determine frequency of authors
             ***************************************************/
            $author = preg_split("/by/", $book);
            $author = preg_split("/published/", $author[1]);
            // Convert to all lower case in case different casing used, and then make first letters uppercase
            $author = ucwords(strtolower(trim($author[0])));
            $authorCountArr[] = $author;

            /***************************************************
             * Determine longest title
             ***************************************************/
            // Get parsed title array
            $title = preg_split("/by/", $book);

            // Title is first array element of parsed string (ie. element 0). Populate $titleLength array with the title of book and length of title.
            // Convert key to all lower case in case different casing used, and then make first letters uppercase
            $titleLengthArr[ucwords(strtolower(trim($title[0])))] = strlen(trim($title[0]));

            /***************************************************
             * Determine oldest author when book published
             ***************************************************/
            // Get parsed "published and born year" array
            $publishedAndBorn = preg_split("/published/", $book);

            // Trim white space for each element, using array_map, after splitting by word "born"
            $publishedAndBorn = array_map("trim", preg_split("/born/", $publishedAndBorn[1]));

            // Check the published year and birth year. If either isn't present, then assign -1 to it
            if (empty($publishedAndBorn[0])){
                $publishedAndBorn[0] = -1;
            }
            if (empty($publishedAndBorn[1])){
                $publishedAndBorn[1] = -1;
            }

            // Finally get the published year and birth year
            $publishedYear = (int)trim($publishedAndBorn[0]);
            $bornYear = (int)trim($publishedAndBorn[1]);

            // Calculate the author's age when book was published
            // Convert key to all lower case in case different casing used, and then make first letters uppercase
            $authorAgeArr[ucwords(strtolower(trim($author)))] = $publishedYear - $bornYear;

            /***************************************************
             * Determine number of books published before 1950
             ***************************************************/
            if ($publishedYear > 1950){
                $booksBefore1950Arr[] = trim($title[0]);
            }
            $numBooksBefore1950 = count($booksBefore1950Arr);
        }

        /***************************************************
         * Determine special books
         ***************************************************/
        $year = "(([0-1]\d{3})|(20[0-1][0-4])){1}"; // year 0000 to 2014
        $_0to9 = "(0[1-9])";                        // Day 00 to 09
        $_10to29 = "([1-2]\d)";                     // Day 10 to 29
        $_30to31 = "(3[0-1])";                      // Day 30 to 31
        $_join = "[-\/]";
        $regex = "/".
            $year.                                      // Any year from 0000 to 2014
            $_join.                                    // - or /
            "((".
                "((01)|(03)|(05)|(07)|(08)|(10)|(12)){1}". // Months with up to 31 days
                $_join.                                    // - or /
                "($_0to9|$_10to29|$_30to31){1})|".         // day 00 to 31
            "(".
                "((04)|(06)|(09)|(11)){1}".                // OR months with up to 30 days
                $_join.                                    // - or /
                "($_0to9|$_10to29|(30)){1})|".             // day 00 to 30
            "(".
                "02".                                      // OR February
                $_join.                                    // - or /
                "($_0to9|$_10to29){1}".                    // day 00 to 29
            ")){1}/";

        if (preg_match($regex, $book)){
            $specialBooksArr[] = trim($book);
        }
        $numSpecialBooks = count($specialBooksArr);
    }

    /***************************************************
     * Process analytics, and print out
     ***************************************************/
    // Most books
    $authorCountArr = array_count_values($authorCountArr);
    $mostBooksArr = array_keys($authorCountArr, max($authorCountArr));
    $maxBooksKey = $mostBooksArr[0];

    // Oldest author
    $oldestAuthArr = array_keys($authorAgeArr, max($authorAgeArr));
    $maxAgeKey = $authorAgeArr[$oldestAuthArr[0]];

    // Longest title
    $longestTitleArr = array_keys($titleLengthArr, max($titleLengthArr));
    $maxTitleKey = $titleLengthArr[$longestTitleArr[0]];

    // Published before 1950
    $booksBefore1950 = implode(", ", $booksBefore1950Arr);

    ?>
    <p>
        <span class="special"><?php echo $mostBooksArr[0]?></span> wrote the most books
        <span class="special"> (<?php echo $authorCountArr[$maxBooksKey]?> books)</span>
    </p>
    <p>
        <span class="special"><?php echo $oldestAuthArr[0]?></span> was the oldest author
        <span class="special"> (<?php echo $maxAgeKey?> years)</span>
    </p>
    <p>
        <span class="special"><?php echo $longestTitleArr[0]?></span> is the longest title
        <span class="special"> (<?php echo $maxTitleKey?> letters)</span>
    </p>
    <p>
        <span class="special"><?php echo $numBooksBefore1950?> books</span> were published before 1950
        <span class="special"> (<?php echo $booksBefore1950?>)</span>
    </p>

    <!-- Print out special books if there are any -->
    <?php
    if ($numSpecialBooks > 0){
        echo "<hr>";
        echo "<h2>$numSpecialBooks special books have been identified!</h2>";
        echo "<ol>";
        foreach($specialBooksArr as $specialBook){
            echo "<li>".$specialBook."</li>";
        }
        echo "</ol>";
        echo "<hr>";
        printLinks();
    }
}
?>

</body>
</html>