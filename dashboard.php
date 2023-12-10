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

    async function clearCardContainerAndAddSpinner() {
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
        // TODO: update to new site name
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
        // TODO: update to new site name
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
        // TODO: update to new site name
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

    async function handleGroupByChange(checkbox) {
        // Define the base URL for your custom endpoint
        // TODO: update to new site name
        const endpointURL = '/review-console/wp-json/console/v1/responsesGrouped';

        if (checkbox.checked) {
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

    async function handleArchivedChange(checkbox) {
        const endpointURL = '/review-console/wp-json/console/v1/responsesArchived';
        if (checkbox.checked) {
            try {
                const response = await fetch(endpointURL);
                if (!response.ok) {
                    throw new Error('Failed to fetch data');
                }
                const data = await response.json();
                renderResponses(JSON.parse(data), false, true); // Render the responses
            } catch (error) {
                console.error(error);
            }
        } else {
            getResponses();
        }


    }

    async function resetCache() {
        clearCardContainerAndAddSpinner();
        // Define the base URL for your custom endpoint
        // TODO: update to new site name
        const endpointURL = '/review-console/wp-json/console/v1/resetCache';

        try {
            const response = await fetch(endpointURL);
            if (!response.ok) {
                throw new Error('Failed to reset wordpress cache');
            }
            window.location.reload();
        } catch (error) {
            console.error(error);
        }
    }

    function renderResponses(responses, isGrouped, isArchived) {
        clearCardContainer();  // remove spinner before loading cards
        const cardContainer = document.getElementById('cardContainer');

        if (isGrouped) {
            for (const formStatus in responses) {
                const group = document.createElement('div');
                group.className = 'cardGroupCustom';
                group.innerHTML = `<h3>${formStatus}</h3>`;

                const responseUnderStatus = responses[formStatus];
                for (const i in responseUnderStatus) {
                    const responseObj = JSON.parse(responseUnderStatus[i]);
                    const responseId = responseObj.responseId;
                    let url = new URL(window.location.href);
                    const singleResponseURL = `${url.protocol}//${url.host}/review-console/single-response/?responseId=${responseId}`;
                    const card = document.createElement('div');
                    card.className = 'card cardCustom';

                    card.innerHTML = `
                        <h5 class="card-title">${responseObj.formQuestionResponses.QID3_TEXT}</h5>
                        <p class="card-text">${responseObj.formQuestionResponses.QID4_TEXT}</p>
                        <p class="card-text text-muted" style="text-align: right;">${responseObj.readableFormSubmissionDate}</p>
                        <div class="alert alert-danger text-right position-absolute" style="top: 0; right: 0; padding: 5px; margin: 5px;">
                        ${responseObj.formStatus}
                        </div>
                    `;
                    // Add a click event listener to the card
                    card.addEventListener('click', function() {
                        routeToSingleResponse(singleResponseURL);
                    });
                    group.appendChild(card);
                }
                cardContainer.appendChild(group);
            }
        } else {
            // uncheck the groupby box because there is currently no sorting and filtering for grouped values.
            var checkbox = document.getElementById("flexSwitchCheckDefault");
            checkbox.checked = false;
            if (!isArchived) {
                var checkbox = document.getElementById("flexSwitchCheckDefaultArchived");
                checkbox.checked = false;
            }
            responses.forEach(response => {
                const responseObj = JSON.parse(response);
                const responseId = responseObj.responseId;
                let url = new URL(window.location.href);
                const singleResponseURL = `${url.protocol}//${url.host}/review-console/single-response/?responseId=${responseId}`;

                const card = document.createElement('div');
                card.className = 'card cardCustom';

                card.innerHTML = `
                    <h5 class="card-title">${responseObj.formQuestionResponses.QID3_TEXT}</h5>
                    <p class="card-text">${responseObj.formQuestionResponses.QID4_TEXT}</p>
                    <p class="card-text text-muted" style="text-align: right;">${responseObj.readableFormSubmissionDate}</p>
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

    function openKnowledgeBase() {
        window.location.href = `${window.location.href}knowledge-base`;
    }

    function returnToHome() {
        window.location.reload();
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
            <img src="https://yabctl.wpenginepowered.com/wp-content/uploads/2023/12/CTL_logo.png" alt="CTL_logo.png" onclick="returnToHome()" width="400" height="110">
        </div>
        <div class="headerRight">
            <a href="https://wustl.az1.qualtrics.com/jfe/form/SV_3EG37AU36cEEDRA" target="_blank">
                <button type="button" class="btn btn-lg headerButton">Qualtrics Dashboard</button>
            </a>
            <button type="button" class="btn btn-lg headerButton" onclick="openKnowledgeBase()">Knowledge Base</button>
        </div>
    </div>
    <!-- Main Content Section -->
    <div class="row">
        <!-- Center Content -->
        <div class="col-md-12">
            <!-- Dropdowns, Radio Buttons, and Cards -->
            <div class="container mt-4">
                <div class="row">
                    <div class="col-12 mt-3 toolbar">
                        <div class="toolbarLeft">
                            <div class="input-group mb-3" id="search">
                                <button class="search-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"/>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                    </svg>
                                </button>
                                <input type="text" class="form-control searchTextInput" aria-describedby="button-addon2" name="filterInput" id="searchBar" oninput="searchKeyWordUpdate(event)">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" id="clearButton" onclick="clearSearch(event)">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                                    </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="toolbarRight">
                            <!-- Radio Buttons -->
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefaultArchived" onchange="handleArchivedChange(this)">
                                <label class="form-check-label" for="flexSwitchCheckDefault"> Show Archived Only  </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault" onchange="handleGroupByChange(this)">
                                <label class="form-check-label" for="flexSwitchCheckDefault"> Group by Status </label>
                            </div>

                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Sort By
                                </button>
                                <div class="dropdown-menu" aria-labelledby="sortDropdown">
                                    <a class="dropdown-item" onclick="getSortedResponsesNewestFirst()">Newest
                                        First</a>
                                    <a class="dropdown-item" onclick="getResponses()">
                                        Oldest First </a>
                                </div>
                            </div>

                            <button class="reset-button" onclick="resetCache()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M23 4v6h-6"></path>
                                    <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                                </svg>
                            </button>
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
