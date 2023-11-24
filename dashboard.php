<?php
/*
Template Name: Dashboard
*/

?>

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

        // display the clear button
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

    function clearSearch(event) {
        document.querySelector('.searchTextInput').value = '';
        filter(event);
        // hide the button until a new search
        document.getElementById('clearButton').style.display = 'none';
    }

    function searchKeyWordUpdate(event) {
        let input = document.querySelector('.searchTextInput').value;
        if (!input) {
            document.getElementById('clearButton').style.display = 'none';
        } else {
            filter(event); 
            document.getElementById('clearButton').style.display = 'inline-block';
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
                const responseId = responseObj.responseId;
                const singleResponseURL = `${window.location.href}single-response/?responseId=${responseId}`;

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
                // Add a click event listener to the card
                card.addEventListener('click', function() {
                    routeToSingleResponse(singleResponseURL);
                });
            });
        }
    }
    function routeToSingleResponse(url) {
        window.location.href = url;
    }
</script>
<!--    FontAwesome CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css">
<!--    Bootstrap CSS TODO: upgrade everything to 5.2 -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
      integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
<!--    Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" i
      ntegrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<?php wp_head(); ?>

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
                                <input type="text" class="form-control searchTextInput" aria-describedby="button-addon2" name="filterInput" id="searchBar" oninput="searchKeyWordUpdate(event)">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="clearButton" onclick="clearSearch(event)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                    </svg>
                                    </button>
                                </div>
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

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script></script>
?>