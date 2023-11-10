<script>
    document.addEventListener('DOMContentLoaded', getResponses);
    let filter_choice;

    function clearCardContainer() {
        document.getElementById("cardContainer").innerHTML = '';
    }

    function clearCardContainerAndAddSpinner() {
        document.getElementById("cardContainer").innerHTML = '';

        const spinnerDiv = document.createElement("div");
        spinnerDiv.className = "spinner-border";
        spinnerDiv.setAttribute("role", "status");

        const spinnerText = document.createElement("span");
        spinnerText.className = "sr-only";
        spinnerText.textContent = "Loading...";

        spinnerDiv.appendChild(spinnerText);

        document.getElementById("cardContainer").appendChild(spinnerDiv);
    }

    async function getResponses() {
        clearCardContainerAndAddSpinner();

        // Define the URL for your custom endpoint
        const endpointURL = '/review-console/wp-json/console/v1/responses';

        try {
            const response = await fetch(endpointURL);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            const data = await response.json();
            renderResponses(JSON.parse(data)); // Render the responses
        } catch (error) {
            console.error(error);
        }
    }

    async function getSortedResponsesNewestFirst() {
        clearCardContainerAndAddSpinner();

        // Define the URL for your custom endpoint
        const endpointURL = '/review-console/wp-json/console/v1/responsesSortedNewestFirst';

        try {
            const response = await fetch(endpointURL);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            const data = await response.json();
            renderResponses(JSON.parse(data)); // Render the sorted responses
        } catch (error) {
            console.error(error);
        }
    }

    async function filter(event) {
        event.preventDefault();
        const filterInputValue = document.querySelector('input[name="filterInput"]').value;

        // Define the base URL for your custom endpoint
        const baseURL = '/review-console/wp-json/console/v1/responsesFiltered';

        // Construct the URL with query parameters manually
        const endpointURL = `${baseURL}?filterInput=${encodeURIComponent(filterInputValue)}`;

        try {
            const response = await fetch(endpointURL);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            const data = await response.json();
            renderResponses(JSON.parse(data)); // Render the responses
        } catch (error) {
            console.error(error);
        }
    }

    function renderResponses(responses) {
        clearCardContainer();  // remove spinner before loading cards
        const cardContainer = document.getElementById('cardContainer');
        responses.forEach(response => {
            const responseObj = JSON.parse(response); // Parse the JSON string to an object

            const card = document.createElement('div');
            card.className = 'card';
            card.style.width = '50rem';

            card.innerHTML = `
                <div class="card-body">
                    <h5 class="card-title">${responseObj.formQuestionResponses.QID3_TEXT}</h5>
                    <p class="card-text">${responseObj.formQuestionResponses.QID4_TEXT}</p>
                    <p class="card-text text-muted" style="text-align: right;">${responseObj.formSubmissionDate}</p>
                    <p class="card-text text-right position-absolute" style="top: 0; right: 0; border: 1px solid green; border-radius: 10px; padding: 5px; margin: 5px;">${responseObj.formStatus}</p>
                </div>
                `;

            cardContainer.appendChild(card);
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
                            <div id="search">
                                <input type="text" name="filterInput">
                                <input type="submit" value="Search" onclick="filter(event)">
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdown"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Sort By
                                </button>
                                <div class="dropdown-menu" aria-labelledby="sortDropdown">
                                    <a class="dropdown-item" href="#" onclick="getSortedResponsesNewestFirst()">Newest
                                        First</a>
                                    <a class="dropdown-item" href="#" onclick="getResponses()">
                                        Oldest First </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Cards -->
            <div class="container mt-4">
                <div class="row">
                    <div class="col" id="cardContainer">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>