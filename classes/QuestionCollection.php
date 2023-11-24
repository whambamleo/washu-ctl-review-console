<?php

class QuestionCollection
{
    private $parsedQuestionsArray;

    public function __construct($questionsJSON) {
        $this->parsedQuestionsArray = $this->parseQualtricsQuestionsJSON($questionsJSON);
    }

    public function getParsedQuestionsJson()
    {
        // Return the parsed questions data as JSON
        return json_encode($this->parsedQuestionsArray);
    }

    private function parseQualtricsQuestionsJSON($questionsJSON): array
    {
        $qualtricsQuestionsArray = json_decode($questionsJSON, true);
        $parsedQuestions = [];

        foreach ($qualtricsQuestionsArray['result']['elements'] as $originalQuestion) {
            // Only keep the 'QuestionDescription' and 'QuestionID' field for each question
            $parsedQuestion = [
                'QuestionDescription' => $originalQuestion['QuestionDescription'],
                'QuestionID' => $originalQuestion['QuestionID']
            ];
            $parsedQuestions[] = $parsedQuestion;
        }
        return [
            'result' => [
                'elements' => $parsedQuestions
            ]
        ];
    }
}
?>