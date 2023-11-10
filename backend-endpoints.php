<?php

// Qualtrics API Handler
include(get_template_directory() . '/qualtrics.php');
// Data Models
include(get_template_directory() . '/classes/Response.php');
include(get_template_directory() . '/classes/ResponseCollection.php');

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
    $responseCollection = get_transient('response_collection');

    if (false === $responseCollection) {
        try {
            $responseCollection = new ResponseCollection(getResponsesFromQualtrics());
            // Cache the object for 1 hour
            set_transient('my_response_collection', $responseCollection, 60 * 60);
        } catch (Exception $e) {
            return "Unable to make responseCollection";
        }
    }

    return $responseCollection->getResponseJSON();
}

function getSortedResponsesNewestFirst(): string
{
    $responseCollection = get_transient('response_collection');

    if (false === $responseCollection) {
        try {
            $responseCollection = new ResponseCollection(getResponsesFromQualtrics());
            // Cache the object for 1 hour
            set_transient('my_response_collection', $responseCollection, 60 * 60);
        } catch (Exception $e) {
            return "Unable to make responseCollection";
        }
    }

    $responseCollection->sortResponsesByDate();
    return $responseCollection->getResponseJSON();
}

?>