<?php
class Response {
    private $responseId;
    private $formStatus;
    private $archived;
    private $formSubmissionDate;
    private $readableFormSubmissionDate;
    private $formQuestionResponses;

    public function __construct($response) {
        $this->responseId = $response['responseId'];
        $values = $response['values'];
        // Extracting relevant fields
        $this->formStatus = $values['formStatus'];
        $this->archived = $values['archived'];
        $this->readableFormSubmissionDate = $this->reformatSubmissionDate($values['endDate']);
        $this->formSubmissionDate = $values['endDate'];

        foreach ($response['values'] as $key => $value) {
            if (str_starts_with($key, "QID")) {
                $this->formQuestionResponses["$key"] = $value;
            }
        }
    }

    public function getJSON() {
        $json_data = [
            'responseId' => $this->responseId,
            'formStatus' => $this->formStatus,
            'archived' => $this->archived,
            'formSubmissionDate' => $this->formSubmissionDate,
            'formQuestionResponses' => $this->formQuestionResponses,
            'readableFormSubmissionDate' => $this->readableFormSubmissionDate
        ];

        return json_encode($json_data);
    }

    public function getResponseId() {
        return $this->responseId;
    }

    public function getFormStatus() {
        return $this->formStatus;
    }

    public function reformatSubmissionDate($formSubmissionDate) {
        $dateTime = new DateTime($formSubmissionDate);
        $now = new DateTime();

        $interval = $now->diff($dateTime);

        $years = $interval->y;
        $months = $interval->m;
        $days = $interval->d;
        $hours = $interval->h;

        if ($years > 0) {
            return "Submitted " . $years . " year" . ($years > 1 ? "s" : "") . " ago";
        } elseif ($months > 0) {
            return "Submitted " . $months . " month" . ($months > 1 ? "s" : "") . " ago";
        } elseif ($days > 0) {
            return "Submitted " . $days . " day" . ($days > 1 ? "s" : "") . " ago";
        } else {
            return "Submitted " . $hours . " hour" . ($hours > 1 ? "s" : "") . " ago";
        }
    }

    public function getFormSubmissionDate() {
        return $this->formSubmissionDate;
    }

    public function getReadableFormSubmissionDate() {
        return $this->readableFormSubmissionDate;
    }

    public function getFormQuestionResponses() {
        return $this->formQuestionResponses;
    }
}
?>