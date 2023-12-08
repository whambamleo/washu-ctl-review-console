<?php

// Qualtrics API Handler
include(get_template_directory() . '/qualtrics.php');
// Data Models
include(get_template_directory() . '/classes/Response.php');
include(get_template_directory() . '/classes/ResponseCollection.php');
include(get_template_directory() . '/classes/QuestionCollection.php');

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
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/responsesFiltered', [
            'methods' => 'GET',
            'callback' => 'getResponsesFiltered',
            'permission_callback' => '__return_true',
        ]);
    });
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/responsesGrouped', [
            'methods' => 'GET',
            'callback' => 'getResponsesGrouped',
            'permission_callback' => '__return_true',
        ]);
    });
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/responsesArchived', [
            'methods' => 'GET',
            'callback' => 'getResponsesArchived',
            'permission_callback' => '__return_true',
        ]);
    });
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/singleResponse', [
            'methods' => 'GET',
            'callback' => 'getSingleResponse',
            'permission_callback' => '__return_true',
        ]);
    });
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/questions', [
            'methods' => 'GET',
            'callback' => 'getQuestions',
            'permission_callback' => '__return_true',
        ]);
    });
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/setEmbeddedData', [
            'methods' => 'GET',
            'callback' => 'setEmbeddedData',
            'permission_callback' => '__return_true',
        ]);
    });
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/deleteResponse', [
            'methods' => 'DELETE',
            'callback' => 'deleteResponseFromQualtrics',
            'permission_callback' => '__return_true',
        ]);
    });
    add_action('rest_api_init', function () {
        register_rest_route('console/v1', '/resetCache', [
            'methods' => 'GET',
            'callback' => 'resetCache',
            'permission_callback' => '__return_true',
        ]);
    });
}

function getResponses(): string
{
    $responseCollection = get_transient('response_collection');

    if (false === $responseCollection) {
        try {
            $responseCollection = resetCache();
        } catch (Exception $e) {
            return "Unable to make responseCollection";
        }
    }

    return $responseCollection->getResponseJSON();
}

function getResponsesFiltered(): string
{
    $filterInput = isset($_GET['filterInput']) ? sanitize_text_field($_GET['filterInput']) : null;
    
    $responseCollection = get_transient('response_collection');

    if (false === $responseCollection || is_null($responseCollection)) {
        try {
            $responseCollection = resetCache();
        } catch (Exception $e) {
            return "Unable to make responseCollection";
        }
    }
    $filteredResponseCollection = clone $responseCollection;
    $filteredResponseCollection->filter($filterInput);
    return $filteredResponseCollection->getResponseJSON();
}

function getResponsesGrouped(): string
{
    $responseCollection = get_transient('response_collection');

    if (false === $responseCollection || is_null($responseCollection)) {
        try {
            $responseCollection = resetCache();
        } catch (Exception $e) {
            return "Unable to make responseCollection";
        }
    }

    return $responseCollection->getResponseGroupedJSON();
}

function getResponsesArchived(): string
{
    $responseCollection = get_transient('response_collection');

    if (false === $responseCollection || is_null($responseCollection)) {
        try {
            $responseCollection = resetCache();
        } catch (Exception $e) {
            return "Unable to make responseCollection";
        }
    }

    return $responseCollection->getResponseArchivedJSON();
}

function getSortedResponsesNewestFirst(): string
{
    $responseCollection = get_transient('response_collection');

    if (false === $responseCollection) {
        try {
            $responseCollection = resetCache();
        } catch (Exception $e) {
            return "Unable to make responseCollection";
        }
    }

    $responseCollection->sortResponsesByDate();
    return $responseCollection->getResponseJSON();
}

function getSingleResponse($data) : string {
    $responseId = isset($_GET['responseId']) ? sanitize_text_field($_GET['responseId']) : null;

    $responseCollection = get_transient('response_collection');
    if (false === $responseCollection) {
        try {
            $responseCollection = resetCache();
        } catch (Exception $e) {
            return "Unable to make responseCollection";
        }
    }

    return $responseCollection->getSingleResponseJSON($responseId);
}

function deleteResponse(WP_REST_Request $request) {
    // Extract the responseId from the request
    $responseId = $request->get_param('responseId');

    // Check if responseId is provided
    if (!$responseId) {
        return new WP_REST_Response(['success' => false, 'message' => 'Response ID not provided'], 400);
    }

    // Call the function to delete the response in Qualtrics
    return deleteResponseFromQualtrics(['responseId' => $responseId]);
}

function getQuestions() {
    $questionCollection = get_transient('question_collection');
    if (false === $questionCollection) {
        try {
            $questionCollection = new QuestionCollection(getQuestionJSONFromQualtrics());
            // Cache the object for 1 hour
            set_transient('question_collection', $questionCollection, 60 * 60);
        } catch (Exception $e) {
            return "Unable to make questionCollection";
        }
    }
    return $questionCollection->getParsedQuestionsJson();
}

function setEmbeddedData($data) : string {
    $responseId = isset($_GET['responseId']) ? sanitize_text_field($_GET['responseId']) : null;
    $fieldName = isset($_GET['fieldName']) ? sanitize_text_field($_GET['fieldName']) : null;
    $fieldValue = isset($_GET['fieldValue']) ? sanitize_text_field($_GET['fieldValue']) : null;

    $response = setEmbeddedDataField($responseId, $fieldName, $fieldValue);

    // Reset the cache with latest values
    try {
        $responseCollection = resetCache();
    } catch (Exception $e) {
        return "Unable to make responseCollection";
    }

    return $response;
}

// Resets the wordpress cache with the latest responses
function resetCache() : ResponseCollection {
    try {
        $responseCollection = new ResponseCollection(getResponsesFromQualtrics());
        // Cache the object for 1 hour
        set_transient('response_collection', $responseCollection, 60 * 60);
        return $responseCollection;
    } catch (Exception $e) {
        return "Cache reset failed";
    }
}
?>