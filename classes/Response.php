<?php
class Response {
    private $responseId;
    private $formStatus;
    private $formSubmissionDate;
    private $formQuestionResponses;

    public function __construct($response) {
        $this->responseId = $response['responseId'];
        $values = $response['values'];
        // Extracting relevant fields
        $this->formStatus = $values['formStatus'];
        $this->formSubmissionDate = $values['endDate'];
        
        foreach ($response['values'] as $key => $value) {
            if (substr($key, 0, 3) === "QID") {
                $this->formQuestionResponses["$key"] = $value;
            }
        }
    }

    public function getResponseId() {
        return $this->responseId;
    }

    public function getFormStatus() {
        return $this->formStatus;
    }

    public function getFormSubmissionDate() {
        return $this->formSubmissionDate;
    }

    public function getFormQuestionResponses() {
        return $this->formQuestionResponses;
    }
}
?>