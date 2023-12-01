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
        questions = questions.result.elements;
        responses = JSON.parse(responses).formQuestionResponses;

        let questionsAndAnswers = {};
        for(let i = 0; i < questions.length; i++) {
            // Form Questions with string responses come with an ID of "QIDxx_TEXT", but boolean responses come with just "QIDxx"
            if (responses.hasOwnProperty(questions[i].QuestionID)) {
                questionsAndAnswers[questions[i].QuestionDescription] = responses[questions[i].QuestionID];
            } else if (responses.hasOwnProperty(questions[i].QuestionID) + "_TEXT") {
                questionsAndAnswers[questions[i].QuestionDescription] = responses[questions[i].QuestionID + "_TEXT"];
            }
        }
        return questionsAndAnswers
    }

    // Function to make a request to the /setEmbeddedData endpoint
    async function setEmbeddedData(fieldName, fieldValue) {
        const urlParams = new URLSearchParams(window.location.search);
        const responseId = urlParams.get('responseId');
        const baseURL = '/review-console/wp-json/console/v1/setEmbeddedData';
        const endpointURL = `${baseURL}?responseId=${encodeURIComponent(responseId)}&fieldName=${encodeURIComponent(fieldName)}&fieldValue=${encodeURIComponent(fieldValue)}`;

        try {
            const response = await fetch(endpointURL);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            const data = await response.json();
            window.location.reload();
        } catch (error) {
            console.error(error);
        }
    }

    // checks if the embeddedData to be changed is archiving and alerts users.
    async function checkBeforeSettingEmbeddedData(fieldName, fieldValue) {
        if (fieldName === 'archived' && fieldValue === 'true') {
            Swal.fire({
                title: 'Archive?',
                text: 'Are you sure you want to archive this response?',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'Archive',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // TODO: call the delete backend
                    setEmbeddedData(fieldName, fieldValue);
                    Swal.fire('Confirmed!', 'Response has been archived', 'success');
                    // TODO: re-direct to dashboard
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    Swal.fire('Canceled', 'Response has not been archived', 'info');
                }
            });
        } else {
            setEmbeddedData(fieldName, fieldValue);
        }
    }

    // Function to make a request to the /singleResponse endpoint
    async function fetchSingleResponse(responseId) {
        const baseURL = '/review-console/wp-json/console/v1/singleResponse';
        const endpointURL = `${baseURL}?responseId=${encodeURIComponent(responseId)}`;

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

        try {
            const response = await fetch(endpointURL);
            if (!response.ok) {
                throw new Error('Failed to fetch data');
            }
            const data = await response.json();
            return JSON.parse(data);
        } catch (error) {
            console.error(error);
        }
    }

    async function changeStatus(newStatus) {
        const statusHeader = document.getElementById("statusHeader");
        statusHeader.innerHTML = `Status: ${newStatus}`;
        // TODO: call the change status backend
    }

    async function deleteResponse() {
        const urlParams = new URLSearchParams(window.location.search);
        const responseId = urlParams.get('responseId');

        if (!responseId) {
            alert('Response ID is missing.');
            return;
        }

        Swal.fire({
            title: 'Delete Warning!',
            text: 'Are you sure you want to delete this response?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Delete',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`/review-console/wp-json/console/v1/deleteResponse?responseId=${encodeURIComponent(responseId)}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire('Confirmed!', 'Response was deleted successfully', 'success');
                        window.location.href = 'https://yujunectl.wpenginepowered.com/review-console/dashboard/'; // Updated redirect URL
                    } else {
                        Swal.fire('Failed!', 'Failed to delete response', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'An error occurred while deleting the response', 'error');
                });
            } else if (result.dismiss === Swal.DismissReason.cancel) {
                Swal.fire('Cancelled', 'Your action was cancelled', 'info');
            }
        });
    }


    function saveChanges() {
    const form = document.getElementById("responseContainer");
    const responseId = new URLSearchParams(window.location.search).get('responseId');
    const updates = {};

    // Collect updates from the form elements
    for (let i = 0; i < form.elements.length - 1; i++) {
        let element = form.elements[i];
        if (element.tagName.toLowerCase() === "textarea" && element.name) {
            updates[element.name] = element.value;
        }
    }

    // Make the API request to update the response
    fetch('/review-console/wp-json/console/v1/editResponse', {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            responseId: responseId,
            updates: updates
        }),
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            console.log('Changes saved successfully!');
            // Additional logic on successful save can be added here
        } else {
            console.error('Failed to save changes:', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });

    // UI Feedback
    document.getElementById('saveChangesBtn').style.display = 'none';
    document.getElementById('editBtn').style.display = 'inline-block';
    document.querySelectorAll('.response').forEach(element => {
        element.setAttribute('readonly', true);
    });

    return false; // Prevent form submission
}


    // function saveChanges() {
    //     // const changes = [];
    //     // const form = document.getElementById("responseContainer");
    //     // const responseId = new URLSearchParams(window.location.search).get('responseId');
    //     // const updatedResponses = {};
    //     // get the labels and textarea responses of the form submission.
    //     // cannot use the names of the textareas because both the names and number
    //     // of elements in the form can change if the Qualtrics form changes.
    //     // loop goes till length-1 because save button is the last element.

    //     const form = document.getElementById("responseContainer");
    //     const responseId = new URLSearchParams(window.location.search).get('responseId');
    //     const updates = {};
    //     // for (let i = 0; i < form.elements.length - 1; i++) {
    //     //     let element = form.elements[i];
    //     //     if (element.tagName.toLowerCase() === "textarea") {
    //     //         changes.push(element.value);
    //     //     }
    //     // }

    //     for (let i = 0; i < form.elements.length -1; i++) {
    //         let element = form.elements[i];
    //         if (element.tagName.toLowerCase() === "textarea" && element.name) {
    //             updates[element.name] = element.value;
    //         }
    //     }

        

    //     // TODO: call backend with changes so that it can save the changes
    //     console.log(updates);
    //     // hide the saveChanges button
    //     const saveBtn = document.getElementById('saveChangesBtn');
    //     saveBtn.style.display = 'none';

    //     // show the edit button
    //     const editBtn = document.getElementById('editBtn');
    //     editBtn.style.display = 'inline-block';

    //     // make all responses readonly
    //     const responseElements = document.querySelectorAll('.response');
    //     responseElements.forEach(element => {
    //         element.setAttribute('readonly', true);
    //     });

    //     fetch('/review-console/wp-json/console/v1/editResponse', {
    //         method: 'POST',
    //         headers: {
    //             'Content-Type': 'application/json',
    //         },
    //         body: JSON.stringify({
    //             responseId: responseId,
    //             updates: updates
    //         }),
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.success) {
    //             console.log('Changes saved successfully!');
    //         } else {
    //             console.error('Failed to save changes: ' + data.message);
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error:', error);
    //     });

    //     return false;
    // }

    async function editResponse() {
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
        console.log(responseObj);
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

        // update the archive dropdown to reflect archiving status
        const archived = document.getElementById('archiveDropdown');
        archived.innerHTML = responseObj.archived === 'true' ? 'Archived' : 'Not Archived';
        console.log(archived.value);
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
                            <div class="dropdown">
                                  <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="archiveDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Archive
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="sortDropdown">
                                        <a class="dropdown-item" onclick="checkBeforeSettingEmbeddedData('archived','true')">Archive</a>
                                        <a class="dropdown-item" onclick="checkBeforeSettingEmbeddedData('archived','false')">Unarchive</a>
                                    </div>
                            </div>
                            <button class="btn btn-outline-secondary" id="editBtn" onclick="editResponse()"> Edit </button>
                            <button class="btn btn-danger" id="button-addon2" onclick="deleteResponse()"> Delete </button>
                        </div>
                   </div>
              </div>
              <div class="row justify-content-center">
                  <div class="col-md-10" id="mainResponseContent">
                        <h1 id="statusHeader"></h1>
                        <form id="responseContainer" action="" method="put" onsubmit="return saveChanges()">

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
