<?php
require_once '../../includes/config.php';
requireLogin();
$page_title = 'File Complaint';
$error = '';
$success = '';

$residents = $pdo->query("SELECT id, resident_id, CONCAT(first_name,' ',last_name) as full_name FROM residents WHERE is_active=1 ORDER BY last_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complainant_id = (int)$_POST['complainant_id'];
    $respondent = sanitize($_POST['respondent_name']);
    $respondent_addr = sanitize($_POST['respondent_address']);
    $type = sanitize($_POST['complaint_type']);
    $description = sanitize($_POST['description']);
    $incident_date = $_POST['incident_date'];
    $location = sanitize($_POST['incident_location']);

    if (!$complainant_id || !$type || !$description) {
        $error = 'Please fill in all required fields.';
    } else {
        $year = date('Y');
        $count = $pdo->query("SELECT COUNT(*) FROM complaints WHERE complaint_no LIKE 'CMP-$year-%'")->fetchColumn() + 1;
        $cmp_no = 'CMP-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $pdo->prepare("INSERT INTO complaints (complaint_no, complainant_id, respondent_name, respondent_address, complaint_type, description, incident_date, incident_location) VALUES (?,?,?,?,?,?,?,?)")
            ->execute([$cmp_no, $complainant_id, $respondent, $respondent_addr, $type, $description, $incident_date ?: null, $location]);

        $success = "Complaint <strong>$cmp_no</strong> has been filed successfully.";
    }
}
include '../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-flag"></i> File a Complaint</h1>
    <p>Submit a community complaint or incident report.</p>
</div>

<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?> <a href="index.php">View All Complaints</a></div><?php endif; ?>

<div class="card">
    <div class="card-header"><h3><i class="fas fa-file-circle-exclamation"></i> Complaint Form</h3></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user"></i> Complainant Information</div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Complainant (Resident) <span style="color:red;">*</span></label>
                        <select name="complainant_id" required>
                            <option value="">-- Select Resident --</option>
                            <?php foreach ($residents as $r): ?>
                            <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['full_name']) ?> (<?= $r['resident_id'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user-slash"></i> Respondent Information</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Respondent Name</label>
                        <input type="text" name="respondent_name" placeholder="Full name of respondent">
                    </div>
                    <div class="form-group">
                        <label>Respondent Address</label>
                        <input type="text" name="respondent_address" placeholder="Address of respondent">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-info-circle"></i> Incident Details</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Complaint Type <span style="color:red;">*</span></label>
                        <select name="complaint_type" required>
                            <option value="">Select type</option>
                            <?php foreach (['Noise Complaint','Property Dispute','Physical Assault','Verbal Abuse','Theft','Vandalism','Domestic Violence','Others'] as $t): ?>
                            <option value="<?= $t ?>"><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Incident Date</label>
                        <input type="date" name="incident_date" max="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group full-width">
                        <label>Incident Location</label>
                        <input type="text" name="incident_location" placeholder="Where did the incident happen?">
                    </div>
                    <div class="form-group full-width">
                        <label>Description / Narration <span style="color:red;">*</span></label>
                        <textarea name="description" rows="5" placeholder="Provide a detailed account of the incident..." required></textarea>
                    </div>
                </div>
            </div>

            <div class="btn-group" style="justify-content:flex-end;">
                <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Complaint</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
