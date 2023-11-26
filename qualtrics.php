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
?>