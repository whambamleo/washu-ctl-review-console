<script>
    document.addEventListener('DOMContentLoaded', getResponses);

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
        const endpointURL = '/-console/wp-json/console/v1/responsesSortedNewestFirst';

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


    function renderResponses(responses) {
        clearCardContainer();  // remove spinner before loading cards
        const cardContainer = document.getElementById('cardContainer');
        responses.forEach(response => {
            const responseObj = JSON.parse(response); // Parse the JSON string to an object
    
            const card = document.createElement('div');
            card.className = 'card cardCustom';
    
            // Construct the card HTML
            card.innerHTML = `
                <div class="card-body">
                    <h5 class="card-title">${responseObj.formQuestionResponses.QID3_TEXT}</h5>
                    <p class="card-text">${responseObj.formQuestionResponses.QID4_TEXT}</p>
                    <p class="card-text text-muted" style="text-align: right;">${responseObj.formSubmissionDate}</p>
                    <div class="alert alert-danger text-right position-absolute" style="top: 0; right: 0; padding: 5px; margin: 5px;">
                      ${responseObj.formStatus}
                    </div>
                </div>
              `;
    
            // Create and append the 'View Details' button
            const detailsButton = document.createElement('a');
            detailsButton.href = `pages/detailsPage.php?responseId=${responseObj.responseId}`;
            detailsButton.target = '_blank';
            detailsButton.className = 'btn btn-primary';
            detailsButton.textContent = 'View Details';
            card.querySelector('.card-body').appendChild(detailsButton);
    
            cardContainer.appendChild(card);
        });
    }
    

</script>
<div>
    <!-- Top Banner -->
    <div class="header">
        <div class="headerLeft">
            <h2> CTL Review Console </h2>
        </div>
        <div class="headerRight">
            <button type="button" class="btn btn-lg headerButton">Qualtrics Dashboar1</button>
            <button type="button" class="btn btn-lg headerButton">Help</button>
        </div>
    </div>
    <!-- Main Content Section -->
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-2 sidebar">
            <ul class="list-group sidebarList">
                <li class="list-group-item sidebarListItem">Cras justo odio</li>
                <li class="list-group-item sidebarListItem">Dapibus ac facilisis in</li>
                <li class="list-group-item sidebarListItem">Morbi leo risus</li>
                <li class="list-group-item sidebarListItem">Porta ac consectetur ac</li>
                <li class="list-group-item sidebarListItem">Vestibulum at eros</li>
            </ul>
        </div>
        <!-- Center Content -->
        <div class="col-md-10">
            <!-- Dropdowns, Radio Buttons, and Cards -->
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12 mt-3 toolbar">
                        <!-- Radio Buttons -->
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                            <label class="form-check-label" for="flexSwitchCheckDefault"> Group by Status </label>
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
                                <a class="dropdown-item" href="#" onclick="getSortedResponsesNewestFirst()">Newest
                                    First</a>
                                <a class="dropdown-item" href="#" onclick="getResponses()">
                                    Oldest First </a>
                            </div>
                        </div>
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