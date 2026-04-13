<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($survey['topic_name']) ?> — Survey</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 0; }
        .header { background: #333; color: #fff; padding: 12px 20px; }
        .container { max-width: 700px; margin: 30px auto; padding: 0 20px; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 25px; margin-bottom: 20px; }
        h1 { font-size: 22px; margin-bottom: 10px; }
        h2 { font-size: 17px; margin-bottom: 15px; color: #333; }
        .question-num { color: #666; font-size: 13px; margin-bottom: 8px; }
        .option { display: block; padding: 10px 15px; border: 2px solid #ddd; border-radius: 5px; margin-bottom: 8px; cursor: pointer; font-size: 15px; }
        .option:hover { border-color: #007bff; background: #f0f7ff; }
        .option input { margin-right: 10px; }
        .option.selected { border-color: #007bff; background: #e7f1ff; }
        .nav { display: flex; justify-content: space-between; margin-top: 20px; }
        .btn { padding: 9px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none; display: inline-block; }
        .btn-primary { background: #007bff; color: #fff; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn:disabled { background: #aaa; cursor: not-allowed; }
        .progress { background: #e9ecef; border-radius: 4px; height: 8px; margin-bottom: 20px; }
        .progress-bar { background: #007bff; height: 8px; border-radius: 4px; transition: width .3s; }
        .intro { text-align: center; padding: 10px 0; }
        .intro ul { list-style: none; padding: 0; color: #666; margin: 15px 0; }
        .intro ul li { margin-bottom: 5px; }
        footer { text-align: center; color: #999; font-size: 13px; padding: 20px; }
    </style>
</head>
<body>

<div class="header">
    <strong>&#9632; SurveySystem</strong>
</div>

<div class="container">

    <!-- Intro card -->
    <div class="card intro" id="introCard">
        <h1><?= esc($survey['topic_name']) ?></h1>
        <ul>
            <li>&#128203; <?= count($questions) ?> question<?= count($questions) !== 1 ? 's' : '' ?></li>
            <li>&#128274; Fully anonymous — no login required</li>
            <li>&#9201; No time limit</li>
        </ul>
        <p style="color:#666; font-size:14px;">Your answers are anonymous. No personal information is collected.</p>
        <br>
        <button class="btn btn-primary" onclick="startSurvey()">Start Survey &rarr;</button>
    </div>

    <!-- Survey form -->
    <form action="<?= base_url('survey/' . $survey['slug'] . '/submit') ?>"
          method="POST" id="surveyForm" style="display:none">
        <?= csrf_field() ?>

        <!-- Progress bar -->
        <div class="progress">
            <div class="progress-bar" id="progressBar" style="width:0%"></div>
        </div>
        <p style="text-align:right; font-size:13px; color:#666; margin-bottom:15px;">
            Question <span id="currentNum">1</span> of <?= count($questions) ?>
        </p>

        <?php foreach ($questions as $idx => $q): ?>
        <div class="card" id="qc-<?= $idx ?>" style="display:none">

            <div class="question-num">Question <?= $idx + 1 ?> of <?= count($questions) ?></div>
            <h2><?= esc($q['question']) ?></h2>

            <?php foreach ($q['options'] as $oi => $opt): ?>
            <label class="option" id="opt-<?= $idx ?>-<?= $oi ?>">
                <input
                    type="radio"
                    name="answers[<?= $q['id'] ?>]"
                    value="<?= $opt['id'] ?>"
                    onchange="pick(<?= $idx ?>, this)"
                >
                <?= chr(65 + $oi) ?>. <?= esc($opt['option_text']) ?>
            </label>
            <?php endforeach; ?>

            <div class="nav">
                <?php if ($idx > 0): ?>
                    <button type="button" class="btn btn-secondary" onclick="goTo(<?= $idx - 1 ?>)">&larr; Previous</button>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>

                <?php if ($idx < count($questions) - 1): ?>
                    <button type="button" class="btn btn-primary" id="nb-<?= $idx ?>"
                            onclick="goTo(<?= $idx + 1 ?>)" disabled>
                        Next &rarr;
                    </button>
                <?php else: ?>
                    <button type="submit" class="btn btn-success" id="submitBtn" disabled>
                        Submit Answers &#10003;
                    </button>
                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>

    </form>

</div>

<footer>&#128274; Your responses are anonymous. No personal data is collected.</footer>

<script>
const TOTAL = <?= count($questions) ?>;

function startSurvey() {
    document.getElementById('introCard').style.display  = 'none';
    document.getElementById('surveyForm').style.display = 'block';
    goTo(0);
}

function goTo(idx) {
    document.querySelectorAll('[id^="qc-"]').forEach(c => c.style.display = 'none');
    document.getElementById('qc-' + idx).style.display = 'block';
    // Update progress
    const pct = Math.round(((idx + 1) / TOTAL) * 100);
    document.getElementById('progressBar').style.width  = pct + '%';
    document.getElementById('currentNum').textContent   = idx + 1;
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function pick(idx, radio) {
    // Highlight selected option
    document.querySelectorAll('#qc-' + idx + ' .option').forEach(o => o.classList.remove('selected'));
    radio.closest('.option').classList.add('selected');
    // Enable next/submit
    const nb  = document.getElementById('nb-' + idx);
    const sub = document.getElementById('submitBtn');
    if (nb)  nb.disabled  = false;
    if (sub && idx === TOTAL - 1) sub.disabled = false;
}

// Prevent double submit
document.getElementById('surveyForm').addEventListener('submit', () => {
    const btn = document.getElementById('submitBtn');
    if (btn) { btn.disabled = true; btn.textContent = 'Submitting...'; }
});
</script>

</body>
</html>