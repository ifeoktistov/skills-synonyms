<?php
include "work.php";
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

    <?php if (isset($_POST['type'])):?>
    <div class="alert alert-info">
        <h4>Server result: </h4>
        <?php echo Work::processPOST(); ?>
    </div>
    <?php endif;?>

    <div class="navbar">
        <div class="navbar-inner">
            <form class="navbar-form form-inline" method="post">
                <label class="checkbox inline">Get synonyms:</label>
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
                <button type="submit" class="btn">Get synonyms</button>
            </form>
        </div>
    </div>



</div>

<script src="http://code.jquery.com/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
</body>
</html>