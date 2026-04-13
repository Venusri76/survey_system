<?= $this->extend('layouts/admin') ?>
<?php $pageTitle = esc($survey['topic_name']) . ' — Results'; ?>

<?= $this->section('content') ?>

<h2><?= esc($survey['topic_name']) ?> — Results</h2>
<hr>

<p>
    <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-info">&larr; Back to Dashboard</a>
    &nbsp;
    <?php if (! empty($responses)): ?>
        <a href="<?= base_url('admin/surveys/' . $survey['id'] . '/results/download') ?>"
           class="btn btn-success">&#8659; Download CSV</a>
    <?php endif; ?>
</p>

<p>
    Status: <strong><?= $survey['status'] ?></strong> |
    Questions: <strong><?= count($questions) ?></strong> |
    Responses: <strong><?= count($responses) ?></strong> |
    URL: <a href="<?= base_url('survey/' . $survey['slug']) ?>" target="_blank">/survey/<?= esc($survey['slug']) ?></a>
</p>

<?php if (empty($responses)): ?>
    <p>No responses yet. Share the survey link to collect answers.</p>
<?php else: ?>

    <!-- Per-question stats -->
    <h3>Question Breakdown</h3>
    <?php foreach ($questions as $idx => $q):
        $s = $stats[$q['id']] ?? ['total'=>0,'correct'=>0,'percent'=>0,'optionCounts'=>[]];
    ?>
    <div style="background:#fff; border:1px solid #ddd; border-radius:5px; padding:15px; margin-bottom:15px;">
        <p><strong>Q<?= $idx + 1 ?>: <?= esc($q['question']) ?></strong>
           &nbsp;&nbsp; <span style="color:green"><?= $s['percent'] ?>% correct</span>
           &nbsp; (<?= $s['correct'] ?>/<?= $s['total'] ?> answered correctly)
        </p>
        <table style="width:auto; min-width:400px;">
            <thead>
                <tr>
                    <th>Option</th>
                    <th>Correct?</th>
                    <th>Chosen Count</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($q['options'] as $opt):
                $cnt = $s['optionCounts'][$opt['id']] ?? 0;
                $pct = $s['total'] > 0 ? round(($cnt / $s['total']) * 100) : 0;
            ?>
                <tr style="background: <?= (int)$opt['is_correct'] === 1 ? '#d4edda' : '#fff' ?>">
                    <td><?= esc($opt['option_text']) ?></td>
                    <td><?= (int)$opt['is_correct'] === 1 ? '&#10003; Yes' : '&#10007; No' ?></td>
                    <td><?= $cnt ?></td>
                    <td><?= $pct ?>%</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>

    <!-- Individual responses -->
    <h3>Individual Responses</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Submitted At</th>
                <?php foreach ($questions as $idx => $q): ?>
                    <th>Q<?= $idx + 1 ?></th>
                <?php endforeach; ?>
                <th>Score</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($responses as $ri => $r):
            $score     = 0;
            $total     = count($questions);
            $answerMap = $r['answers'];
        ?>
        <tr>
            <td><?= $ri + 1 ?></td>
            <td><?= $r['submitted_at'] ?></td>

            <?php foreach ($questions as $q):
                $chosenOptId = $answerMap[$q['id']] ?? null;
                $isCorrect   = false;
                $chosenText  = '—';

                if ($chosenOptId !== null) {
                    foreach ($q['options'] as $opt) {
                        if ((int)$opt['id'] === (int)$chosenOptId) {
                            $chosenText = $opt['option_text'];
                            $isCorrect  = (int)$opt['is_correct'] === 1;
                            break;
                        }
                    }
                }

                if ($isCorrect) $score++;
            ?>
            <td style="background:<?= $chosenOptId ? ($isCorrect ? '#d4edda' : '#f8d7da') : '#fff' ?>"
                title="<?= esc($chosenText) ?>">
                <?= $chosenOptId ? ($isCorrect ? '&#10003;' : '&#10007;') : '—' ?>
            </td>
            <?php endforeach; ?>

            <td><strong><?= $score ?>/<?= $total ?></strong></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

<?php endif; ?>

<?= $this->endSection() ?>