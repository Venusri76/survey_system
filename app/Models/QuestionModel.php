<?php

namespace App\Models;

use CodeIgniter\Model;

class QuestionModel extends Model
{
    protected $table         = 'questions';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['survey_id', 'question'];

    // Save one question + all its options
    public function insertWithOptions(int $surveyId, string $question, string $correct, array $wrong): int
    {
        $qid = $this->insert([
            'survey_id' => $surveyId,
            'question'  => $question,
        ]);

        $db = db_connect();

        // Save correct answer
        $db->table('options')->insert([
            'question_id' => $qid,
            'option_text' => $correct,
            'is_correct'  => 1,
        ]);

        // Save wrong answers
        foreach ($wrong as $w) {
            $db->table('options')->insert([
                'question_id' => $qid,
                'option_text' => $w,
                'is_correct'  => 0,
            ]);
        }

        return $qid;
    }

    // Get questions with SHUFFLED options — for survey page
    public function getWithOptions(int $surveyId): array
    {
        $questions = $this->where('survey_id', $surveyId)->findAll();
        $db        = db_connect();

        foreach ($questions as &$q) {
            $q['options'] = $db->table('options')
                               ->where('question_id', $q['id'])
                               ->get()->getResultArray();
            shuffle($q['options']);
        }
        unset($q);

        return $questions;
    }

    // Get questions with options in order — for results page
    public function getWithOptionsOrdered(int $surveyId): array
    {
        $questions = $this->where('survey_id', $surveyId)->findAll();
        $db        = db_connect();

        foreach ($questions as &$q) {
            $q['options'] = $db->table('options')
                               ->where('question_id', $q['id'])
                               ->get()->getResultArray();
        }
        unset($q);

        return $questions;
    }
}