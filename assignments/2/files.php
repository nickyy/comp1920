<?php
/**
 * Author: Nicky Yuen (A00880493)
 * Last modified: March 18, 2014
 * File: files.php
 * File description: Comp 1920 Assignment 2 file used for file upload, file parsing, image resizing, and emailing.
 */

// Import the various functions used for the different operations from functions.php
require_once("functions.php");

// Buffer any output before sending header info
ob_start();

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
            if ($_SESSION['loggedIn']){
                ?>
                <li><a href="files.php">Process Image</a></li>
                <?php
            }
            ?>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
    <section class="main">

        <form method="POST" action="files.php" enctype="multipart/form-data">
            <div class="file">
             <label for="file01">File 01</label><input type="file" name="file[]" id="file01">
            </div>
            <div class="file">
             <label for="file02">File 02</label><input type="file" name="file[]" id="file02">
            </div>
            <div class="file">
             <label for="file03">File 03</label><input type="file" name="file[]" id="file03">
            </div>
            <div class="file">
             <label for="file04">File 04</label><input type="file" name="file[]" id="file04">
            </div>
            <div class="file">
             <label for="file05">File 05</label><input type="file" name="file[]" id="file05">
            </div>
            <div class="file">
             <label for="file06">File 06</label><input type="file" name="file[]" id="file06">
            </div>
            <div class="file">
             <label for="file07">File 07</label><input type="file" name="file[]" id="file07">
            </div>
            <div class="file">
             <label for="file08">File 08</label><input type="file" name="file[]" id="file08">
            </div>
            <div class="file">
             <label for="file09">File 09</label><input type="file" name="file[]" id="file09">
            </div>
            <div class="file">
             <label for="file10">File 10</label><input type="file" name="file[]" id="file10">
            </div>
            <input id="upload" type="submit" value="Upload Files">
        </form>

    <?php

    // Check if submit button was pressed
    if ($_SERVER['REQUEST_METHOD'] == 'POST'){
        // Define target folder location to copy files to
        $targetFolder = "as2_files";

        // Check if target directory exists. If not, create it.
        if (!is_dir($targetFolder)){
            mkdir($targetFolder);
            echo "<p>Folder '$targetFolder' created</p>";
        }

        // Define target folder to upload files to
        $targetFolder = "as2_files/";

        // Variable to hold name of file with jpeg
        $jpgFile = "";

        // Array to hold name of text file(s) which contain an email address
        $emailFiles[] = array();

        // Determine length of the 2D array
        $length = max(array_map('count', $_FILES['file']));

        // Initialise jpg and email flags
        $hasMoreThanOneJpg = false;
        $hasJpg = false;
        $hasEmail = false;

        // Count the number of files uploaded
        $numFilesUploaded = 0;

        // Loop through each uploaded file
        for ($i=0; $i<$length; $i++){
            $fileTmp = $_FILES['file']['tmp_name'][$i];
            if (!empty($fileTmp)){
                $numFilesUploaded++;
                $uploadedFile = $targetFolder.basename($_FILES['file']['name'][$i]);
                if (move_uploaded_file($_FILES['file']['tmp_name'][$i], $uploadedFile)){
                    // Get the file name and the extension
                    $filenameParts = explode(".", $_FILES['file']['name'][$i]);

                    // Check if file is a .jpg file
                    if (isJpg(strtolower($filenameParts[1]))){
                        // Check if a jpg file already exists (ie. $hasJpg = true)
                        if (!$hasJpg && !$hasMoreThanOneJpg){
                            $jpgFile = implode(".", $filenameParts);
                            $hasJpg = true;
                        }
                        else {
                            $hasMoreThanOneJpg = true;
                        }
                    }

                    // Check if contents of file have email address
                    $fileContents = file_get_contents($targetFolder.$_FILES['file']['name'][$i]);

                    // Store all the files containing email addresses into an array
                    if (checkForEmail(trim($fileContents))){
                        $emailFiles[$i] = $_FILES['file']['name'][$i];
                        $hasEmail = true;
                    }
                }
            }
        }
        // Output a warning message if more than one JPG file was selected for upload. Only upload the first one selected.
        if ($hasMoreThanOneJpg){
            echo "<p style='font-style:italic'>(Note: More than one JPG file selected. Using first one only: $jpgFile)</p>";
        }

        // Display error message depending on type of error
        if (!$hasJpg || !$hasEmail){
            // If no jpg was uploaded, notify user
            if (!$hasJpg){
                echo "<p class='error'>At least one file needs to be a JPG file.</p>";
            }

            // If no email address was uploaded, notify user
            if (!$hasEmail){
                echo "<p class='error'>At least one file needs to have an email address!</p>";
            }
            echo "<p>Reselect your files again.</p>";
        }
        // If required files available (ie. jpg and email address)
        else {
            echo "<p class='success'>".$numFilesUploaded." files uploaded.</p>";
            // Remove empty arrays from $emailFiles and reset keys
            $emailFiles = array_values(array_filter($emailFiles));

            // Remove duplicate files from array
            $emailFiles = array_unique($emailFiles);

            // Call the function to create the thumbnail
            createThumbnail($jpgFile, $targetFolder, "assignment2.jpg");

            // Loop through all the (unique) email files to get the email address,
            // and then send thumbnail attachment to the email.
            echo "<p class='success'>Email with thumbnail attachment sent to:</p>";
            if ($emailFiles){
                for ($i=0; $i<count($emailFiles); $i++){
                    // Store the email addresses into $email variable
                    $email = file_get_contents($targetFolder.$emailFiles[$i]);

					
					
                    // ******************** NEED TO CHANGE DEPENDING ON SHAW OR TELUS *********************
                    //ini_set("SMTP", "mail.shaw.ca");
                    ini_set("SMTP", "smtp.telus.net");
					// ******************** NEED TO CHANGE DEPENDING ON SHAW OR TELUS *********************

					
					
                    // THESE STAY THE SAME
                    ini_set("smtp_port", "25");
                    ini_set("sendmail_from", "nicky@comp1920.com");

                    // Call the function to send attachment to the email specified in $email variable
                    if (sendEmail(
                            $email,
                            "Comp 1920 Assignment 2 image resized",
                            "Your image has been resized and is attached. Regards, Nicky".PHP_EOL,
                            $targetFolder,
                            "assignment2.jpg"
                        )){
                        // Email was succesfully sent
        //                echo "<p style='color:green;font-style:italic'>Mail sent to: $email</p>";
                    }
                    // Email failed to send
                    else {
                        echo "<p class='error'>Mail could not be sent to: $email</p>";
                    }

                    // Draw and display a rectangle, with email address, that message and attachment were sent to
                    createSqImg($email, $targetFolder);
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
