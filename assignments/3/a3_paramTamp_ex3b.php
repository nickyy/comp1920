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

        #order {
            text-align: center;
        }

        #orderDetails {
            font-weight: bold;
        }
    </style>
</head>
<body>
<fieldset>
    <legend>Example #3 of Parameter Tampering - Hidden Form Fields And Tampering Tools</legend>
    <div id="order">
        <h2>Thanks for your order!</h2>
        <p>Order details:</p>
        <img src="samsung.jpg" width="100">
        <p id="orderDetails"><?php echo $_POST['quantity']."x ".$_POST['desc']." for $".$_POST['price']*$_POST['quantity'] ?></p>
    </div>
    <p><a href="a3_paramTamp_ex3a.php">Back to the first page</a></p>
</fieldset>
</body>
</html>


