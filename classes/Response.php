<?php
class Response {
    public $responseId;
    public $formStatus;
    public $formSubmissionDate;
    public $formQuestionResponses;

    public function __construct($response) {
        $this->responseId = $response['responseId'];
        $this->values = $response['values'];
        $this->formStatus = $this->values['formStatus'];
        $this->formSubmissionDate = $this->values['endDate'];
        
        foreach ($response['values'] as $key => $value) {
            if (substr($key, 0, 3) === "QID") {
                $this->formQuestionResponses["$key"] = $value;
            }
        }
    }
}
?>