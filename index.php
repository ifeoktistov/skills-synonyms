<?php
include "work.php";

$server_result = '';
if (isset($_POST['type'])) {
    $server_result = Work::processPOST();
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>skills-synonyms</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
</head>
<body>

<div class="container">
    <h3>Hello, skills-synonyms!</h3>

    <?php if ($server_result):?>
    <div class="alert alert-info">
        <h4>Server result: </h4>
        <?php echo $server_result; ?>
    </div>
    <?php endif;?>

    <div class="navbar">
        <div class="navbar-inner">
            <form class="navbar-form form-inline" method="post">
                <label class="checkbox inline">Type word:</label>
                <input type="text" class="span2" name="word">
                <input type="hidden" name="type" value="api-test">
                <button type="submit" class="btn">Test API</button>
            </form>
        </div>
    </div>

    <div class="navbar">
        <div class="navbar-inner">
            <form class="navbar-form form-inline" method="post">
                <label class="checkbox inline">List:</label><br/>
                <textarea rows="3" name="list"></textarea>
                <input type="hidden" name="type" value="list"><br/>
                <button type="submit" class="btn">Get Synonyms</button>
            </form>
        </div>
    </div>

    <div class="navbar">
        <div class="navbar-inner">
            <form class="navbar-form form-inline" method="post" enctype="multipart/form-data">
                <label class="checkbox inline">Input CSV file:</label>
                <input type="file" class="span6" name="file">
                <input type="hidden" name="type" value="csv-file"><br/>
                <button type="submit" class="btn">Parse File</button>
            </form>
        </div>
    </div>
</div>

<script src="http://code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>