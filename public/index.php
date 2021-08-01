<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Service\Parser;

$parser = new Parser();

if (isset($_POST["submit"]) && isset($_FILES["csv"]['tmp_name']) && $_FILES["csv"]['tmp_name']) {
    $tmp_path = $_FILES["csv"]['tmp_name'];
    $csv = $parser->importCSV($tmp_path);
}

?>

<!doctype html>

<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Padua</title>
    <meta name="description" content="Padua">

    <link rel="icon" href="/favicon.ico">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="stylesheet" href="css/styles.css?v=1.0">

</head>

<body>
    <!-- your content here... -->
    <div style="font-size: 20px;">Upload new CSV</div>
    <form method="post" enctype="multipart/form-data">
        <div style="margin: 10px 0px;">
            <label for="csv">Select CSV to upload:</label>
        </div>
        <input type="file" id="csv" name="csv" accept="text/csv" required>
        <div style="margin-top: 20px;">
            <button name="submit">Upload CSV</button>
        </div>
    </form>

    <?php 
       if(isset($csv)){
           echo "<div style=\"font-size: 20px; margin-top: 20px;\">Bank Transactions from CSV</div>";
           echo $parser->renderTable($csv);
       }
    ?>

    <script src="js/scripts.js"></script>
</body>

</html>