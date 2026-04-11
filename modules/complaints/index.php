<?php
require_once '../../includes/config.php';
requireLogin();
$page_title = 'Complaints';

$search = sanitize($_GET['search'] ?? '');
$filter_status = sanitize($_GET['status'] ?? '');

$sql = "SELECT c.*, CONCAT(r.first_name,' ',r.last_name) as complainant_name FROM complaints c JOIN residents r ON c.complainant_id=r.id WHERE 1";
$params = [];

if ($search) {
    $sql .= " AND (r.first_name LIKE ? OR r.last_name LIKE ? OR c.complaint_no LIKE ? OR c.complaint_type LIKE ?)";
    $s = "%$search%";
    $params = [$s,$s,$s,$s];
}
if ($filter_status) { $sql .= " AND c.status=?"; $params[] = $filter_status; }
$sql .= " ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$complaints = $stmt->fetchAll();

// Update status handler
$action = $_POST['action'] ?? '';
if ($action === 'update_status') {
    $pdo->prepare("UPDATE complaints SET status=?, admin_notes=?, updated_at=NOW() WHERE id=?")
        ->execute([$_POST['new_status'], sanitize($_POST['admin_notes']), (int)$_POST['cmp_id']]);
    header('Location: index.php');
    exit;
}

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-triangle-exclamation"></i> Complaint & Incident Management</h1>
        <p>Track and manage community complaints and incidents.</p>
    </div>
    <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> File Complaint</a>
</div>

<!-- STATUS SUMMARY -->
<div class="stats-grid" style="grid-template-columns:repeat(5,1fr);margin-bottom:24px;">
    <?php
    $statuses_summary = [
        'Pending' => ['color'=>'#856404','bg'=>'#fff3cd'],
        'Under Investigation' => ['color'=>'#004085','bg'=>'#cce5ff'],
        'Mediation' => ['color'=>'#432874','bg'=>'#e2d9f3'],
        'Resolved' => ['color'=>'#155724','bg'=>'#d4edda'],
        'Dismissed' => ['color'=>'#6c757d','bg'=>'#e9ecef'],
    ];
    foreach ($statuses_summary as $stat => $style):
        $cnt = $pdo->prepare("SELECT COUNT(*) FROM complaints WHERE status=?");
        $cnt->execute([$stat]);
        $c = $cnt->fetchColumn();
    ?>
    <div class="stat-card" style="--card-color:<?= $style['color'] ?>;--card-bg:<?= $style['bg'] ?>;padding:14px;">
        <div class="stat-info">
            <div class="stat-value" style="font-size:24px;color:<?= $style['color'] ?>;"><?= $c ?></div>
            <div class="stat-label" style="font-size:11px;"><?= $stat ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- FILTER -->
<div class="card mb-3">
    <div class="card-body" style="padding:16px;">
        <form method="GET" class="filter-bar">
            <div class="search-wrap" style="flex:2;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by name, case no., or type..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <select name="status" style="width:200px;">
                <option value="">All Status</option>
                <?php foreach (['Pending','Under Investigation','Mediation','Resolved','Dismissed','Escalated'] as $s): ?>
                <option value="<?= $s ?>" <?= $filter_status===$s?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
            <a href="index.php" class="btn btn-outline"><i class="fas fa-times"></i> Clear</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Complaints (<?= count($complaints) ?>)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Case No.</th>
                        <th>Complainant</th>
                        <th>Respondent</th>
                        <th>Type</th>
                        <th>Incident Date</th>
                        <th>Status</th>
                        <th>Filed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($complaints as $cmp): ?>
                    <tr>
                        <td><strong style="color:var(--blue);"><?= $cmp['complaint_no'] ?></strong></td>
                        <td style="font-weight:600;"><?= htmlspecialchars($cmp['complainant_name']) ?></td>
                        <td style="font-size:12.5px;"><?= htmlspecialchars($cmp['respondent_name'] ?: 'Unknown') ?></td>
                        <td><span style="font-size:12px;"><?= $cmp['complaint_type'] ?></span></td>
                        <td style="font-size:12px;"><?= $cmp['incident_date'] ? date('M d, Y', strtotime($cmp['incident_date'])) : 'N/A' ?></td>
                        <td>
                            <span class="badge badge-<?= strtolower(str_replace(' ','-',$cmp['status'])) ?>">
                                <?= $cmp['status'] ?>
                            </span>
                        </td>
                        <td style="font-size:12px;"><?= date('M d, Y', strtotime($cmp['created_at'])) ?></td>
                        <td>
                            <div class="btn-group">
                                <button onclick="openModal('viewCmp<?= $cmp['id'] ?>')" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></button>
                                <?php if (hasRole(['admin','secretary','captain'])): ?>
                                <button onclick="openModal('updCmp<?= $cmp['id'] ?>')" class="btn btn-sm btn-gold"><i class="fas fa-edit"></i></button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <!-- View Modal -->
                    <div class="modal-backdrop" id="viewCmp<?= $cmp['id'] ?>">
                        <div class="modal">
                            <div class="modal-header">
                                <h3><i class="fas fa-eye"></i> <?= $cmp['complaint_no'] ?></h3>
                                <button class="modal-close" onclick="closeModal('viewCmp<?= $cmp['id'] ?>')"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="modal-body">
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;font-size:13.5px;">
                                    <div><strong>Complainant:</strong> <?= htmlspecialchars($cmp['complainant_name']) ?></div>
                                    <div><strong>Case No.:</strong> <?= $cmp['complaint_no'] ?></div>
                                    <div><strong>Respondent:</strong> <?= htmlspecialchars($cmp['respondent_name'] ?: 'N/A') ?></div>
                                    <div><strong>Type:</strong> <?= $cmp['complaint_type'] ?></div>
                                    <div><strong>Incident Date:</strong> <?= $cmp['incident_date'] ? date('F d, Y', strtotime($cmp['incident_date'])) : 'N/A' ?></div>
                                    <div><strong>Location:</strong> <?= htmlspecialchars($cmp['incident_location'] ?: 'N/A') ?></div>
                                    <div><strong>Status:</strong> <span class="badge badge-<?= strtolower(str_replace(' ','-',$cmp['status'])) ?>"><?= $cmp['status'] ?></span></div>
                                    <div><strong>Filed:</strong> <?= date('F d, Y', strtotime($cmp['created_at'])) ?></div>
                                </div>
                                <div style="margin-bottom:12px;">
                                    <strong style="display:block;margin-bottom:6px;">Description:</strong>
                                    <p style="background:var(--gray-100);padding:12px;border-radius:8px;font-size:13.5px;line-height:1.7;"><?= nl2br(htmlspecialchars($cmp['description'])) ?></p>
                                </div>
                                <?php if ($cmp['admin_notes']): ?>
                                <div>
                                    <strong style="display:block;margin-bottom:6px;">Admin Notes:</strong>
                                    <p style="background:#fff3cd;padding:12px;border-radius:8px;font-size:13.5px;line-height:1.7;"><?= nl2br(htmlspecialchars($cmp['admin_notes'])) ?></p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-outline" onclick="closeModal('viewCmp<?= $cmp['id'] ?>')">Close</button>
                            </div>
                        </div>
                    </div>

                    <!-- Update Modal -->
                    <div class="modal-backdrop" id="updCmp<?= $cmp['id'] ?>">
                        <div class="modal" style="max-width:450px;">
                            <div class="modal-header">
                                <h3><i class="fas fa-edit"></i> Update Case Status</h3>
                                <button class="modal-close" onclick="closeModal('updCmp<?= $cmp['id'] ?>')"><i class="fas fa-times"></i></button>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="cmp_id" value="<?= $cmp['id'] ?>">
                                <div class="modal-body">
                                    <div class="form-group">
                                        <label>New Status</label>
                                        <select name="new_status">
                                            <?php foreach (['Pending','Under Investigation','Mediation','Resolved','Dismissed','Escalated'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $cmp['status']===$s?'selected':'' ?>><?= $s ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Admin Notes</label>
                                        <textarea name="admin_notes" rows="4" placeholder="Add case notes, resolution details..."><?= htmlspecialchars($cmp['admin_notes']) ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline" onclick="closeModal('updCmp<?= $cmp['id'] ?>')">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php endforeach; ?>
                    <?php if (empty($complaints)): ?>
                    <tr><td colspan="8" class="text-center text-muted" style="padding:30px;">No complaints found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
