<?php

class ResponseCollection
{
    public array $responses = [];

    public function __construct($json)
    {
        $jsonDataArray = json_decode($json, true);
        if ($jsonDataArray) {
            foreach ($jsonDataArray['responses'] as $responseData) {
                $response = new Response($responseData);
                $this->responses[] = $response;
            }
        } else {
            echo "Invalid JSON format.";
        }
    }

    public function printValues(): void
    {
        foreach ($this->responses as $response) {
            echo "Response ID: " . $response->responseId . "\n";
            foreach ($response->values as $key => $value) {
                echo "$key: $value\n";
            }
            echo "------------------------------\n";
        }
    }
}

?>
