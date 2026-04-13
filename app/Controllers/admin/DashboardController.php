<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\CsvParser;
use App\Models\SurveyModel;
use App\Models\QuestionModel;

class DashboardController extends BaseController
{
    // GET  admin/dashboard
    public function index(): string
    {
        $surveyModel = model(SurveyModel::class);
        $surveys     = $surveyModel->getAllSurveys();

        foreach ($surveys as &$survey) {
            $survey['response_count'] = $surveyModel->countResponses((int) $survey['id']);
            $survey['question_count'] = $surveyModel->countQuestions((int) $survey['id']);
        }
        unset($survey);

        return view('admin/dashboard', ['surveys' => $surveys]);
    }

    // POST  admin/surveys/upload
    public function upload()
    {
        if (! $this->validate([
            'topic_name' => 'required|min_length[3]|max_length[255]',
            'csv_file'   => 'uploaded[csv_file]|max_size[csv_file,5120]|ext_in[csv_file,csv]',
        ])) {
            return redirect()->to(base_url('admin/dashboard'))
                ->withInput()
                ->with('upload_errors', $this->validator->getErrors());
        }

        $file      = $this->request->getFile('csv_file');
        $topicName = trim($this->request->getPost('topic_name'));

        // Generate unique slug with random suffix
        $surveyModel = model(SurveyModel::class);
        $slug        = $surveyModel->generateSlug($topicName);

        // Save CSV file to writable/uploads/
        $uploadPath = WRITEPATH . 'uploads/';
        if (! is_dir($uploadPath)) mkdir($uploadPath, 0755, true);
        $fileName = 'survey_' . time() . '_' . $file->getRandomName();
        $file->move($uploadPath, $fileName);

        // Parse CSV into rows
        $rows = (new CsvParser())->parse($uploadPath . $fileName);

        if (empty($rows)) {
            return redirect()->to(base_url('admin/dashboard'))
                ->with('error', 'CSV has no valid questions. Check the format.');
        }

        // Save survey record
        $surveyId = $surveyModel->insert([
            'topic_name' => $topicName,
            'slug'       => $slug,
            'status'     => 'active',
        ]);

        // Save each question with its options
        $qModel = model(QuestionModel::class);
        foreach ($rows as $row) {
            $qModel->insertWithOptions(
                (int) $surveyId,
                $row['question'],
                $row['correct'],
                $row['wrong']
            );
        }

        return redirect()->to(base_url('admin/dashboard'))
            ->with('success',
                'Survey "' . esc($topicName) . '" created with ' . count($rows) . ' question(s)! '
                . 'URL: ' . base_url('survey/' . $slug)
            );
    }

    // POST  admin/surveys/toggle/(:num)
    public function toggle(int $id)
    {
        $surveyModel = model(SurveyModel::class);
        $survey      = $surveyModel->find($id);

        if (! $survey) {
            return redirect()->to(base_url('admin/dashboard'))
                ->with('error', 'Survey not found.');
        }

        $surveyModel->toggleStatus($id);
        $new = $survey['status'] === 'active' ? 'INACTIVE' : 'ACTIVE';

        return redirect()->to(base_url('admin/dashboard'))
            ->with('success', '"' . esc($survey['topic_name']) . '" is now ' . $new . '.');
    }

    // GET  admin/surveys/delete/(:num)
    public function delete(int $id)
    {
        $surveyModel = model(SurveyModel::class);
        $survey      = $surveyModel->find($id);

        if (! $survey) {
            return redirect()->to(base_url('admin/dashboard'))
                ->with('error', 'Survey not found.');
        }

        // Cascading FK deletes questions, options, responses automatically
        $surveyModel->delete($id);

        return redirect()->to(base_url('admin/dashboard'))
            ->with('success', '"' . esc($survey['topic_name']) . '" deleted successfully.');
    }
}