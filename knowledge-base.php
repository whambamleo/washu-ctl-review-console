<?php
/*
Template Name: Knowledge Base
*/
?>
<script>
    function returnToHome() {
        let url = new URL(window.location.href);
        window.location.href = `${url.protocol}//${url.host}/review-console/dashboard`;
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
        <img src="https://yabctl.wpenginepowered.com/wp-content/uploads/2023/12/CTL_logo.png" alt="CTL_logo.png" onclick="returnToHome()" width="400" height="110">
        </div>
        <div class="headerRight">
            <a href="https://wustl.az1.qualtrics.com/jfe/form/SV_3EG37AU36cEEDRA" target="_blank">
                <button type="button" class="btn btn-lg headerButton">Qualtrics Dashboard</button>
            </a>
        </div>
    </div>
    <!-- Main Content Section -->
    <div class="row">
        <!-- Center Content -->
        <div class="container">
        <section>
            <h2>1. How to Use the System</h2>
            <ol>
                <li>Once a response has been submitted, it takes a few minutes for Qualtrics to update 
                    the list of forms that has been submitted and be displayed in the page. After the changes 
                    have been saved on Qualtrics, the review console will gather the changes which could take upto
                    an hour. If you would like to see the responses as early as possible, please refer to the
                    "Cache and Reset Button" section below.
                </li>
                <li>Once a response is rendered on the dashboard, simply click on it to view the details of the response. 
                    It will redirect to a new page containing a more detailed view of the response.</li>
            </ol>
        </section>

        <section>
            <h2>2. Precautions when Updating the Qualtrics Form</h2>
            <p>While updating the contents of the form, please be cautious as this console expects the following to remain stable:</p>
            <ol>
                <li>First question of the Qualtrics survey should always be the full name of the user.</li>
                <li>Second question should always be the name of the tool they are suggesting.</li>
            </ol>
            <p>The titles of these fields are free to change.</p>
        </section>

        <section>
            <h2>3. How to make changes</h2>

            <h3>3.1 Updating Qualtrics Responses (Non-Embedded)</h3>
            <p>The user cannot directly change the recorded responses through this website. To change the responses, 
                the user must manually change the responses by locating it at the Qualtrics end and change it there.</p>

            <h3>3.2 Updating Qualtrics Responses (Embedded)</h3>
            <p>Within the current implementation, three embedded form fields have been used, "formStatus", "comments", and "archived"
                currently, "formStatus" has the following options:  
                Updating the status value options:</p>
            <ul>
                <li>Submitted</li>
                <li>Tool Consultation</li>
                <li>Tool Exploration</li>
                <li>Tool Testing</li>
                <li>Report/Proposal for Funding</li>
                <li>Governance Recommendation</li>
                <li>Tool Review Via ServiceNow</li>
            </ul>
            <p>In case of an update to the names of these statuses, their order, addition of new statuses, removal of statuses,... 
                ensure that the following changes are made:
            </p>
            <ol>
                <li>The "formStatus" embedded data reflects the changes</li>
                <li>The dropdown in single-response.php is updated to reflect the new statuses</li>
                <li>Update the predefinedValues variable in ResponseCollection.php (This array has predefined keys so that it can easily maintain the correct order of statuses on the dashboard. In other words, after grouping by formStatus, this order is enforced on the groups.)</li>
            </ol>
        </section>

            <section>
                <h2>4. Architecture</h2>
                <p>
                    qualtrics.php contains a library of generic functions that interact with the
                    Qualtrics API, including pulling reponses, questions and setting embedded
                    data fields. Our backend endpoints are the communication layer between the
                    Wordpress front-end and the Qualtrics database. More specifically, the
                    Wordpress templates are defined in dashboard.php and single-responses.php,
                    which are wired to JavaScript functions that signals backend-endpoint calls
                    defined in backend-endpoints.php (which also registers the custom REST API
                    with route console/v1). These calls are tied to the API methods in
                    Qualtrics.php that make API calls to the Qualtrics DB.
                </p>
            </section>

        <section>
            <h2>5. Cache and Reset Button</h2>
            <p>Instead of having a Database, we pull information from Qualtrics Data base and display it. To enhance the efficiency
                of the console, we store this information in the website’s cache. The cache is automatically updated every hour, i.e
                all changes that were made to the responses on the Qualtrics database will be reflected every hour. 
                This allows the page to load the responses in real-time when using features such as search, sort, and group by. 
                </p>
            <p>If it is necessary that new responses and changes are reflect in under an hour, please use the reset button in the right hand 
                side of the toolbar to reset the cache with the current data in Qualtrics which only takes a few seconds.</p>
        </section>

        <section>
            <h2>6. Comment Section</h2>
            <p>Within the details page of the responses is a comment section which is part of the embedded data. 
                Each response has its own comment textbox that can be updated to reflect the most up to date information on that
                response. Caution: this feature does not trach history of comments. Therefore, it could be helpful to 
                leave comments along with the commenter’s name and date if applicable and add to but not replace previous comments.</p>
        </section>
    </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script></script>
