<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You — SurveySystem</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        .header { background: #333; color: #fff; padding: 12px 20px; }
        .container { max-width: 600px; margin: 60px auto; padding: 0 20px; text-align: center; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 40px 30px; }
        .icon { font-size: 50px; color: #28a745; margin-bottom: 15px; }
        h1 { color: #28a745; margin-bottom: 10px; }
        p { color: #555; font-size: 15px; margin-bottom: 8px; }
        footer { text-align: center; color: #999; font-size: 13px; padding: 20px; }
    </style>
</head>
<body>

<div class="header">
    <strong>&#9632; SurveySystem</strong>
</div>

<div class="container">
    <div class="card">
        <div class="icon">&#10003;</div>
        <h1>Thank You!</h1>

        <?php if (! empty($survey)): ?>
            <p>Your response to <strong><?= esc($survey['topic_name']) ?></strong> has been recorded.</p>
        <?php else: ?>
            <p>Your response has been recorded successfully.</p>
        <?php endif; ?>

        <p style="color:#999; font-size:13px; margin-top:15px;">
            &#128274; Your answers are completely anonymous.<br>
            No personal information was collected.
        </p>
    </div>
</div>

<footer>Powered by SurveySystem</footer>

</body>
</html>