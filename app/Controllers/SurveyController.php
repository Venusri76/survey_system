<?php

namespace App\Controllers;

use App\Models\SurveyModel;
use App\Models\QuestionModel;
use App\Models\ResponseModel;

class SurveyController extends BaseController
{
    // GET  survey/(:segment)
    public function take(string $slug): string
    {
        $surveyModel = new SurveyModel();
        $survey      = $surveyModel->findBySlug($slug);

        if (! $survey) {
            return view('survey/unavailable', ['reason' => 'not_found']);
        }

        if ($survey['status'] !== 'active') {
            return view('survey/unavailable', ['reason' => 'inactive']);
        }

        $questionModel = new QuestionModel();
        $questions     = $questionModel->getWithOptions((int) $survey['id']);

        return view('survey/take', [
            'survey'    => $survey,
            'questions' => $questions,
        ]);
    }

    // POST  survey/(:segment)/submit
    public function submit(string $slug)
    {
        $surveyModel = new SurveyModel();
        $survey      = $surveyModel->findBySlug($slug);

        if (! $survey || $survey['status'] !== 'active') {
            return redirect()->to(base_url('survey/' . $slug . '/thankyou'));
        }

        // Collect and sanitise posted answers
        // Form sends: answers[question_id] = option_id
        $raw       = $this->request->getPost('answers');
        $sanitized = [];

        if (is_array($raw)) {
            foreach ($raw as $qId => $optId) {
                if (is_numeric($qId) && is_numeric($optId)) {
                    $sanitized[(int) $qId] = (int) $optId;
                }
            }
        }

        // Use new instance — never returns null unlike model() helper
        $responseModel = new ResponseModel();
        $responseModel->saveResponse((int) $survey['id'], $sanitized);

        // Redirect after POST to prevent duplicate submissions on refresh
        return redirect()->to(base_url('survey/' . $slug . '/thankyou'));
    }

    // GET  survey/(:segment)/thankyou
    public function thankyou(string $slug): string
    {
        $surveyModel = new SurveyModel();
        $survey      = $surveyModel->findBySlug($slug);

        return view('survey/thankyou', ['survey' => $survey]);
    }
}