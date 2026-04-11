<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','secretary','captain']);
$page_title = 'View Resident';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT r.*, h.address, h.purok, h.household_no, h.household_head FROM residents r LEFT JOIN households h ON r.household_id=h.id WHERE r.id=?");
$stmt->execute([$id]);
$res = $stmt->fetch();
if (!$res) { header('Location: index.php'); exit; }

$age = getAge($res['birthdate']);

// Get document requests
$docs = $pdo->prepare("SELECT * FROM document_requests WHERE resident_id=? ORDER BY requested_at DESC LIMIT 10");
$docs->execute([$id]);
$docs = $docs->fetchAll();

// Get complaints
$cmps = $pdo->prepare("SELECT * FROM complaints WHERE complainant_id=? ORDER BY created_at DESC LIMIT 5");
$cmps->execute([$id]);
$cmps = $cmps->fetchAll();

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-id-card"></i> Resident Profile</h1>
        <p><?= htmlspecialchars($res['first_name'].' '.$res['last_name']) ?> — <?= $res['resident_id'] ?></p>
    </div>
    <div class="btn-group">
        <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
        <a href="edit.php?id=<?= $id ?>" class="btn btn-gold"><i class="fas fa-edit"></i> Edit</a>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:24px;">
    <!-- LEFT: Profile Card -->
    <div>
        <div class="card mb-3">
            <div style="background:linear-gradient(135deg,#1e3557,#446CAC);padding:30px;text-align:center;">
                <div style="width:80px;height:80px;background:var(--gold);border-radius:50%;margin:0 auto 14px;display:flex;align-items:center;justify-content:center;font-size:32px;font-weight:800;color:var(--blue-dark);box-shadow:0 4px 20px rgba(251,197,49,0.4);">
                    <?= strtoupper(substr($res['first_name'],0,1)) ?>
                </div>
                <div style="font-family:'Lora',serif;font-size:18px;font-weight:700;color:white;"><?= htmlspecialchars($res['first_name'].' '.($res['middle_name']?$res['middle_name'][0].'. ':'').$res['last_name'].($res['suffix']?' '.$res['suffix']:'')) ?></div>
                <div style="color:rgba(255,255,255,0.6);font-size:12px;margin-top:4px;"><?= $res['resident_id'] ?></div>
                <div style="display:flex;justify-content:center;gap:6px;margin-top:12px;flex-wrap:wrap;">
                    <?php if ($res['voter_status']): ?><span class="badge" style="background:rgba(40,167,69,0.3);color:#5dd879;font-size:9px;">VOTER</span><?php endif; ?>
                    <?php if ($res['senior_citizen']): ?><span class="badge" style="background:rgba(111,66,193,0.3);color:#b388ff;font-size:9px;">SENIOR CITIZEN</span><?php endif; ?>
                    <?php if ($res['pwd']): ?><span class="badge" style="background:rgba(23,162,184,0.3);color:#7ee8f5;font-size:9px;">PWD</span><?php endif; ?>
                    <?php if ($res['solo_parent']): ?><span class="badge" style="background:rgba(255,193,7,0.3);color:#ffe082;font-size:9px;">SOLO PARENT</span><?php endif; ?>
                </div>
            </div>
            <div class="card-body">
                <?php
                $fields = [
                    'Age' => $age . ' years old',
                    'Gender' => $res['gender'],
                    'Civil Status' => $res['civil_status'],
                    'Birthdate' => formatDate($res['birthdate']),
                    'Birthplace' => $res['birthplace'] ?: 'N/A',
                    'Nationality' => $res['nationality'],
                    'Religion' => $res['religion'] ?: 'N/A',
                ];
                foreach ($fields as $label => $val):
                ?>
                <div style="display:flex;justify-content:space-between;padding:7px 0;border-bottom:1px solid var(--gray-100);font-size:13px;">
                    <span style="color:var(--gray-500);"><?= $label ?></span>
                    <span style="font-weight:600;"><?= htmlspecialchars($val) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Request Button -->
        <div class="card">
            <div class="card-body" style="text-align:center;">
                <div style="font-size:13px;color:var(--gray-500);margin-bottom:12px;">Request Document for this Resident</div>
                <a href="../documents/add.php" class="btn btn-primary" style="width:100%;justify-content:center;">
                    <i class="fas fa-file-plus"></i> New Document Request
                </a>
            </div>
        </div>
    </div>

    <!-- RIGHT: Details -->
    <div>
        <div class="card mb-3">
            <div class="card-header"><h3><i class="fas fa-home"></i> Address & Contact</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13.5px;">
                    <div><span style="color:var(--gray-500);">Household No.</span><br><strong><?= $res['household_no'] ?: 'N/A' ?></strong></div>
                    <div><span style="color:var(--gray-500);">Purok</span><br><strong><?= $res['purok'] ?: 'N/A' ?></strong></div>
                    <div style="grid-column:1/-1;"><span style="color:var(--gray-500);">Address</span><br><strong><?= htmlspecialchars($res['address'] ?: 'N/A') ?></strong></div>
                    <div><span style="color:var(--gray-500);">Contact No.</span><br><strong><?= $res['contact_no'] ?: 'N/A' ?></strong></div>
                    <div><span style="color:var(--gray-500);">Email</span><br><strong><?= $res['email'] ?: 'N/A' ?></strong></div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h3><i class="fas fa-briefcase"></i> Employment & Education</h3></div>
            <div class="card-body">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;font-size:13.5px;">
                    <div><span style="color:var(--gray-500);">Occupation</span><br><strong><?= $res['occupation'] ?: 'N/A' ?></strong></div>
                    <div><span style="color:var(--gray-500);">Monthly Income</span><br><strong><?= formatCurrency($res['monthly_income']) ?></strong></div>
                    <div style="grid-column:1/-1;"><span style="color:var(--gray-500);">Educational Attainment</span><br><strong><?= $res['educational_attainment'] ?: 'N/A' ?></strong></div>
                </div>
            </div>
        </div>

        <!-- Document History -->
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="fas fa-file-lines"></i> Document History</h3>
            </div>
            <div class="card-body" style="padding:0;">
                <?php if (empty($docs)): ?>
                <div class="text-center text-muted" style="padding:20px;">No documents requested yet.</div>
                <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>Request No.</th><th>Document</th><th>Status</th><th>Amount</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($docs as $d): ?>
                        <tr>
                            <td><a href="../documents/generate.php?id=<?= $d['id'] ?>" style="color:var(--blue);font-weight:600;"><?= $d['request_no'] ?></a></td>
                            <td style="font-size:12px;"><?= $d['document_type'] ?></td>
                            <td><span class="badge badge-<?= strtolower($d['status']) ?>"><?= $d['status'] ?></span></td>
                            <td>₱<?= number_format($d['amount'],2) ?></td>
                            <td style="font-size:11px;"><?= date('M d, Y', strtotime($d['requested_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Complaint History -->
        <div class="card">
            <div class="card-header"><h3><i class="fas fa-triangle-exclamation"></i> Complaint History</h3></div>
            <div class="card-body" style="padding:0;">
                <?php if (empty($cmps)): ?>
                <div class="text-center text-muted" style="padding:20px;">No complaints filed.</div>
                <?php else: ?>
                <table class="data-table">
                    <thead><tr><th>Case No.</th><th>Type</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach ($cmps as $c): ?>
                        <tr>
                            <td style="font-weight:600;"><?= $c['complaint_no'] ?></td>
                            <td style="font-size:12px;"><?= $c['complaint_type'] ?></td>
                            <td><span class="badge badge-<?= strtolower(str_replace(' ','-',$c['status'])) ?>"><?= $c['status'] ?></span></td>
                            <td style="font-size:11px;"><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
