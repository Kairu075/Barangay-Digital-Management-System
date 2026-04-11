<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','secretary','captain']);
$page_title = 'Residents';

$search = sanitize($_GET['search'] ?? '');
$filter_gender = sanitize($_GET['gender'] ?? '');
$filter_purok = sanitize($_GET['purok'] ?? '');

$sql = "SELECT r.*, h.address, h.purok, h.household_no FROM residents r LEFT JOIN households h ON r.household_id=h.id WHERE r.is_active=1";
$params = [];

if ($search) {
    $sql .= " AND (r.first_name LIKE ? OR r.last_name LIKE ? OR r.resident_id LIKE ? OR r.contact_no LIKE ?)";
    $s = "%$search%";
    $params = array_merge($params, [$s,$s,$s,$s]);
}
if ($filter_gender) {
    $sql .= " AND r.gender = ?";
    $params[] = $filter_gender;
}
if ($filter_purok) {
    $sql .= " AND h.purok = ?";
    $params[] = $filter_purok;
}

$sql .= " ORDER BY r.last_name, r.first_name";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$residents = $stmt->fetchAll();

$puroks = $pdo->query("SELECT DISTINCT purok FROM households WHERE purok IS NOT NULL ORDER BY purok")->fetchAll(PDO::FETCH_COLUMN);

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-users"></i> Residents</h1>
        <p>Manage and view all registered residents of Barangay San Marino.</p>
    </div>
    <a href="add.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Resident</a>
</div>

<!-- STATS ROW -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card" style="--card-color:#446CAC;--card-bg:#e8eef7;padding:16px;">
        <div class="stat-icon" style="width:42px;height:42px;font-size:18px;"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:22px;"><?= count($residents) ?></div>
            <div class="stat-label">Total Shown</div>
        </div>
    </div>
    <div class="stat-card" style="--card-color:#446CAC;--card-bg:#e8eef7;padding:16px;">
        <div class="stat-icon" style="width:42px;height:42px;font-size:18px;color:#446CAC;background:#e8eef7;"><i class="fas fa-mars"></i></div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:22px;"><?= $pdo->query("SELECT COUNT(*) FROM residents WHERE gender='Male' AND is_active=1")->fetchColumn() ?></div>
            <div class="stat-label">Male</div>
        </div>
    </div>
    <div class="stat-card" style="--card-color:#e83e8c;--card-bg:#fff0f7;padding:16px;">
        <div class="stat-icon" style="width:42px;height:42px;font-size:18px;color:#e83e8c;background:#fff0f7;"><i class="fas fa-venus"></i></div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:22px;"><?= $pdo->query("SELECT COUNT(*) FROM residents WHERE gender='Female' AND is_active=1")->fetchColumn() ?></div>
            <div class="stat-label">Female</div>
        </div>
    </div>
    <div class="stat-card" style="--card-color:#6f42c1;--card-bg:#f5f0ff;padding:16px;">
        <div class="stat-icon" style="width:42px;height:42px;font-size:18px;color:#6f42c1;background:#f5f0ff;"><i class="fas fa-person-cane"></i></div>
        <div class="stat-info">
            <div class="stat-value" style="font-size:22px;"><?= $pdo->query("SELECT COUNT(*) FROM residents WHERE senior_citizen=1 AND is_active=1")->fetchColumn() ?></div>
            <div class="stat-label">Senior Citizens</div>
        </div>
    </div>
</div>

<!-- FILTERS -->
<div class="card mb-3">
    <div class="card-body" style="padding:16px;">
        <form method="GET" class="filter-bar">
            <div class="search-wrap" style="flex:2;">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search by name, ID, or contact..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <select name="gender" style="width:140px;">
                <option value="">All Gender</option>
                <option value="Male" <?= $filter_gender==='Male'?'selected':'' ?>>Male</option>
                <option value="Female" <?= $filter_gender==='Female'?'selected':'' ?>>Female</option>
            </select>
            <select name="purok" style="width:150px;">
                <option value="">All Puroks</option>
                <?php foreach ($puroks as $p): ?>
                <option value="<?= $p ?>" <?= $filter_purok===$p?'selected':'' ?>><?= $p ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-filter"></i> Filter
            </button>
            <a href="index.php" class="btn btn-outline">
                <i class="fas fa-times"></i> Clear
            </a>
        </form>
    </div>
</div>

<!-- TABLE -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-list"></i> Resident Records (<?= count($residents) ?> results)</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Resident ID</th>
                        <th>Full Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Civil Status</th>
                        <th>Purok</th>
                        <th>Contact</th>
                        <th>Special</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($residents as $res): ?>
                    <?php $age = getAge($res['birthdate']); ?>
                    <tr>
                        <td><strong style="color:var(--blue);"><?= $res['resident_id'] ?></strong></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($res['last_name'].', '.$res['first_name'].' '.($res['middle_name']??'')) ?></div>
                            <div style="font-size:11px;color:var(--gray-500);"><?= $res['occupation'] ?: 'N/A' ?></div>
                        </td>
                        <td><?= $age ?></td>
                        <td><?= $res['gender'] ?></td>
                        <td><?= $res['civil_status'] ?></td>
                        <td><?= $res['purok'] ?? 'N/A' ?></td>
                        <td style="font-size:12px;"><?= $res['contact_no'] ?: 'N/A' ?></td>
                        <td>
                            <?php if ($res['senior_citizen']): ?><span class="badge" style="background:#e2d9f3;color:#432874;font-size:9px;">SC</span><?php endif; ?>
                            <?php if ($res['pwd']): ?><span class="badge" style="background:#cce5ff;color:#004085;font-size:9px;">PWD</span><?php endif; ?>
                            <?php if ($res['voter_status']): ?><span class="badge" style="background:#d4edda;color:#155724;font-size:9px;">Voter</span><?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <a href="view.php?id=<?= $res['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>
                                <a href="edit.php?id=<?= $res['id'] ?>" class="btn btn-sm btn-gold"><i class="fas fa-edit"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($residents)): ?>
                    <tr><td colspan="9" class="text-center text-muted" style="padding:30px;">No residents found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
