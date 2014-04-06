<?php
/**
 * Created by PhpStorm.
 * User: Nicky
 * Date: 3/23/14
 * Time: 9:52 PM
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Example 1</title>
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
        <legend>Example #1 of Parameter Tampering</legend>
        <h2>Welcome to the Bank of Fraud</h2>
        <p>Select an account number and enter a deposit amount:</p>
        <form method="GET" action="a3_paramTamp_ex1.php">
            <label for="account">Account</label><select name="account" id="account">
                <option value="" selected="selected">Select account</option>
                <option value="1111">1111</option>
                <option value="2222">2222</option>
                <option value="3333">3333</option>
            </select>
            <label for="amount">Amount</label><input type="text" id="amount" name="amount" placeholder="0.00">
            <input type="submit" value="Submit"><input type="reset" value="Clear">
        </form>
    </fieldset>
<?php
$db = "yuenwork_dB";//"comp1920";
$h = "localhost";
$u = "yuenwork_dbuser";//"root";
$p = "yuenwork_dbuser123";//"nyuen123";
$t = "a3_ex1";

// Connect to database
$c = mysqli_connect($h, $u, $p, $db) or die(mysqli_connect_error());

// Create table if not exist
$q = "
    CREATE TABLE IF NOT EXISTS ".$t."(
    account         INT             NOT NULL PRIMARY KEY,
    amount          DECIMAL(10,2)   NULL,
    firstName       VARCHAR(255)    NULL,
    lastName        VARCHAR(255)    NULL
    );
";
mysqli_query($c, $q) or die(mysqli_error($c));

/*
// Here's the SQL to delete the records
$q = "TRUNCATE TABLE ".$t.";";
// Execute the query
mysqli_query($c, $q) or die(mysqli_error($c));
*/

// Create account #1 if not already exists
$q = "
    INSERT INTO ".$t." (account, amount, firstName, lastName)
    SELECT * FROM (SELECT 1111, 0.00, 'Nicky', 'Yuen') AS tmp
    WHERE NOT EXISTS (SELECT account FROM ".$t." WHERE account = 1111) LIMIT 1
    ;";
mysqli_query($c, $q) or die(mysqli_error($c));

// Create account #2 if not already exists
$q = "INSERT INTO ".$t." (account, amount, firstName, lastName)
    SELECT * FROM (SELECT 2222, 0.00, 'David', 'Nyquist') AS tmp
    WHERE NOT EXISTS (SELECT account FROM ".$t." WHERE account = 2222) LIMIT 1
    ;";
mysqli_query($c, $q) or die(mysqli_error($c));

// Create account #3 if not already exists
$q = "INSERT INTO ".$t." (account, amount, firstName, lastName)
    SELECT * FROM (SELECT 3333, 0.00, 'Jason', 'Truman') AS tmp
    WHERE NOT EXISTS (SELECT account FROM ".$t." WHERE account = 3333) LIMIT 1
    ;";
mysqli_query($c, $q) or die(mysqli_error($c));

// Create account #4 if not already exists
$q = "INSERT INTO ".$t." (account, amount, firstName, lastName)
    SELECT * FROM (SELECT 4444, 0.00, 'Jason', 'Harrison') AS tmp
    WHERE NOT EXISTS (SELECT account FROM ".$t." WHERE account = 4444) LIMIT 1
    ;";
mysqli_query($c, $q) or die(mysqli_error($c));

if ($_SERVER['REQUEST_METHOD'] == 'GET'){
    if (!empty($_GET['amount'])){
        $a = $_GET['amount'];
        $acct = $_GET['account'];

        $q = "UPDATE ".$t." SET amount=amount+".$a." WHERE account=".$acct.";";
        mysqli_query($c, $q) or die(mysqli_error($c));

        $q = "SELECT firstName, lastName, amount FROM ".$t." WHERE account=".$acct.";";
        $info = mysqli_query($c, $q) or die(mysqli_error($c));
        $info = mysqli_fetch_row($info);

        echo "<p>Hi, <span style='font-weight:bold'>".$info[0]." ".$info[1]."</span>, you deposited $".$a." into account #".$acct.".</p>";
        echo "<p>Your account total is: <span style='font-weight:bold;text-decoration:underline'>$".$info[2]."</span></p>";

        echo "<p><a href='a3_paramTamp_ex1.php'>Continue banking with the Bank Of Fraud</a>";
    }
}

?>

</body>
</html>