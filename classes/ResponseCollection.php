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

    public function convertToCards(): string
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
            $card = $card . '<h5 class="card-title">' . reset($questionResponses) . '</h5>';
//            $detailsUrl = "detailsPage.php?responseId=" . $response->getResponseId();
//            $card = $card . '<a href="' . $detailsUrl . '" class="btn btn-primary">View Details</a>';
    

            $card = $card . '</div></div>';
            $cards = $cards . $card;
        }
        return $cards . "</div>";
    }

    public function getResponseJSON() {
        $output = array();
        foreach ($this->responses as $response) {
            $output[] = $response->getJSON();
        }
        return json_encode($output);
    }

    public function sortResponsesAlphabetically(): void
    {
        usort($this->responses, function ($a, $b) {
            return strcmp($a->getResponseId(), $b->getResponseId());
        });
    }

    public function sortResponsesByDate($newestFirst = true): void
    {
        usort($this->responses, function ($a, $b) use ($newestFirst) {
            $dateA = strtotime($a->getFormSubmissionDate());
            $dateB = strtotime($b->getFormSubmissionDate());

            if ($dateA == $dateB) {
                return 0;
            }

            $comparison = ($dateA < $dateB) ? -1 : 1;

            return $newestFirst ? -$comparison : $comparison;
        });
    }
}

?>
