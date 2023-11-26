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
        renderContent(responseId);
    });

    async function getQuestionsAndResponses(responseId) {
        try {
            const [formResponses, formQuestions] = await Promise.all([
                fetchSingleResponse(responseId),
                fetchQuestions()
            ]);
            return [formResponses, formQuestions];
        } catch (error) {
            console.log(error);
        }
    }

    function matchQuestionsAndResponses(responses, questions) {
        console.log("responses: ", JSON.parse(responses));
        console.log("questions: ", questions.result.elements);
        questions = questions.result.elements;
        responses = JSON.parse(responses).formQuestionResponses;

        let questionsAndAnswers = {};
        for(let i = 0; i < questions.length; i++) {
            console.log(questions[i].QuestionDescription);
            // Form Questions with string responses come with an ID of "QIDxx_TEXT", but boolean responses come with just "QIDxx"
            if (responses.hasOwnProperty(questions[i].QuestionID)) {
                questionsAndAnswers[questions[i].QuestionDescription] = responses[questions[i].QuestionID];
            } else if (responses.hasOwnProperty(questions[i].QuestionID) + "_TEXT") {
                questionsAndAnswers[questions[i].QuestionDescription] = responses[questions[i].QuestionID + "_TEXT"];
            }
        }
        console.log(questionsAndAnswers);
        return questionsAndAnswers
    }

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
            return JSON.parse(data);
            // renderSingleResponse(JSON.parse(data));
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
            return JSON.parse(data);
            console.log(JSON.parse(data));
        } catch (error) {
            console.error(error);
        }
    }

    async function changeStatus(newStatus) {
        console.log("changing Status to: ", newStatus);
        // TODO: call the change status backend
    }

    async function deleteTicket() {
        console.log("deleting");
        Swal.fire({
            title: 'Delete Warning!',
            text: 'Are you sure you want to delete this ticket?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // TODO: call the delete backend
                Swal.fire('Confirmed!', 'Ticket was Deleted', 'success');
                // TODO: re-direct to dashboard
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire('Canceled', 'Your action was canceled', 'info');
            }
        });
    }

    function saveChanges() {
        console.log("saving");
        const changes = [];
        let form = document.getElementById("responseContainer");
        // get the labels and textarea responses of the form submission.
        // cannot use the names of the textareas because both the names and number
        // of elements in the form can change if the Qualtrics form changes.
        // loop goes till length-1 because save button is the last element.
        console.log(form.elements);
        for (let i = 0; i < form.elements.length - 1; i++) {
            let element = form.elements[i];
            if (element.tagName.toLowerCase() === "textarea") {
                changes.push(element.value);
            }
        }

        // TODO: call backend with changes so that it can save the changes

        // hide the saveChanges button
        const saveBtn = document.getElementById('saveChangesBtn');
        saveBtn.style.display = 'none';

        // show the edit button
        const editBtn = document.getElementById('editBtn');
        editBtn.style.display = 'inline-block';
        return false;
    }

    async function editTicket() {
        console.log("editing");
        // make all responses editable
        const responseElements = document.querySelectorAll('.response');
        responseElements.forEach(element => {
            element.removeAttribute('readonly');
        });

        // if there is no save button, create one
        const saveBtn = document.getElementById('saveChangesBtn');
        if (!saveBtn) {
            // create a save button. Should be wrapped in another div align right
            const responseContainer = document.getElementById('responseContainer');

            const submitBtnContainer = document.createElement('div');
            submitBtnContainer.classList.add('saveBtnContainer');

            const submitButton = document.createElement('button');
            submitButton.type = 'submit';
            submitButton.textContent = 'Save Changes';
            submitButton.classList.add('btn');
            submitButton.classList.add('btn-outline-secondary');
            submitButton.id = 'saveChangesBtn';
            submitBtnContainer.appendChild(submitButton);
            responseContainer.appendChild(submitBtnContainer);
        } else {
            saveBtn.style.display = 'inline-block';
        }

        // hide the edit button
        const editBtn = document.getElementById('editBtn');
        editBtn.style.display = 'none';
    }

    async function renderContent(responseId) {
        // Get both responses and questions.
        const [formResponses, formQuestions] = await getQuestionsAndResponses(responseId);

        // Match questions with answers.
        const questionsAndResponses = matchQuestionsAndResponses(formResponses, formQuestions);
        renderSingleResponse(questionsAndResponses, formResponses);
    }

    function renderSingleResponse(formInfo, fullResponse) {
        const responseObj = JSON.parse(fullResponse);
        const responseContainer = document.getElementById('responseContainer');
        responseContainer.innerHTML = ''; // Clear existing content

        // Create and append HTML elements for each field
        const statusHeader = document.getElementById('statusHeader');
        statusHeader.innerHTML = `Status: ${responseObj.formStatus}`;

        for (const key in formInfo) {
            if (formInfo.hasOwnProperty(key)) {
                const questionElement = document.createElement('label');
                questionElement.classList.add('question');
                questionElement.textContent = `${key.slice(0, -1)}`;
                questionElement.setAttribute('for', key);
                responseContainer.appendChild(questionElement);

                const responseElement = document.createElement('textarea');
                responseElement.classList.add('response');
                // make the responses readonly until the edit button is clicked.
                responseElement.setAttribute('readonly', true);
                responseElement.setAttribute('name', key);
                responseElement.value = `${formInfo[key]}`;
                responseElement.style.width = '100%';
                responseContainer.appendChild(responseElement);

                // Make sure that the text area is long enough to display all of the content
                responseElement.style.height = (responseElement.scrollHeight) + 'px';
            }
        }

        const formSubmissionDateElement = document.createElement('p');
        formSubmissionDateElement.textContent = `${responseObj.formSubmissionDate}`;
        responseContainer.appendChild(formSubmissionDateElement);
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
                <div class="row justify-content-center">
                    <div class="col-md-7" id="Headers">
                        <div class="subHeaderLeft">
                            <h1 id="statusHeader"></h1>
                        </div>
                    </div>
                    <div class="col-md-3" id="Headers">
                        <div class="subHeaderRight">
                            <div class="dropdown">
                                  <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="sortDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Change Status
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <a class="dropdown-item" onclick="changeStatus(`Submitted`)">Submitted</a>
                                        <a class="dropdown-item" onclick="changeStatus(`ToolConsultation`)">Tool Consultation</a>
                                        <a class="dropdown-item" onclick="changeStatus(`ToolExploration`)">Tool Exploration</a>
                                        <a class="dropdown-item" onclick="changeStatus(`ToolTesting`)">Tool Testing</a>
                                        <a class="dropdown-item" onclick="changeStatus(`ProposalForFunding`)">Report/Proposal for Funding</a>
                                        <a class="dropdown-item" onclick="changeStatus(`GovernanceRecommendation`)">Governance Recommendation</a>
                                        <a class="dropdown-item" onclick="changeStatus(`ServiceNow`)">Tool Review Via ServiceNow</a>
                                    </div>
                            </div>
                            <button class="btn btn-outline-secondary" id="editBtn" onclick="editTicket()"> Edit </button>
                            <button class="btn btn-danger" id="button-addon2" onclick="deleteTicket()"> Delete </button>
                        </div>
                   </div>
              </div>
              <div class="row justify-content-center">
                  <div class="col-md-10">
                        <form id="responseContainer" action="" method="post" onsubmit="return saveChanges()">

                        </form>
                  </div>
              </div>
            </div>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script></script>
