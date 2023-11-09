<script>
    function clearCardContainer() {
        document.getElementById("cardContainer").innerHTML = '';
    }

    function getResponses() {
        clearCardContainer();

        // Define the URL for your custom endpoint
        const endpointURL = '/washu-ctl-review-console/wp-json/console/v1/responses';

        // Make a GET request to the custom endpoint
        fetch(endpointURL)
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Failed to fetch data');
                }
            })
            .then(data => {
                // Handle the response data
                console.log(data);
            })
            .catch(error => {
                console.error(error);
            });
    }

    function getSortedResponsesNewestFirst() {
        clearCardContainer();

        // Define the URL for your custom endpoint
        const endpointURL = '/washu-ctl-review-console/wp-json/console/v1/responsesSortedNewestFirst';

        // Make a GET request to the custom endpoint
        fetch(endpointURL)
            .then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Failed to fetch data');
                }
            })
            .then(data => {
                // Handle the response data
                console.log(data);
            })
            .catch(error => {
                console.error(error);
            });
    }

</script>
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
                        <button class="btn btn-primary" onclick="getResponses()"> GET responses </button>
                        <button class="btn btn-primary" onclick="getSortedResponsesNewestFirst()"> GET sorted responses newest first </button>
                        <form class="d-flex justify-content-end">
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
                            <!-- Filtering Dropdown -->
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="filterDropdown"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Filter
                                </button>
                                <div class="dropdown-menu" aria-labelledby="filterDropdown">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else</a>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdown"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Sort
                                </button>
                                <div class="dropdown-menu" aria-labelledby="sortDropdown">
                                    <a class="dropdown-item" href="#" onclick="">Newest
                                        First</a>
                                    <a class="dropdown-item" href="#" onclick="">
                                        Oldest First </a>
                                    <a class="dropdown-item" href="#" onclick="">
                                        Alphabetically
                                        by ID </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Cards -->
            <div class="container mt-4">
                <div class="row">
                    <div class="col" id="cardContainer"></div>
                </div>
            </div>
        </div>
    </div>
</div>