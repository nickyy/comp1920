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
    <legend>Example #3 of Parameter Tampering - Hidden Form Fields And Tampering Tools</legend>
    <img src="samsung.jpg" alt="Samsung" width="200" height="200">
    <p>Product ID: UN60F8000AFXZC</p>
    <p>Samsung 60" 1080p 240Hz Smart LED TV</p>
    <p>Price: $2499.99</p>
    <form method="POST" action="a3_paramTamp_ex3b.php">
        <label for="quantity">Quantity:</label><input type="text" size="3" maxlength="3" name="quantity" id="quantity">
        <input type="hidden" name="price" value="2499.99">
        <input type="hidden" name="desc" value="Samsung 60 1080p 240Hz Smart LED TV">
        <input type="submit" value="Submit">
    </form>
</fieldset>
</body>
</html>

