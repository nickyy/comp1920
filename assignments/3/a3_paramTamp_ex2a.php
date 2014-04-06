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
        <form method="GET" action="a3_paramTamp_ex2b.php">
            <label for="name">Enter a name:</label> <input type="text" name="name" id="name">
            <input type="submit" value="Submit">
        </form>
    </fieldset>
</body>
</html>

