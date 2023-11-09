<?php

// Qualtrics API Handler
include(get_template_directory() . '/qualtrics.php');
// Data Models
include(get_template_directory() . '/classes/Response.php');
include(get_template_directory() . '/classes/ResponseCollection.php');


try {
    $responseCollection = new ResponseCollection(getResponsesFromQualtrics());
} catch (Exception $e) {
    echo "Unable to make responseCollection";
}

function initCustomEndpoints(): void
{
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/responses', [
            'methods' => 'GET',
            'callback' => 'getResponses',
            'permission_callback' => '__return_true',
        ]);
    });
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/responsesSortedNewestFirst', [
            'methods' => 'GET',
            'callback' => 'getSortedResponsesNewestFirst',
            'permission_callback' => '__return_true',
        ]);
    });
}

function getResponses(): string
{
    global $responseCollection;
    return $responseCollection->getResponseJSON();
}

function getSortedResponsesNewestFirst(): string
{
    global $responseCollection;
    $responseCollection->sortResponsesByDate();
    return $responseCollection->getResponseJSON();
}

?>