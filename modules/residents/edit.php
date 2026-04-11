<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','secretary']);
$page_title = 'Edit Resident';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM residents WHERE id=?");
$stmt->execute([$id]);
$res = $stmt->fetch();
if (!$res) { header('Location: index.php'); exit; }

$households = $pdo->query("SELECT * FROM households ORDER BY household_no")->fetchAll();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'household_id' => $_POST['household_id'] ?: null,
        'last_name' => sanitize($_POST['last_name']),
        'first_name' => sanitize($_POST['first_name']),
        'middle_name' => sanitize($_POST['middle_name']),
        'suffix' => sanitize($_POST['suffix']),
        'birthdate' => $_POST['birthdate'],
        'birthplace' => sanitize($_POST['birthplace']),
        'gender' => $_POST['gender'],
        'civil_status' => $_POST['civil_status'],
        'nationality' => sanitize($_POST['nationality']),
        'religion' => sanitize($_POST['religion']),
        'occupation' => sanitize($_POST['occupation']),
        'monthly_income' => (float)$_POST['monthly_income'],
        'educational_attainment' => sanitize($_POST['educational_attainment']),
        'voter_status' => isset($_POST['voter_status']) ? 1 : 0,
        'senior_citizen' => getAge($_POST['birthdate']) >= 60 ? 1 : 0,
        'pwd' => isset($_POST['pwd']) ? 1 : 0,
        'solo_parent' => isset($_POST['solo_parent']) ? 1 : 0,
        'email' => sanitize($_POST['email']),
        'contact_no' => sanitize($_POST['contact_no']),
    ];

    $sql = "UPDATE residents SET household_id=?, last_name=?, first_name=?, middle_name=?, suffix=?, birthdate=?, birthplace=?, gender=?, civil_status=?, nationality=?, religion=?, occupation=?, monthly_income=?, educational_attainment=?, voter_status=?, senior_citizen=?, pwd=?, solo_parent=?, email=?, contact_no=? WHERE id=?";
    $pdo->prepare($sql)->execute(array_merge(array_values($fields), [$id]));
    $success = 'Resident information updated successfully.';

    // Reload updated data
    $stmt = $pdo->prepare("SELECT * FROM residents WHERE id=?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
}

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-user-edit"></i> Edit Resident</h1>
        <p><?= htmlspecialchars($res['first_name'].' '.$res['last_name']) ?> — <?= $res['resident_id'] ?></p>
    </div>
    <div class="btn-group">
        <a href="view.php?id=<?= $id ?>" class="btn btn-outline"><i class="fas fa-eye"></i> View Profile</a>
        <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
    </div>
</div>

<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

<div class="card">
    <div class="card-header"><h3><i class="fas fa-id-card"></i> Edit Resident Information</h3></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user"></i> Personal Information</div>
                <div class="form-grid">
                    <div class="form-group"><label>Last Name *</label><input type="text" name="last_name" value="<?= htmlspecialchars($res['last_name']) ?>" required></div>
                    <div class="form-group"><label>First Name *</label><input type="text" name="first_name" value="<?= htmlspecialchars($res['first_name']) ?>" required></div>
                    <div class="form-group"><label>Middle Name</label><input type="text" name="middle_name" value="<?= htmlspecialchars($res['middle_name']) ?>"></div>
                    <div class="form-group"><label>Suffix</label><input type="text" name="suffix" value="<?= htmlspecialchars($res['suffix']) ?>"></div>
                    <div class="form-group"><label>Date of Birth *</label><input type="date" name="birthdate" value="<?= $res['birthdate'] ?>" required></div>
                    <div class="form-group"><label>Birthplace</label><input type="text" name="birthplace" value="<?= htmlspecialchars($res['birthplace']) ?>"></div>
                    <div class="form-group"><label>Gender *</label>
                        <select name="gender" required>
                            <option value="Male" <?= $res['gender']==='Male'?'selected':'' ?>>Male</option>
                            <option value="Female" <?= $res['gender']==='Female'?'selected':'' ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Civil Status</label>
                        <select name="civil_status">
                            <?php foreach (['Single','Married','Widowed','Separated','Annulled'] as $s): ?>
                            <option value="<?= $s ?>" <?= $res['civil_status']===$s?'selected':'' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>Nationality</label><input type="text" name="nationality" value="<?= htmlspecialchars($res['nationality']) ?>"></div>
                    <div class="form-group"><label>Religion</label><input type="text" name="religion" value="<?= htmlspecialchars($res['religion']) ?>"></div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-home"></i> Household</div>
                <div class="form-group">
                    <label>Household</label>
                    <select name="household_id">
                        <option value="">-- None --</option>
                        <?php foreach ($households as $hh): ?>
                        <option value="<?= $hh['id'] ?>" <?= $res['household_id']==$hh['id']?'selected':'' ?>>
                            <?= $hh['household_no'] ?> — <?= htmlspecialchars($hh['address']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-briefcase"></i> Employment</div>
                <div class="form-grid">
                    <div class="form-group"><label>Occupation</label><input type="text" name="occupation" value="<?= htmlspecialchars($res['occupation']) ?>"></div>
                    <div class="form-group"><label>Monthly Income (₱)</label><input type="number" name="monthly_income" value="<?= $res['monthly_income'] ?>" min="0" step="0.01"></div>
                    <div class="form-group full-width"><label>Educational Attainment</label>
                        <select name="educational_attainment">
                            <option value="">Select</option>
                            <?php foreach (['No Formal Education','Elementary Level','Elementary Graduate','High School Level','High School Graduate','Senior High Level','Senior High Graduate','Vocational','College Level','College Graduate','Post Graduate'] as $e): ?>
                            <option value="<?= $e ?>" <?= $res['educational_attainment']===$e?'selected':'' ?>><?= $e ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-phone"></i> Contact</div>
                <div class="form-grid">
                    <div class="form-group"><label>Contact Number</label><input type="tel" name="contact_no" value="<?= htmlspecialchars($res['contact_no']) ?>"></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" value="<?= htmlspecialchars($res['email']) ?>"></div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-star"></i> Classifications</div>
                <div style="display:flex;gap:28px;flex-wrap:wrap;">
                    <?php foreach (['voter_status'=>'Registered Voter','pwd'=>'PWD','solo_parent'=>'Solo Parent'] as $n=>$l): ?>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none;letter-spacing:0;font-size:14px;font-weight:500;">
                        <input type="checkbox" name="<?= $n ?>" style="width:auto;" <?= $res[$n]?'checked':'' ?>>
                        <?= $l ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="btn-group" style="justify-content:flex-end;">
                <a href="view.php?id=<?= $id ?>" class="btn btn-outline"><i class="fas fa-times"></i> Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
