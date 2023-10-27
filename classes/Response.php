<?php
class Response {
    public $responseId;
    public $values;
    public $labels;

    public function __construct($response) {
        $this->responseId = $response['responseId'];
        $this->values = $response['values'];
        $this->labels = $response['labels'];
    }
}
?>