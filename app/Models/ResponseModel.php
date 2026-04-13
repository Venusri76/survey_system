<?php

namespace App\Models;

use CodeIgniter\Model;

class ResponseModel extends Model
{
    protected $table         = 'responses';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;

    // answers column is longtext in your DB — store as JSON string
    protected $allowedFields = [
        'survey_id',
        'answers',
        'submitted_at',
    ];

    // Save a full response
    // $answers = [question_id => option_id, ...]
    public function saveResponse(int $surveyId, array $answers): int
    {
        $insertId = $this->insert([
            'survey_id'    => $surveyId,
            'answers'      => json_encode($answers),  // encode array to JSON string
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);

        return (int) $insertId;
    }

    // Get all responses for a survey, newest first
    public function getBySurvey(int $surveyId): array
    {
        $rows = $this->where('survey_id', $surveyId)
                     ->orderBy('submitted_at', 'DESC')
                     ->findAll();

        // Decode JSON string back to array for each response
        foreach ($rows as &$r) {
            $r['answers'] = json_decode($r['answers'], true) ?? [];
        }
        unset($r);

        return $rows;
    }
}