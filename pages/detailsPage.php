<?php
// Include the necessary classes and configuration
require_once 'ResponseDetails.php'; // Adjust the path as necessary
$config = require 'qualtrics_config.php'; // Adjust the path as necessary

$responseId = $_GET['responseId'] ?? null; // Get the response ID from the URL
$details = null;

if ($responseId) {
    try {
        $responseDetails = new ResponseDetails($responseId, $config['x-api-token'], $config['dataCenterId'], $config['surveyId']);
        $details = $responseDetails->getDetails();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        // Handle the error appropriately
    }
} else {
    echo "No response ID provided.";
    // Handle the case where no ID is provided
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Response Details</title>
    <!-- Include your stylesheets here -->
</head>
<body>
    <!-- Top Banner -->
    <div class="header">
        <!-- Header content similar to dashboard.php -->
    </div>

    <!-- Main Content Section -->
    <div class="row">
        <!-- Left Sidebar -->
        <div class="col-md-2 sidebar">
            <!-- Sidebar content similar to dashboard.php -->
        </div>

        <!-- Center Content -->
        <div class="col-md-10">
            <!-- Details Content -->
            <div class="container mt-4">
                <div class="row">
                    <div class="col">
                        <?php
                        // Check if we have details to display
                        if ($details) {
                            echo '<div class="qualtrics-response-details">';
                            // Iterate through details and display them
                            foreach ($details as $questionId => $responseText) {
                                if (preg_match('/^QID[3-8]_TEXT$/', $questionId)) {
                                    echo "<div class='question'>";
                                    echo "<strong>" . htmlspecialchars($questionId) . ":</strong> ";
                                    echo "<p>" . htmlspecialchars($responseText) . "</p>";
                                    echo "</div>";
                                }
                            }
                            echo '</div>';
                        } else {
                            echo '<p>No details available for this response.</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
