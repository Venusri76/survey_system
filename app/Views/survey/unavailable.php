<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Survey Unavailable — SurveySystem</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; }
        .header { background: #333; color: #fff; padding: 12px 20px; }
        .container { max-width: 600px; margin: 60px auto; padding: 0 20px; text-align: center; }
        .card { background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 40px 30px; }
        .icon { font-size: 50px; margin-bottom: 15px; }
        h1 { color: #dc3545; margin-bottom: 10px; }
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
        <div class="icon">&#9888;</div>

        <?php if (($reason ?? '') === 'inactive'): ?>
            <h1>Survey Closed</h1>
            <p>This survey is currently closed and not accepting responses.</p>
            <p style="color:#999; font-size:13px;">Please contact the organiser for more information.</p>
        <?php else: ?>
            <h1>Survey Not Found</h1>
            <p>This survey link does not exist or may have been removed.</p>
        <?php endif; ?>

    </div>
</div>

<footer>Powered by SurveySystem</footer>

</body>
</html>