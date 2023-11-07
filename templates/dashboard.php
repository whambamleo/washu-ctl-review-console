<?php
include(get_template_directory().'/qualtrics.php');
include(get_template_directory().'/classes/Response.php');
include(get_template_directory().'/classes/ResponseCollection.php');

try {
    $responseCollection = new ResponseCollection(getResponses());
    ?>

    <div class="container-fluid">
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
                                <!-- Filtering Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Filter
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="filterDropdown">
                                        <a class="dropdown-item" href="#">Action</a>
                                        <a class="dropdown-item" href="#">Another action</a>
                                        <a class "dropdown-item" href="#">Something else</a>
                                    </div>
                                </div>

                                <!-- Sorting Dropdown -->
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdown"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
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
                                    <input class="form-check-input" type="radio" name="exampleRadios" id="radioOption1"
                                           value="option1">
                                    <label class="form-check-label" for="radioOption1">
                                        Option 1
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="exampleRadios" id="radioOption2"
                                           value="option2">
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
                        <div class="col"><?php echo $responseCollection->convertToCards(); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
} catch (Exception $e) {
    echo "Unable to make responseCollection";
}
?>
