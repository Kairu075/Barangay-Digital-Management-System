<?php
require_once 'includes/config.php';
requireLogin();

$page_title = 'Dashboard';

// Stats
$totalResidents = $pdo->query("SELECT COUNT(*) FROM residents WHERE is_active=1")->fetchColumn();
$totalHouseholds = $pdo->query("SELECT COUNT(*) FROM households")->fetchColumn();
$pendingDocs = $pdo->query("SELECT COUNT(*) FROM document_requests WHERE status IN ('Pending','Processing','For Approval')")->fetchColumn();
$pendingComplaints = $pdo->query("SELECT COUNT(*) FROM complaints WHERE status IN ('Pending','Under Investigation','Mediation')")->fetchColumn();
$seniorCitizens = $pdo->query("SELECT COUNT(*) FROM residents WHERE senior_citizen=1 AND is_active=1")->fetchColumn();
$pwdCount = $pdo->query("SELECT COUNT(*) FROM residents WHERE pwd=1 AND is_active=1")->fetchColumn();

// Monthly income
$monthlyIncome = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE MONTH(transaction_date)=MONTH(NOW()) AND YEAR(transaction_date)=YEAR(NOW())")->fetchColumn();

// Recent requests
$recentRequests = $pdo->query("SELECT dr.*, CONCAT(r.first_name,' ',r.last_name) as resident_name FROM document_requests dr JOIN residents r ON dr.resident_id=r.id ORDER BY dr.requested_at DESC LIMIT 5")->fetchAll();

// Recent complaints
$recentComplaints = $pdo->query("SELECT c.*, CONCAT(r.first_name,' ',r.last_name) as complainant FROM complaints c JOIN residents r ON c.complainant_id=r.id ORDER BY c.created_at DESC LIMIT 5")->fetchAll();

// Announcements
$announcements = $pdo->query("SELECT * FROM announcements WHERE is_published=1 AND (end_date IS NULL OR end_date >= CURDATE()) ORDER BY priority='Urgent' DESC, priority='Important' DESC, created_at DESC LIMIT 4")->fetchAll();

// Gender distribution
$genderData = $pdo->query("SELECT gender, COUNT(*) as count FROM residents WHERE is_active=1 GROUP BY gender")->fetchAll();

include 'includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-gauge-high"></i> Dashboard</h1>
    <p>Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?>! Here's your barangay overview.</p>
</div>

<!-- STAT CARDS -->
<div class="stats-grid">
    <div class="stat-card" style="--card-color:#446CAC;--card-bg:#e8eef7;">
        <div class="stat-icon"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= number_format($totalResidents) ?></div>
            <div class="stat-label">Total Residents</div>
            <div class="stat-sub"><i class="fas fa-home"></i> <?= $totalHouseholds ?> Households</div>
        </div>
    </div>
    <div class="stat-card" style="--card-color:#FBC531;--card-bg:#fffdf0;">
        <div class="stat-icon" style="background:#fffdf0;color:#d4a520;"><i class="fas fa-file-certificate"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= $pendingDocs ?></div>
            <div class="stat-label">Pending Documents</div>
            <div class="stat-sub" style="color:#856404;">Awaiting processing</div>
        </div>
    </div>
    <div class="stat-card" style="--card-color:#dc3545;--card-bg:#fff5f5;">
        <div class="stat-icon" style="background:#fff5f5;color:#dc3545;"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= $pendingComplaints ?></div>
            <div class="stat-label">Active Complaints</div>
            <div class="stat-sub" style="color:#dc3545;">Needs attention</div>
        </div>
    </div>
    <div class="stat-card" style="--card-color:#28a745;--card-bg:#f0fff4;">
        <div class="stat-icon" style="background:#f0fff4;color:#28a745;"><i class="fas fa-peso-sign"></i></div>
        <div class="stat-info">
            <div class="stat-value">₱<?= number_format($monthlyIncome, 0) ?></div>
            <div class="stat-label">This Month's Collection</div>
            <div class="stat-sub">Total transactions</div>
        </div>
    </div>
    <div class="stat-card" style="--card-color:#6f42c1;--card-bg:#f5f0ff;">
        <div class="stat-icon" style="background:#f5f0ff;color:#6f42c1;"><i class="fas fa-person-cane"></i></div>
        <div class="stat-info">
            <div class="stat-value"><?= $seniorCitizens ?></div>
            <div class="stat-label">Senior Citizens</div>
            <div class="stat-sub" style="color:#6f42c1;"><i class="fas fa-wheelchair"></i> <?= $pwdCount ?> PWD</div>
        </div>
    </div>
</div>

<!-- MAIN GRID -->f
<div class="dashboard-grid">
    <div>
        <!-- Recent Document Requests -->
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="fas fa-file-lines"></i> Recent Document Requests</h3>
                <a href="<?= SITE_URL ?>/modules/documents/index.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Request No.</th>
                                <th>Resident</th>
                                <th>Document</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRequests as $req): ?>
                            <tr>
                                <td><strong><?= $req['request_no'] ?></strong></td>
                                <td><?= htmlspecialchars($req['resident_name']) ?></td>
                                <td style="font-size:12px;"><?= $req['document_type'] ?></td>
                                <td>
                                    <span class="badge badge-<?= strtolower($req['status']) ?>">
                                        <?= $req['status'] ?>
                                    </span>
                                </td>
                                <td style="font-size:12px;"><?= date('M d, Y', strtotime($req['requested_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Complaints -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-triangle-exclamation"></i> Recent Complaints</h3>
                <a href="<?= SITE_URL ?>/modules/complaints/index.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="card-body" style="padding:0;">
                <div class="table-wrap">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Complainant</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentComplaints as $cmp): ?>
                            <tr>
                                <td><strong><?= $cmp['complaint_no'] ?></strong></td>
                                <td><?= htmlspecialchars($cmp['complainant']) ?></td>
                                <td style="font-size:12px;"><?= $cmp['complaint_type'] ?></td>
                                <td>
                                    <span class="badge badge-<?= strtolower(str_replace(' ','-',$cmp['status'])) ?>">
                                        <?= $cmp['status'] ?>
                                    </span>
                                </td>
                                <td style="font-size:12px;"><?= date('M d, Y', strtotime($cmp['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN -->
    <div>
        <!-- Quick Actions -->
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="fas fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <a href="<?= SITE_URL ?>/modules/residents/add.php" class="btn btn-primary btn-sm" style="justify-content:center;">
                        <i class="fas fa-user-plus"></i> Add Resident
                    </a>
                    <a href="<?= SITE_URL ?>/modules/documents/add.php" class="btn btn-gold btn-sm" style="justify-content:center;">
                        <i class="fas fa-file-plus"></i> New Request
                    </a>
                    <a href="<?= SITE_URL ?>/modules/complaints/add.php" class="btn btn-outline btn-sm" style="justify-content:center;">
                        <i class="fas fa-flag"></i> File Complaint
                    </a>
                    <a href="<?= SITE_URL ?>/modules/announcements/add.php" class="btn btn-outline btn-sm" style="justify-content:center;">
                        <i class="fas fa-bullhorn"></i> Post Notice
                    </a>
                    <a href="<?= SITE_URL ?>/modules/finance/index.php" class="btn btn-sm" style="background:#28a745;color:white;justify-content:center;border:none;">
                        <i class="fas fa-receipt"></i> Transactions
                    </a>
                    <a href="<?= SITE_URL ?>/modules/finance/report.php" class="btn btn-sm" style="background:#6f42c1;color:white;justify-content:center;border:none;">
                        <i class="fas fa-chart-bar"></i> Reports
                    </a>
                </div>
            </div>
        </div>

        <!-- Announcements -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-bullhorn"></i> Latest Announcements</h3>
                <a href="<?= SITE_URL ?>/modules/announcements/index.php" class="btn btn-sm btn-outline">View All</a>
            </div>
            <div class="card-body">
                <?php foreach ($announcements as $ann): ?>
                <div class="announcement-item <?= strtolower($ann['priority']) ?>">
                    <div class="ann-title"><?= htmlspecialchars($ann['title']) ?></div>
                    <div class="ann-meta">
                        <span><i class="fas fa-tag" style="font-size:10px;"></i> <?= $ann['category'] ?></span>
                        <span class="badge badge-<?= strtolower($ann['priority']) ?>" style="font-size:9px;"><?= $ann['priority'] ?></span>
                    </div>
                    <div class="ann-excerpt"><?= mb_substr(strip_tags($ann['content']), 0, 90) ?>...</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
