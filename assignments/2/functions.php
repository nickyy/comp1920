<?php
/**
* Author: Nicky Yuen (A00880493)
* Last modified: March 18, 2014
* File: functions.php
* File description: Comp 1920 Assignment 2 file containing various helper functions.
 */

/********************************************
 * Draw the form depending on the $username input
 * @param $u - username from form
 *******************************************/
function showForm($u){
    ?>
    <section class="login">
        <form method='POST' action='login.php'>
            <input type='text' name='u' placeholder='username' value=<?php echo $u?>>
            <input type='password' name='p' placeholder='password'>
            <input type='submit' value='Login' id="loginButton">
        </form>
    </section>
    <?php
}


/********************************************
 * Increment the visit count in database of user logging in
 * @param $dB - link to the database
 * @param $u - username from form
 *******************************************/
function setVisits($dB, $u){
    $q = "
        UPDATE personalinfo
        SET numberofvisits = numberofvisits + 1
        WHERE username = '".$u."'
    ";
    $dB->query($q);
}


/********************************************
 * Set the IP address and write it to the database, using the username
 * @param $dB - link to the database
 * @param $u - username from form
 * @param $ip - IP address of user's computer
 *******************************************/
function setIPAddress($dB, $u, $ip){
    $q = "
        UPDATE personalinfo
        SET ipaddress = '".$ip."'
        WHERE username = '".$u."'
    ";
    $dB->query($q);
}


/********************************************
 * Get the password from database of username
 * @param $dB - link to the database
 * @param $u - username from form
 * @return mixed - return the password for associated username
 *******************************************/
function getPassword($dB, $u){
    $q = "
        SELECT password
        FROM personalinfo
        WHERE username = '".$u."'
    ";
    $result = $dB->query($q) or die($dB->error);
    $password = $result->fetch_assoc();
    return $password['password'];
}


/********************************************
 * Get the user's info (first name, last name, number of visits, and IP address) from database
 * @param $dB - link to the database
 * @param $u - username from form
 * @return mixed - return user info
 *******************************************/
function getUserInfo($dB, $u){
    $q = "
        SELECT firstname, lastname, numberofvisits, ipaddress
        FROM personalinfo
        WHERE username = '".$u."'
    ";
    $result = $dB->query($q) or die($dB->error());
    $userDetails = $result->fetch_assoc();
    return $userDetails;
}


/******************************************
 * Check the login info provided in form by the user, and validate against all usernames in database
 * @param $dbLink - link to the database
 * @param $userArr - array of all usernames in database
 * @param $u - username from form
 * @param $p - password from form
 * @return bool - status of username/password
 ******************************************/
function checkLogin($dbLink, $userArr, $u, $p){
    // Start login session
    session_start();

    // If $_SESSION['attempts'] variable isn't set yet, then initialise it to 1 for the first login attempt
    if (!isset($_SESSION['attempts'])){
        $_SESSION['attempts'] = 1;
    }
    // otherwise increment for each login attempt, up to 3 tries
    else {
        $_SESSION['attempts']++;
    }

    // Initialise login flag
    $validLogin = false;
    $validUsername = false;

    // Check if user exists
    foreach ($userArr as $username){
        // Username entered is valid
        if (trim($u) == $username){
            // Set username flag
            $validUsername = true;

            // Retrieve password for entered username
            $password = getPassword($dbLink, $u);

            // Check if password matches
            if (trim($p) == $password){
                // Login successful
                return $validLogin = true;
            }
            // Password doesn't match
            else {
                echo "<p class='error'>Wrong password</p>";
                return $validLogin = false;
            }
        }
        // User doesn't exist, so set login flag to false
        else {
            $validUsername = false;
            $validLogin = false;
        }
    }
    // If username flag was false, it was because entered username does not exist
    if (!$validUsername){
        echo "<p class='error''>No such user</p>";
    }
    return $validLogin;
}


/******************************************
 * Check if string contains an email address
 * @param $content - input string to check for email address
 * @return bool - true or false
 ******************************************/
function checkForEmail($content){
    // Email address regex from https://www.owasp.org/login.php/OWASP_Validation_Regex_Repository
    $emailRegex = "/^[a-zA-Z0-9+&*-]+(?:\.[a-zA-Z0-9_+&*-]+)*@(?:[a-zA-Z0-9-]+\.)+[a-zA-Z]{2,7}$/";

    // Check if content string has an email address
    if (preg_match($emailRegex, $content)){
        return true;
    }
    else {
        return false;
    }
}


/******************************************
 * Check if file has a .JPG or .jpg extensions
 * @param $content - the string to check
 * @return bool - true or false
 *****************************************/
function isJpg($content){
    $jpgRegex = "/^jpg$/i";
    if (preg_match($jpgRegex, $content)){
        return true;
    }
    else {
        return false;
    }
}


/******************************************
 * Create a thumbnail image
 * @param $originalFile - the original image file to convert
 * @param $folder - the folder from which to find the original image
 * @param $thumbnailName - the name of the new thumbnail image
 *****************************************/
function createThumbnail($originalFile, $folder, $thumbnailName){
    // Process JPG image file (remember that the image file is in the target folder
    // that was used to upload the files). Thumbnail will be created in root folder.
    $originalImage = imageCreateFromJpeg($folder.$originalFile);

    // Get original image dimensions
    $sizeX = imagesx($originalImage);
    $sizeY = imagesy($originalImage);

    // Persist the x:y ratio.
    // If x > y, then x:y = 100:newY --> newY = 100y/x
    // If y > x, then x:y = newX:100 --> newX = 100x/y
    if ($sizeX >= $sizeY){
        $newSizeX = 100;
        $newSizeY = round(100*$sizeY/$sizeX);
    }
    else {
        $newSizeY = 100;
        $newSizeX = round(100*$sizeX/$sizeY);
    }

    // Create the thumbnail image with the new dimensions
    $thumbnail = imageCreateTrueColor($newSizeX, $newSizeY);

    // Copy and resize the original image based on the new dimensions, to create the thumbnail
    imageCopyResampled($thumbnail, $originalImage, 0, 0, 0, 0, $newSizeX, $newSizeY, $sizeX, $sizeY);

    // Generate new image and output to file with best quality, saved to path "$folder.$thumbnailName"
    if (imageJpeg($thumbnail, $folder.$thumbnailName, 100)){
        echo "<p class='success'>The following thumbnail image '".$thumbnailName."' created from '".$originalFile."':</p>";
        ?>
            <div class="thumbnail"><img src="<?php echo $folder.$thumbnailName?>" alt="<?php echo $folder.$thumbnailName?>"></div>
        <?php
    }
    else {
        echo "<p class='error'>Uh oh! Something wrong with creating '".$thumbnailName."'.</p>";
    }

    // Since we're outputting the image (using "<img src=....>") along with other text on the page,
    // then don't need to declare header type or call imagejpeg function
    //header("Content-Type: image/jpg");
    // Output the image to browser
    //imageJpeg($thumbnail);

    // Destroy image from memory
    imageDestroy($thumbnail);
}


/******************************************
 * Function to send mail, copied pretty much from http://www.litfuel.net/tutorials/mail2.htm
 * @param $to - recipient email address
 * @param $subject - subject of email
 * @param $msg - body message of email
 * @param $folder - the location of the file to send
 * @param $fileName - name of file attachment
 * @return bool - true or false
 *****************************************/
function sendEmail($to, $subject, $msg, $folder, $fileName){
    // Set the timezone
    date_default_timezone_set('America/Vancouver');

    // Configure the email parameters
    $boundText = "nyuen_comp1920_asgn2";
    $bound = 	"--".$boundText."\r\n";
    $boundLast = 	"--".$boundText."--\r\n";

    // Configure headers... again, more info from http://www.litfuel.net/tutorials/mail2.htm
    $headers =  "From: nicky@comp1920_assignment2.com\r\n".
                "MIME-Version: 1.0\r\n".
                "Content-Type: multipart/mixed; boundary=\"$boundText\"";

    $message = "If you can see this MIME, then your client doesn't accept MIME types!\r\n".
                $bound;

    // Configure the message
    $message .= "Content-Type: text/html; charset=\"iso-8859-1\"\r\n".
                "Content-Transfer-Encoding: 7bit\r\n\r\n".
                $msg."\r\n".
                $bound;

    // Get the file to attach
    $file = file_get_contents($folder.$fileName);

    // More configuration for message
    $message .= "Content-Type: image/jpg; name=\"$fileName\"\r\n".
                "Content-Transfer-Encoding: base64\r\n".
                "Content-disposition: attachment; file=\"$fileName\"\r\n".
                "\r\n".
                chunk_split(base64_encode($file)).
                $boundLast;

    // Send the mail
    return mail($to, $subject, $message, $headers);
}


/******************************************
 * Random colour generator modified from
 * http://www.craiglotter.co.za/2010/10/20/php-random-color-generator-function/
 * and
 * http://stackoverflow.com/questions/9186038/php-generate-rgb
 * @return string - a HEX string for a colour
 *****************************************/
function genRandomColor(){
    $randomColour = strtoupper(dechex(rand(0,10000000)));
    if (strlen($randomColour) != 6){
        $randomColour = str_pad($randomColour, 10, '0', STR_PAD_RIGHT);
        $randomColor = substr($randomColour, 0, 6);
    }

    $RBG = array(
        hexdec(substr($randomColour, 0, 2)),
        hexdec(substr($randomColour, 2, 2)),
        hexdec(substr($randomColour, 4, 2)));

    return $RBG;
}


/******************************************
 * Draw a coloured rectangle with the email address as a caption
 * @param $email - the text to be placed in the image
 *****************************************/
function createSqImg($email, $folder){
    // Allocate memory for square image
    $square = imagecreatetruecolor(200, 50);

    // Define colour white for image (used for text)
    $white  = imagecolorallocate($square, 255, 255, 255);

    // Generate a random colour RGB array
    $colour = genRandomColor();

    // Fill in background
    imagefill($square, 0, 0, imagecolorallocate($square, $colour[0], $colour[1], $colour[2]));

    // Write email into image
    imagestring($square, 3, 20, 20, $email, $white);

    // Clear the output buffers
//    ob_clean();
    // Set the content type as an image
//    header('Content-Type: image/png');

    // Replace dots in email with underscores
    $fileName = str_replace(".", "_", $email);

    // Replace "@" with "_at_"
    $fileName = str_replace("@", "_at_", $fileName);
    $fileName .= ".png";

    // Output square to screen
    imagepng($square, $folder.$fileName, 9);
    ?>
    <div class="emailFig">
        <img src='<?php echo $folder.$fileName?>' alt='Email sent to <?php echo $email?>'>
    </div>
    <?php

    // Destroy image from memory
    imagedestroy($square);
}
?>