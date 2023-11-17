<?php
class ResponseDetails {
    private $responseId;
    private $details;
    private $apiToken;
    private $dataCenterId;
    private $surveyId;

    public function __construct($responseId, $apiToken, $dataCenterId, $surveyId) {
        $this->responseId = $responseId;
        $this->apiToken = $apiToken;
        $this->dataCenterId = $dataCenterId;
        $this->surveyId = $surveyId;
        $this->details = $this->fetchDetails();
    }

    private function fetchDetails() {
        $url = "https://{$this->dataCenterId}.qualtrics.com/API/v3/surveys/{$this->surveyId}/responses/{$this->responseId}";
        $headers = [
            "X-API-TOKEN: {$this->apiToken}",
            'Content-Type: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            curl_close($ch);
            throw new Exception('Curl error: ' . curl_error($ch));
        }
        curl_close($ch);
        
        $response = json_decode($result, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON decoding error: ' . json_last_error_msg());
        }

        // Check if the expected data is in the response and return it
        if (isset($response['result']['responses'][0])) {
            return $response['result']['responses'][0];
        } else {
            throw new Exception("Response details not found for ID: {$this->responseId}");
        }
    }

    public function getDetails() {
        return $this->details;
    }
}

?>