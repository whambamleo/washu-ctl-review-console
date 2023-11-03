<?php
include('qualtrics.php');
include('classes/Response.php');
include('classes/ResponseCollection.php')
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
    <?php
    try {
        $responseCollection = new ResponseCollection(getResponses());
        echo '<div class="container-fluid">
            <!-- Top Banner -->
            <div class="row bg-primary text-white p-3">
                <div class="col">
                    <h1>CTL Review Console</h1>
                </div>
            </div>

            <!-- Main Content Section -->
            <div class="row">
                <!-- Left Sidebar -->
                <div class="col-md-2 bg-light">
                    <ul>
                        <li>Item 1</li>
                        <li>Item 2</li>
                        <li>Item 3</li>
                        <li>Item 4</li>
                        <!-- Add more sidebar items as needed -->
                    </ul>
                </div>

                <!-- Center Content -->
                <div class="col-md-10">
                    <!-- Dropdowns, Radio Buttons, and Cards -->
                    <div class="container mt-4">
                        <div class="row">
                            <div class="col-12 mt-3">
                                <form class="d-flex justify-content-end">

                                    <!-- filtering dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Filter
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="filterDropdown">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else</a>
                                        </div>
                                    </div>
                                    
                                    <!-- sorting dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Sort
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="sortDropdown">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else</a>
                                        </div>
                                    </div>

                                    <!-- Radio Buttons -->
                                    <div class="form-check me-2">
                                        <input class="form-check-input" type="radio" name="exampleRadios" id="radioOption1" value="option1">
                                        <label class="form-check-label" for="radioOption1">
                                            Option 1
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="exampleRadios" id="radioOption2" value="option2">
                                        <label class="form-check-label" for="radioOption2">
                                            Option 2
                                        </label>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Cards -->
                    <div class="container mt-4">
                        <div class="row">
                            <div class="col">' . $responseCollection->convertToCards() . '</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    } catch (Exception $e) {
        echo "Unable to make responseCollection";
    }
    echo '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
      <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>';
    ?>
</body>
</html>
