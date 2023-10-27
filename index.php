<?php
include('qualtrics.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title> CTL Review Console </title>
    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your Site Description">
    <meta name="author" content="Your Name or Author">
<!--    <link rel="shortcut icon" href="images/logo.png">-->

    <!--    FontAwesome CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
    <!--    Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
          integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
    <!-- this loads style.css, but I think it also loads other stuff we don't need -->
<!--    --><?php //wp_head(); ?><!-- -->
</head>

<body>
    <p id="test"> CTL Review Console </p>
    <?php
    try {
        getResponses();
    } catch (Exception $e) {
    }
    echo "<p>";
    echo "</p>";
    ?>
</body>
</html>
