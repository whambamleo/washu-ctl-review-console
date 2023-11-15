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
            renderResponses(JSON.parse(data), false); // Render the responses
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
            renderResponses(JSON.parse(data), false); // Render the sorted responses
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
            renderResponses(JSON.parse(data), false); // Render the responses
        } catch (error) {
            console.error(error);
        }
    }

    async function handleCheckboxChange(checkbox) {
        // Define the base URL for your custom endpoint
        const endpointURL = '/review-console/wp-json/console/v1/responsesGrouped';

        if (checkbox.checked) {
            console.log("ON");
            try {
                const response = await fetch(endpointURL);
                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }
                const data = await response.json();
                renderResponses(JSON.parse(data), true); // Render the responses
            } catch (error) {
                console.error(error);
            }
        } else {
            getResponses();
        }
    }

    function renderResponses(responses, isGrouped) {
        clearCardContainer();  // remove spinner before loading cards
        const cardContainer = document.getElementById('cardContainer');

        if (isGrouped) {
            console.log(responses["submitted"]);
            for (const formStatus in responses) {

                const group = document.createElement('div');
                group.className = 'cardGroupCustom';
                group.innerHTML = `<h3>${formStatus}</h3>`;

                const responseUnderStatus = responses[formStatus];
                for (const i in responseUnderStatus) {
                    const responseObj = JSON.parse(responseUnderStatus[i]);
                    const card = document.createElement('div');
                    card.className = 'card cardCustom';

                    card.innerHTML = `
                        <h5 class="card-title">${responseObj.formQuestionResponses.QID3_TEXT}</h5>
                        <p class="card-text">${responseObj.formQuestionResponses.QID4_TEXT}</p>
                        <p class="card-text text-muted" style="text-align: right;">${responseObj.formSubmissionDate}</p>
                        <div class="alert alert-danger text-right position-absolute" style="top: 0; right: 0; padding: 5px; margin: 5px;">
                        ${responseObj.formStatus}
                        </div>
                    `;
                    group.appendChild(card);
                }
                cardContainer.appendChild(group);
            }
        } else {
            // uncheck the groupby box because there is currently no sorting and filtering for grouped values.
            var checkbox = document.getElementById("flexSwitchCheckDefault");
            checkbox.checked = false;
            responses.forEach(response => {
                const responseObj = JSON.parse(response);
                const card = document.createElement('div');
                card.className = 'card cardCustom';

                card.innerHTML = `
                    <h5 class="card-title">${responseObj.formQuestionResponses.QID3_TEXT}</h5>
                    <p class="card-text">${responseObj.formQuestionResponses.QID4_TEXT}</p>
                    <p class="card-text text-muted" style="text-align: right;">${responseObj.formSubmissionDate}</p>
                    <div class="alert alert-danger text-right position-absolute" style="top: 0; right: 0; padding: 5px; margin: 5px;">
                    ${responseObj.formStatus}
                    </div>
                `;
                cardContainer.appendChild(card);
            });
        }
    }

</script>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">

<div>
    <!-- Top Banner -->
    <div class="header">
        <div class="headerLeft">
            <h2> CTL Review Console </h2>
        </div>
        <div class="headerRight">
            <button type="button" class="btn btn-lg headerButton">Qualtrics Dashboard</button>
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
                        <div class="toolbarLeft">
                            <div class="input-group mb-3" id="search">
                                <input type="text" class="form-control searchTextInput" aria-describedby="button-addon2" name="filterInput">
                                <button class="btn btn-outline-secondary" type="submit" id="button-addon2" onclick="filter(event)">Search</button>
                            </div>
                        </div>
                        <div class="toolbarRight">
                            <!-- Radio Buttons -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" onchange="handleCheckboxChange(this)">
                                <label class="form-check-label" for="flexSwitchCheckDefault"> Group by Status </label>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Sort By
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