<?php

namespace App\Models;

use CodeIgniter\Model;

class SurveyModel extends Model
{
    protected $table         = 'surveys';
    protected $primaryKey    = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = ['topic_name', 'slug', 'status'];

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }

    public function getAllSurveys(): array
    {
        return $this->orderBy('id', 'DESC')->findAll();
    }

    public function toggleStatus(int $id): void
    {
        $survey = $this->find($id);
        if (! $survey) return;
        $new = $survey['status'] === 'active' ? 'inactive' : 'active';
        $this->update($id, ['status' => $new]);
    }

    public function generateSlug(string $topicName): string
    {
        $base = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $topicName), '-'));
        $slug = $base . '-' . bin2hex(random_bytes(4));
        while ($this->findBySlug($slug)) {
            $slug = $base . '-' . bin2hex(random_bytes(4));
        }
        return $slug;
    }

    public function countResponses(int $surveyId): int
    {
        return $this->db->table('responses')
            ->where('survey_id', $surveyId)
            ->countAllResults();
    }

    public function countQuestions(int $surveyId): int
    {
        return $this->db->table('questions')
            ->where('survey_id', $surveyId)
            ->countAllResults();
    }
}