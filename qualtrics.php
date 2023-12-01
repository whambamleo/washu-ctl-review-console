<?php

/* TODO:
 * Make function for redundant HTTP request setup
 * Make sure the sleep and request loop in getResponses eventually times out and throws an exception
*/

$api_config = include('qualtrics_config.php');
$dataCenterId = $api_config['dataCenterId'];
$api_token = $api_config['x-api-token'];
$surveyId = $api_config['surveyId'];
global $dataCenterId, $surveyId, $api_token;

function getResponsesFromQualtrics() {
    try {
        $progressId = startResponseExport();
    } catch (Exception $e) {
        echo 'Failed while trying to initiate export:' .$e->getMessage();
        return;
    }

    try {
        $fileId = checkExportProgress($progressId);
        while (strlen($fileId) == 0) {
            sleep(0.1);
            $fileId = checkExportProgress($progressId);
        }
    } catch (Exception $e) {
        echo 'Failed while checking progress of export:' .$e->getMessage();
        return;
    }

    try {
        return getResponseJSON($fileId);
    } catch (Exception $e) {
        echo 'Failed while fetching responses JSON file:' .$e->getMessage();
        return;
    }
}

function getQuestionJSONFromQualtrics() {
    global $dataCenterId, $surveyId, $api_token;

    $url = "https://$dataCenterId.qualtrics.com/API/v3/survey-definitions/$surveyId/questions";
    $headers = [
        "X-API-TOKEN: $api_token",
        'Content-Type: application/json',
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    } else {
        curl_close($ch);
        return $response;
    }
}

function setEmbeddedDataField($responseId, $fieldName, $fieldValue) {
    global $dataCenterId, $surveyId, $api_token;
    $url = "https://$dataCenterId.qualtrics.com/API/v3/responses/$responseId";

    $headers = [
        "X-API-TOKEN: $api_token",
        'Content-Type: application/json',
    ];
    $data = [
        'surveyId' => $surveyId,
        'embeddedData' => [
            $fieldName => $fieldValue
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    } else {
        curl_close($ch);
        return $response;
    }
}

/**
 * Initiates a generic response export and returns a progressID for
 * following calls
 * @return string progressID
 * @throws Exception if call fails
 */
function startResponseExport(): string
{
    global $dataCenterId, $surveyId, $api_token;
    $url = "https://$dataCenterId.qualtrics.com/API/v3/surveys/$surveyId/export-responses";
    $headers = [
        "X-API-TOKEN: $api_token",
        'Content-Type: application/json',
    ];
    $data = [
        'format' => 'json',
        'compress' => 'false',
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    } else {
        curl_close($ch);
        $response_as_json = json_decode($response);
        return $response_as_json->result->progressId;
    }
}

/**
 * Checks the progress of an export and returns the fileId when the export
 * is complete. If the export is not done, it returns an empty string
 * @return string fileId
 * @throws Exception if call fails
 */
function checkExportProgress($progressId): string {
    global $dataCenterId, $surveyId, $api_token;

    if ($progressId == null) {
        throw new Exception("Progress ID field provided was null!");
    }

    $fileId = "";
    $url = "https://$dataCenterId.qualtrics.com/API/v3/surveys/$surveyId/export-responses/$progressId";
    $headers = [
        "X-API-TOKEN: $api_token",
        'Content-Type: application/json',
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    } else {
        curl_close($ch);
        $response_as_json = json_decode($response);
        if ($response_as_json === null) {
            throw new Exception('Failed to decode JSON response');
        }
        if ($response_as_json->result->status == "failed") {
            throw new Exception("File export failed!");
        }

        if ($response_as_json->result->status == "complete" && $response_as_json->result->percentComplete == "100") {
            $fileId = $response_as_json->result->fileId;
        }

        return $fileId;
    }
}

/**
 * Uses the fileId to fetch the final JSON of responses
 * @return mixed responses
 * @throws Exception if call fails
 */
function getResponseJSON($fileId) {
    global $dataCenterId, $surveyId, $api_token;

    if ($fileId == null) {
        throw new Exception("File ID field provided was null!");
    }

    $url = "https://$dataCenterId.qualtrics.com/API/v3/surveys/$surveyId/export-responses/$fileId/file";
    $headers = [
        "X-API-TOKEN: $api_token",
        'Content-Type: application/json',
    ];
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        throw new Exception('Curl error: ' . curl_error($ch));
    } else {
        curl_close($ch);
        return $response;
    }
}

function deleteResponseFromQualtrics($data) {
    global $api_token, $surveyId, $dataCenterId;
    
    $responseId = $data['responseId'];
    $url = "https://{$dataCenterId}.qualtrics.com/API/v3/surveys/{$surveyId}/responses/{$responseId}";

    $args = array(
        'method' => 'DELETE',
        'headers' => array(
            'X-API-TOKEN' => $api_token,
            'Content-Type' => 'application/json'
        )
    );

    $response = wp_remote_request($url, $args);
    $response_code = wp_remote_retrieve_response_code($response);

    if ($response_code == 200) {
        delete_transient('response_collection');
        return new WP_REST_Response(['success' => true], 200);
    } else {
        return new WP_REST_Response(['success' => false], $response_code);
    }
}

// function editResponseInQualtrics() {
//     return new WP_REST_Response(['success' => true, 'message' => 'Endpoint reached'], 200);
// }

function editResponseInQualtrics($responseId, $updates) {
    global $api_token, $surveyId, $dataCenterId;

    // Construct the URL for the Qualtrics API
    $url = "https://{$dataCenterId}.qualtrics.com/API/v3/surveys/{$surveyId}/responses/{$responseId}";

    // Prepare the headers and body for the API request
    $headers = [
        'Content-Type' => 'application/json',
        'X-API-TOKEN' => $api_token
    ];

    // Prepare the body. Assuming updates are in the form of 'QIDx_TEXT' => 'response'
    $body = [
        'surveyId' => $surveyId,
        'responses' => $updates  // Adjust this line according to the exact Qualtrics API requirements
    ];

    $args = [
        'method' => 'PUT', // Ensure 'PUT' is the correct method as per Qualtrics API
        'headers' => $headers,
        'body' => json_encode($body),
        'data_format' => 'body'
    ];

    // Make the API request
    $response = wp_remote_request($url, $args);

    // Error handling
    if (is_wp_error($response)) {
        return new WP_Error('qualtrics_update_failed', $response->get_error_message(), [
            'status' => wp_remote_retrieve_response_code($response)
        ]);
    }

    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = json_decode(wp_remote_retrieve_body($response), true);

    // Check for successful response
    if ($response_code === 200) {
        return new WP_REST_Response(['success' => true, 'message' => 'Response updated successfully.'], 200);
    } else {
        return new WP_REST_Response(['success' => false, 'message' => $response_body['error']['message'] ?? 'Unknown error'], $response_code);
    }
}



// function editResponseInQualtrics($responseId, $updates) {
//     global $api_token, $surveyId, $dataCenterId;

//     $url = "https://{$dataCenterId}.qualtrics.com/API/v3/surveys/{$surveyId}/responses/{$responseId}";

//     $body = [
//         'surveyId' => $surveyId,
//         'embeddedData' => $updates
//     ];

//     $args = [
//         'method' => 'PUT', // instead of POSt
//         'headers' => [
//             'Content-Type' => 'application/json',
//             'X-API-TOKEN' => $api_token
//         ],
//         'body' => json_encode($body),
//         'data_format' => 'body'
//     ];

//     $response = wp_remote_request($url, $args);

//     if (is_wp_error($response)) {
//         return new WP_Error('qualtrics_update_failed', $response->get_error_message(), [
//             'status' => 500
//         ]);
//     }

//     $response_code = wp_remote_retrieve_response_code($response);
//     $response_body = json_decode(wp_remote_retrieve_body($response), true);

//     if ($response_code === 200) {
//         return new WP_REST_Response(['success' => true, 'message' => 'Response updated successfully.'], 200);
//     } else {
//         return new WP_REST_Response(['success' => false, 'message' => $response_body['error']['message']], $response_code);
//     }
// }

?>