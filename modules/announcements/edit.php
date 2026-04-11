<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','captain','secretary']);
$page_title = 'Edit Announcement';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM announcements WHERE id=?");
$stmt->execute([$id]);
$ann = $stmt->fetch();
if (!$ann) { header('Location: index.php'); exit; }

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = sanitize($_POST['title']);
    $content = sanitize($_POST['content']);
    $cat     = sanitize($_POST['category']);
    $pri     = sanitize($_POST['priority']);
    $start   = $_POST['start_date'] ?: null;
    $end     = $_POST['end_date'] ?: null;
    $pub     = isset($_POST['is_published']) ? 1 : 0;

    if (!$title || !$content) { $error = 'Title and content required.'; }
    else {
        $pdo->prepare("UPDATE announcements SET title=?, content=?, category=?, priority=?, start_date=?, end_date=?, is_published=? WHERE id=?")
            ->execute([$title,$content,$cat,$pri,$start,$end,$pub,$id]);
        $success = 'Announcement updated successfully.';
        $ann = array_merge($ann, ['title'=>$title,'content'=>$content,'category'=>$cat,'priority'=>$pri,'start_date'=>$start,'end_date'=>$end,'is_published'=>$pub]);
    }
}

include '../../includes/header.php';
?>
<div class="page-header flex-between">
    <div><h1><i class="fas fa-edit"></i> Edit Announcement</h1></div>
    <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

<div class="card" style="max-width:800px;">
    <div class="card-header"><h3><i class="fas fa-edit"></i> Edit Form</h3></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group full-width"><label>Title *</label><input type="text" name="title" value="<?= htmlspecialchars($ann['title']) ?>" required></div>
                <div class="form-group"><label>Category</label>
                    <select name="category">
                        <?php foreach (['General','Health','Emergency','Events','Programs','Advisory'] as $c): ?>
                        <option value="<?= $c ?>" <?= $ann['category']===$c?'selected':'' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Priority</label>
                    <select name="priority">
                        <?php foreach (['Normal','Important','Urgent'] as $p): ?>
                        <option value="<?= $p ?>" <?= $ann['priority']===$p?'selected':'' ?>><?= $p ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Start Date</label><input type="date" name="start_date" value="<?= $ann['start_date'] ?>"></div>
                <div class="form-group"><label>End Date</label><input type="date" name="end_date" value="<?= $ann['end_date'] ?>"></div>
                <div class="form-group full-width"><label>Content *</label><textarea name="content" rows="8" required><?= htmlspecialchars($ann['content']) ?></textarea></div>
                <div class="form-group full-width">
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none;letter-spacing:0;font-size:14px;font-weight:500;">
                        <input type="checkbox" name="is_published" style="width:auto;" <?= $ann['is_published']?'checked':'' ?>> Published
                    </label>
                </div>
            </div>
            <div class="btn-group" style="justify-content:flex-end;">
                <a href="index.php" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
