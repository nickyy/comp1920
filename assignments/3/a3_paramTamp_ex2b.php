<?php
/**
 * Created by PhpStorm.
 * User: Nicky
 * Date: 3/29/14
 * Time: 4:41 PM
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Example 2</title>
    <style>
        * {
            font-family: arial, sans-serif;
        }

        label {
            padding: 10px 10px 10px 0;
        }

        fieldset {
            width: 60%;
            margin-top: 10px;
        }

        legend {
            color: white;
            font-size: 1.5em;
            background: black;
            border-radius: 10px;
            padding: 0 15px;
        }
    </style>
</head>
<body>
    <fieldset>
        <legend>Example #2 of Parameter Tampering - Code Injection via Hidden Form Fields</legend>
        <form>
            <input type="hidden" name="username" value="<?php echo $_GET['name'];?>">
            <p>
                Show some other form fields here.....
            </p>
        </form>
        <a href="a3_paramTamp_ex2a.php">Back to the first page</a>
    </fieldset>
</body>
</html>


