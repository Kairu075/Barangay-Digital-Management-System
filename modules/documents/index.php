<?php
require_once '../../includes/config.php';
requireLogin();
$page_title = 'Document Requests';

$search = sanitize($_GET['search'] ?? '');
$filter_status = sanitize($_GET['status'] ?? '');
$filter_type = sanitize($_GET['type'] ?? '');

$sql = "SELECT dr.*, CONCAT(r.first_name,' ',r.last_name) as resident_name, r.resident_id as res_id FROM document_requests dr JOIN residents r ON dr.resident_id=r.id WHERE 1";
$params = [];

if ($search) {
    $sql .= " AND (r.first_name LIKE ? OR r.last_name LIKE ? OR dr.request_no LIKE ?)";
    $s = "%$search%";
    $params = array_merge($params, [$s,$s,$s]);
}
if ($filter_status) { $sql .= " AND dr.status = ?"; $params[] = $filter_status; }
if ($filter_type) { $sql .= " AND dr.document_type = ?"; $params[] = $filter_type; }

$sql .= " ORDER BY dr.requested_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$requests = $stmt->fetchAll();

/// Handle status update
$action = $_POST['action'] ?? '';
if ($action === 'update_status') {
    $id = (int)$_POST['req_id'];
    $status = $_POST['new_status'];
    $pdo->prepare("UPDATE document_requests SET status=? WHERE id=?")->execute([$status, $id]);
    if ($status === 'Approved') {
        $pdo->prepare("UPDATE document_requests SET approved_by=?, approved_at=NOW() WHERE id=?")->execute([$_SESSION['user_id'], $id]);
    }
    if ($status === 'Released') {
        $pdo->prepare("UPDATE document_requests SET released_by=?, released_at=NOW() WHERE id=?")->execute([$_SESSION['user_id'], $id]);
    }
    header('Location: index.php');
    exit;
}

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-file-certificate"></i> Document Requests</h1>
        <p>Manage all document requests and generate official certificates.</p>
    </div>
    <a href="add.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Request</a>
</div>

<!-- FILTER -->
<div class="card mb-3">
    <div class="card-body" style="padding:16px;">
        <form method="GET" class="filter-bar">
            <div class="search-wrap" style="flex:2;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by name or request no..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <select name="status" style="width:160px;">
                <option value="">All Status</option>
                <?php foreach (['Pending','Processing','For Approval','Approved','Released','Rejected'] as $s): ?>
                <option value="<?= $s ?>" <?= $filter_status===$s?'selected':'' ?>><?= $s ?></option>
                <?php endforeach; ?>
            </select>
            <select name="type" style="width:200px;">
                <option value="">All Document Types</option>
                <?php foreach (['Barangay Clearance','Certificate of Residency','Indigency Certificate','Business Clearance','Certificate of Good Moral Character'] as $t): ?>
                <option value="<?= $t ?>" <?= $filter_type===$t?'selected':'' ?>><?= $t ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filter</button>
            <a href="index.php" class="btn btn-outline"><i class="fas fa-times"></i> Clear</a>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Requests (<?= count($requests) ?>)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Request No.</th>
                        <th>Resident</th>
                        <th>Document Type</th>
                        <th>Purpose</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($requests as $req): ?>
                    <tr>
                        <td><strong style="color:var(--blue);"><?= $req['request_no'] ?></strong></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($req['resident_name']) ?></div>
                            <div style="font-size:11px;color:var(--gray-500);"><?= $req['res_id'] ?></div>
                        </td>
                        <td style="font-size:12.5px;"><?= $req['document_type'] ?></td>
                        <td style="font-size:12px;color:var(--gray-600);"><?= htmlspecialchars(mb_substr($req['purpose'],0,30)) ?>...</td>
                        <td><strong>₱<?= number_format($req['amount'],2) ?></strong></td>
                        <td><span class="badge badge-<?= strtolower($req['payment_status']) ?>"><?= $req['payment_status'] ?></span></td>
                        <td>
                            <span class="badge badge-<?= strtolower($req['status']) ?>"><?= $req['status'] ?></span>
                        </td>
                        <td style="font-size:12px;"><?= date('M d, Y', strtotime($req['requested_at'])) ?></td>
                        <td>
                            <div class="btn-group">
                                <a href="generate.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-primary" title="View/Generate Document"><i class="fas fa-file-arrow-down"></i></a>
                                <?php if (hasRole(['admin','secretary','captain'])): ?>
                                <button onclick="openModal('statusModal<?= $req['id'] ?>')" class="btn btn-sm btn-gold" title="Update Status"><i class="fas fa-edit"></i></button>
                                <?php endif; ?>
                                <?php if ($req['payment_status'] === 'Unpaid' && hasRole(['admin','treasurer'])): ?>
                                <a href="payment.php?id=<?= $req['id'] ?>" class="btn btn-sm btn-success" title="Record Payment"><i class="fas fa-peso-sign"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>

                    <!-- Status Update Modal -->
                    <div class="modal-backdrop" id="statusModal<?= $req['id'] ?>">
                        <div class="modal" style="max-width:400px;">
                            <div class="modal-header">
                                <h3><i class="fas fa-edit"></i> Update Status</h3>
                                <button class="modal-close" onclick="closeModal('statusModal<?= $req['id'] ?>')"><i class="fas fa-times"></i></button>
                            </div>
                            <form method="POST">
                                <input type="hidden" name="action" value="update_status">
                                <input type="hidden" name="req_id" value="<?= $req['id'] ?>">
                                <div class="modal-body">
                                    <p style="margin-bottom:12px;font-size:13.5px;"><strong><?= $req['request_no'] ?></strong> — <?= htmlspecialchars($req['resident_name']) ?></p>
                                    <div class="form-group">
                                        <label>New Status</label>
                                        <select name="new_status">
                                            <?php foreach (['Pending','Processing','For Approval','Approved','Released','Rejected'] as $s): ?>
                                            <option value="<?= $s ?>" <?= $req['status']===$s?'selected':'' ?>><?= $s ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline" onclick="closeModal('statusModal<?= $req['id'] ?>')">Cancel</button>
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php if (empty($requests)): ?>
                    <tr><td colspan="9" class="text-center text-muted" style="padding:30px;">No document requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
