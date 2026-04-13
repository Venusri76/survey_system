<?= $this->extend('layouts/admin') ?>
<?php $pageTitle = 'Dashboard'; ?>

<?= $this->section('content') ?>

<h2>Admin Dashboard</h2>
<hr>

<!-- Upload Form -->
<h3>Create New Survey</h3>
<form action="<?= base_url('admin/surveys/upload') ?>" method="POST" enctype="multipart/form-data" style="background:#fff; padding:20px; border:1px solid #ddd; border-radius:5px; margin-bottom:25px;">
    <?= csrf_field() ?>

    <?php foreach (session()->getFlashdata('upload_errors') ?? [] as $e): ?>
        <div style="background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:8px 12px; border-radius:4px; margin-bottom:10px; font-size:13px;">
            &bull; <?= esc($e) ?>
        </div>
    <?php endforeach; ?>

    <div class="form-group">
        <label>Topic Name </label>
        <input type="text" name="topic_name" value="<?= esc(old('topic_name')) ?>"
               placeholder="e.g. Computer Science Basics" required style="max-width:400px;">
    </div>

    <div class="form-group">
        <label>CSV File </label>
        <input type="file" name="csv_file" accept=".csv" required style="max-width:400px;">
    </div>

    <button type="submit" class="btn btn-primary">Upload CSV</button>
</form>

<!-- Surveys Table -->
<h3>All Surveys</h3>

<?php if (empty($surveys)): ?>
    <p>No surveys yet. Upload a CSV file to create your first survey.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Topic</th>
                <th>Survey URL</th>
                <th>Questions</th>
                <th>Responses</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($surveys as $i => $s): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><?= esc($s['topic_name']) ?></td>
                <td>
                    <a href="<?= base_url('survey/' . $s['slug']) ?>" target="_blank">
                        /survey/<?= esc($s['slug']) ?>
                    </a>
                </td>
                <td><?= $s['question_count'] ?></td>
                <td><?= $s['response_count'] ?></td>
                <td>
                    <form method="POST" action="<?= base_url('admin/surveys/toggle/' . $s['id']) ?>" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn <?= $s['status'] === 'active' ? 'btn-success' : 'btn-warning' ?>">
                            <?= $s['status'] === 'active' ? 'Active' : 'Inactive' ?>
                        </button>
                    </form>
                </td>
                <td>
                    <a href="<?= base_url('admin/surveys/' . $s['id'] . '/results') ?>" class="btn btn-info">Results</a>
                    &nbsp;
                    <a href="<?= base_url('admin/surveys/delete/' . $s['id']) ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Delete &quot;<?= esc($s['topic_name']) ?>&quot; and all its data?')">
                        Delete
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?= $this->endSection() ?>