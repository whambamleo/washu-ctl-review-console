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

    public function convertToCards() : string
    {
        $cards = '<div class="cards">';
        foreach ($this->responses as $response) {
            $questionResponses = array_values($response->getFormQuestionResponses());
            $card = '<div class="card" style="width: 50rem;">';
            $card = $card . '<div class="card-body">';
            $card = $card . '<h5 class="card-title">' . reset($questionResponses) . '</h5>';
            $card = $card . '<p class="card-text">Tool Name: ' . next($questionResponses) . "</p>";
            $card = $card . '<p class="card-text text-muted" style="text-align: right;">Submitted on: ' . $response->getFormSubmissionDate() . '</p>';
            $card = $card . '<p class="card-text text-right position-absolute" style="top: 0; right: 0; border: 1px solid green; border-radius: 10px; padding: 5px; margin: 5px;">' . $response->getFormStatus() . '</p>';
            $card = $card . '</div></div>';
            $cards = $cards . $card;
        }
        return $cards . "</div>";
    }
}
?>
