<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SurveyModel;
use App\Models\QuestionModel;
use App\Models\ResponseModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ResultsController extends BaseController
{
    // GET  admin/surveys/(:num)/results
    public function index(int $id): string
    {
        $survey = model(SurveyModel::class)->find($id);
        if (! $survey) throw PageNotFoundException::forPageNotFound();

        $questions = model(QuestionModel::class)->getWithOptionsOrdered($id);
        $responses = model(ResponseModel::class)->getBySurvey($id);
        // $responses[n]['answers'] = [question_id => option_id, ...]

        // Build per-question statistics
        $stats = [];
        foreach ($questions as $q) {
            $total        = 0;
            $correct      = 0;
            $optionCounts = [];

            foreach ($responses as $r) {
                // Get which option this user chose for this question
                $chosenOptId = $r['answers'][$q['id']] ?? null;

                if ($chosenOptId !== null) {
                    $total++;
                    $optionCounts[$chosenOptId] = ($optionCounts[$chosenOptId] ?? 0) + 1;

                    // Check if chosen option is correct
                    foreach ($q['options'] as $opt) {
                        if ((int) $opt['id'] === (int) $chosenOptId && (int) $opt['is_correct'] === 1) {
                            $correct++;
                            break;
                        }
                    }
                }
            }

            $stats[$q['id']] = [
                'total'        => $total,
                'correct'      => $correct,
                'percent'      => $total > 0 ? round(($correct / $total) * 100) : 0,
                'optionCounts' => $optionCounts,
            ];
        }

        return view('admin/results', compact('survey', 'questions', 'responses', 'stats'));
    }

    // GET  admin/surveys/(:num)/results/download
    public function download(int $id)
    {
        $survey = model(SurveyModel::class)->find($id);
        if (! $survey) throw PageNotFoundException::forPageNotFound();

        $questions = model(QuestionModel::class)->getWithOptionsOrdered($id);
        $responses = model(ResponseModel::class)->getBySurvey($id);

        $handle = fopen('php://temp', 'w+');

        // Header row
        $header = ['Response #', 'Submitted At'];
        foreach ($questions as $q) {
            $header[] = $q['question'];
            $header[] = 'Correct?';
        }
        $header[] = 'Score';
        $header[] = 'Score %';
        fputcsv($handle, $header);

        // Data rows
        $rowNum = 1;
        foreach ($responses as $r) {
            $row   = [$rowNum++, $r['submitted_at']];
            $score = 0;
            $total = count($questions);

            foreach ($questions as $q) {
                $chosenOptId = $r['answers'][$q['id']] ?? null;
                $chosenText  = '—';
                $isCorrect   = false;

                if ($chosenOptId !== null) {
                    foreach ($q['options'] as $opt) {
                        if ((int) $opt['id'] === (int) $chosenOptId) {
                            $chosenText = $opt['option_text'];
                            $isCorrect  = (int) $opt['is_correct'] === 1;
                            break;
                        }
                    }
                }

                $row[] = $chosenText;
                $row[] = $isCorrect ? 'Yes' : 'No';
                if ($isCorrect) $score++;
            }

            $row[] = $score . '/' . $total;
            $row[] = $total > 0 ? round(($score / $total) * 100) . '%' : '0%';
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        $filename = 'results-' . $survey['slug'] . '-' . date('Y-m-d') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Pragma', 'no-cache')
            ->setBody("\xEF\xBB\xBF" . $csv);
    }
}