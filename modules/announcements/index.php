<?php
require_once '../../includes/config.php';
requireLogin();
$page_title = 'Announcements';

// Handle publish toggle
$action = $_POST['action'] ?? '';
if ($action === 'toggle') {
    $id = (int)$_POST['ann_id'];
    $pdo->prepare("UPDATE announcements SET is_published = NOT is_published WHERE id=?")->execute([$id]);
    header('Location: index.php');
    exit;
}
if ($action === 'delete') {
    $pdo->prepare("DELETE FROM announcements WHERE id=?")->execute([(int)$_POST['ann_id']]);
    header('Location: index.php');
    exit;
}

$announcements = $pdo->query("SELECT a.*, u.full_name as author FROM announcements a LEFT JOIN users u ON a.created_by=u.id ORDER BY a.created_at DESC")->fetchAll();

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-bullhorn"></i> Announcements</h1>
        <p>Manage and publish barangay announcements, events, and advisories.</p>
    </div>
    <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Post Announcement</a>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> All Announcements (<?= count($announcements) ?>)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th>Views</th>
                        <th>Author</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($announcements as $ann): ?>
                    <tr>
                        <td>
                            <div style="font-weight:600;max-width:250px;"><?= htmlspecialchars($ann['title']) ?></div>
                            <div style="font-size:12px;color:var(--gray-500);"><?= mb_substr(strip_tags($ann['content']), 0, 60) ?>...</div>
                        </td>
                        <td><span class="badge badge-normal"><?= $ann['category'] ?></span></td>
                        <td><span class="badge badge-<?= strtolower($ann['priority']) ?>"><?= $ann['priority'] ?></span></td>
                        <td style="font-size:12px;"><?= $ann['start_date'] ? date('M d, Y', strtotime($ann['start_date'])) : '—' ?></td>
                        <td style="font-size:12px;"><?= $ann['end_date'] ? date('M d, Y', strtotime($ann['end_date'])) : '—' ?></td>
                        <td>
                            <span class="badge <?= $ann['is_published'] ? 'badge-approved' : 'badge-rejected' ?>">
                                <?= $ann['is_published'] ? 'Published' : 'Draft' ?>
                            </span>
                        </td>
                        <td><?= $ann['views'] ?></td>
                        <td style="font-size:12px;"><?= htmlspecialchars($ann['author'] ?? 'System') ?></td>
                        <td>
                            <div class="btn-group">
                                <button onclick="openModal('viewAnn<?= $ann['id'] ?>')" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button>
                                <?php if (hasRole(['admin','captain','secretary'])): ?>
                                <a href="edit.php?id=<?= $ann['id'] ?>" class="btn btn-sm btn-gold"><i class="fas fa-edit"></i></a>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="toggle">
                                    <input type="hidden" name="ann_id" value="<?= $ann['id'] ?>">
                                    <button type="submit" class="btn btn-sm" style="background:<?= $ann['is_published'] ? '#dc3545' : '#28a745' ?>;color:white;border:none;" title="<?= $ann['is_published'] ? 'Unpublish' : 'Publish' ?>">
                                        <i class="fas fa-<?= $ann['is_published'] ? 'eye-slash' : 'check' ?>"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <!-- View Modal -->
                    <div class="modal-backdrop" id="viewAnn<?= $ann['id'] ?>">
                        <div class="modal">
                            <div class="modal-header">
                                <h3><i class="fas fa-bullhorn"></i> <?= htmlspecialchars($ann['title']) ?></h3>
                                <button class="modal-close" onclick="closeModal('viewAnn<?= $ann['id'] ?>')"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="modal-body">
                                <div style="display:flex;gap:10px;margin-bottom:14px;flex-wrap:wrap;">
                                    <span class="badge badge-normal"><?= $ann['category'] ?></span>
                                    <span class="badge badge-<?= strtolower($ann['priority']) ?>"><?= $ann['priority'] ?></span>
                                    <span class="badge <?= $ann['is_published'] ? 'badge-approved' : 'badge-rejected' ?>"><?= $ann['is_published'] ? 'Published' : 'Draft' ?></span>
                                </div>
                                <div style="line-height:1.7;font-size:14px;white-space:pre-line;"><?= nl2br(htmlspecialchars($ann['content'])) ?></div>
                                <div style="margin-top:14px;font-size:12px;color:var(--gray-500);">
                                    Posted by <?= htmlspecialchars($ann['author'] ?? 'System') ?> on <?= date('F d, Y', strtotime($ann['created_at'])) ?>
                                    <?php if ($ann['start_date']): ?> | Valid: <?= date('M d', strtotime($ann['start_date'])) ?> – <?= $ann['end_date'] ? date('M d, Y', strtotime($ann['end_date'])) : 'Ongoing' ?><?php endif; ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-outline" onclick="closeModal('viewAnn<?= $ann['id'] ?>')">Close</button>
                            </div>
                        </div>
                    </div>

                    <?php endforeach; ?>
                    <?php if (empty($announcements)): ?>
                    <tr><td colspan="9" class="text-center text-muted" style="padding:30px;">No announcements found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
