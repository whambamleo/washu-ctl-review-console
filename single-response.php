<?php
/*
Template Name: Single Response
*/
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Parse the current URL and get the responseId parameter
        const urlParams = new URLSearchParams(window.location.search);
        const responseId = urlParams.get('responseId');
        console.log(responseId);
        fetchSingleResponse(responseId);
        fetchQuestions();

    });

    // Function to make a request to the /singleResponse endpoint
    async function fetchSingleResponse(responseId) {
        const baseURL = '/review-console/wp-json/console/v1/singleResponse';
        const endpointURL = `${baseURL}?responseId=${encodeURIComponent(responseId)}`;
        console.log(endpointURL);

        try {
            const response = await fetch(endpointURL);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            const data = await response.json();
            renderSingleResponse(JSON.parse(data));
        } catch (error) {
            console.error(error);
        }
    }

    // Function to make a request to the /questions endpoint
    async function fetchQuestions() {
        const endpointURL = '/review-console/wp-json/console/v1/questions';
        console.log(endpointURL);

        try {
            const response = await fetch(endpointURL);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            const data = await response.json();
            console.log(JSON.parse(data));
        } catch (error) {
            console.error(error);
        }
    }

    function renderSingleResponse(response) {
        const responseObj = JSON.parse(response);

        const responseContainer = document.getElementById('responseContainer');
        responseContainer.innerHTML = ''; // Clear existing content

        // Create and append HTML elements for each field
        const responseIdElement = document.createElement('p');
        responseIdElement.textContent = `Response ID: ${responseObj.responseId}`;
        responseContainer.appendChild(responseIdElement);

        const formStatusElement = document.createElement('p');
        formStatusElement.textContent = `Form Status: ${responseObj.formStatus}`;
        responseContainer.appendChild(formStatusElement);

        const formSubmissionDateElement = document.createElement('p');
        formSubmissionDateElement.textContent = `Form Submission Date: ${responseObj.formSubmissionDate}`;
        responseContainer.appendChild(formSubmissionDateElement);

        // Assuming formQuestionResponses is an object
        const formQuestionResponses = responseObj.formQuestionResponses;

        for (const key in formQuestionResponses) {
            if (formQuestionResponses.hasOwnProperty(key)) {
                const questionElement = document.createElement('p');
                questionElement.textContent = `${key}: ${formQuestionResponses[key]}`;
                responseContainer.appendChild(questionElement);
            }
        }
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

<!-- Top Banner -->
<div>
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
            <div class="container mt-4">
                <div class="row">
                    <div class="col" id="responseContainer"></div>
                </div>
            </div>
        </div>
</div>

