<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','captain','secretary']);
$page_title = 'Post Announcement';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $category = sanitize($_POST['category']);
    $priority = sanitize($_POST['priority']);
    $start = $_POST['start_date'] ?: null;
    $end = $_POST['end_date'] ?: null;
    $published = isset($_POST['is_published']) ? 1 : 0;

    if (!$title || !$content) {
        $error = 'Title and content are required.';
    } else {
        $pdo->prepare("INSERT INTO announcements (title, content, category, priority, start_date, end_date, is_published, created_by) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$title, $content, $category, $priority, $start, $end, $published, $_SESSION['user_id']]);
        $success = 'Announcement posted successfully!';
    }
}

include '../../includes/header.php';
?>
<div class="page-header">
    <h1><i class="fas fa-bullhorn"></i> Post Announcement</h1>
    <p>Create and publish a new barangay announcement.</p>
</div>

<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?> <a href="index.php">View All</a></div><?php endif; ?>

<div class="card" style="max-width:800px;">
    <div class="card-header"><h3><i class="fas fa-edit"></i> Announcement Form</h3></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Title <span style="color:red;">*</span></label>
                    <input type="text" name="title" placeholder="Announcement title..." required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <select name="category">
                        <?php foreach (['General','Health','Emergency','Events','Programs','Advisory'] as $c): ?>
                        <option value="<?= $c ?>"><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Priority</label>
                    <select name="priority">
                        <option value="Normal">Normal</option>
                        <option value="Important">Important</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date">
                </div>
                <div class="form-group full-width">
                    <label>Content <span style="color:red;">*</span></label>
                    <textarea name="content" rows="8" placeholder="Write the full announcement content..." required></textarea>
                </div>
                <div class="form-group full-width">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none;letter-spacing:0;font-size:14px;font-weight:500;">
                        <input type="checkbox" name="is_published" checked style="width:auto;">
                        Publish immediately
                    </label>
                </div>
            </div>
            <div class="btn-group" style="justify-content:flex-end;">
                <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Post Announcement</button>
            </div>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
