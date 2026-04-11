<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','secretary']);
$page_title = 'Add Resident';

$error = '';
$success = '';

// Get households for dropdown
$households = $pdo->query("SELECT * FROM households ORDER BY household_no")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'household_id' => $_POST['household_id'] ?: null,
        'last_name' => sanitize($_POST['last_name']),
        'first_name' => sanitize($_POST['first_name']),
        'middle_name' => sanitize($_POST['middle_name']),
        'suffix' => sanitize($_POST['suffix']),
        'birthdate' => $_POST['birthdate'],
        'birthplace' => sanitize($_POST['birthplace']),
        'gender' => $_POST['gender'],
        'civil_status' => $_POST['civil_status'],
        'nationality' => sanitize($_POST['nationality']) ?: 'Filipino',
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

    if (!$data['last_name'] || !$data['first_name'] || !$data['birthdate'] || !$data['gender']) {
        $error = 'Please fill in all required fields.';
    } else {
        // Generate resident ID
        $year = date('Y');
        $count = $pdo->query("SELECT COUNT(*) FROM residents WHERE resident_id LIKE 'RES-$year-%'")->fetchColumn() + 1;
        $resident_id = 'RES-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO residents (resident_id, household_id, last_name, first_name, middle_name, suffix, birthdate, birthplace, gender, civil_status, nationality, religion, occupation, monthly_income, educational_attainment, voter_status, senior_citizen, pwd, solo_parent, email, contact_no) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $resident_id, $data['household_id'], $data['last_name'], $data['first_name'], $data['middle_name'],
            $data['suffix'], $data['birthdate'], $data['birthplace'], $data['gender'], $data['civil_status'],
            $data['nationality'], $data['religion'], $data['occupation'], $data['monthly_income'],
            $data['educational_attainment'], $data['voter_status'], $data['senior_citizen'],
            $data['pwd'], $data['solo_parent'], $data['email'], $data['contact_no']
        ]);

        $success = "Resident successfully registered with ID: <strong>$resident_id</strong>";
    }
}

include '../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-user-plus"></i> Add New Resident</h1>
    <p>Register a new resident to the barangay database.</p>
</div>

<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?> <a href="index.php">Back to list</a></div><?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-id-card"></i> Resident Information Form</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <!-- Personal Info -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user"></i> Personal Information</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Last Name <span style="color:red;">*</span></label>
                        <input type="text" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>First Name <span style="color:red;">*</span></label>
                        <input type="text" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Middle Name</label>
                        <input type="text" name="middle_name" value="<?= htmlspecialchars($_POST['middle_name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Suffix (Jr., Sr., etc.)</label>
                        <input type="text" name="suffix" value="<?= htmlspecialchars($_POST['suffix'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Date of Birth <span style="color:red;">*</span></label>
                        <input type="date" name="birthdate" value="<?= $_POST['birthdate'] ?? '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Birthplace</label>
                        <input type="text" name="birthplace" value="<?= htmlspecialchars($_POST['birthplace'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Gender <span style="color:red;">*</span></label>
                        <select name="gender" required>
                            <option value="">Select gender</option>
                            <option value="Male" <?= ($_POST['gender']??'')==='Male'?'selected':'' ?>>Male</option>
                            <option value="Female" <?= ($_POST['gender']??'')==='Female'?'selected':'' ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Civil Status</label>
                        <select name="civil_status">
                            <?php foreach (['Single','Married','Widowed','Separated','Annulled'] as $s): ?>
                            <option value="<?= $s ?>" <?= ($_POST['civil_status']??'Single')===$s?'selected':'' ?>><?= $s ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Nationality</label>
                        <input type="text" name="nationality" value="<?= htmlspecialchars($_POST['nationality'] ?? 'Filipino') ?>">
                    </div>
                    <div class="form-group">
                        <label>Religion</label>
                        <input type="text" name="religion" value="<?= htmlspecialchars($_POST['religion'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-home"></i> Household / Address</div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Household</label>
                        <select name="household_id">
                            <option value="">-- Select Household --</option>
                            <?php foreach ($households as $hh): ?>
                            <option value="<?= $hh['id'] ?>" <?= ($_POST['household_id']??'')==$hh['id']?'selected':'' ?>>
                                <?= $hh['household_no'] ?> — <?= htmlspecialchars($hh['address']) ?> (<?= $hh['purok'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Employment -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-briefcase"></i> Employment & Education</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Occupation</label>
                        <input type="text" name="occupation" value="<?= htmlspecialchars($_POST['occupation'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Monthly Income (₱)</label>
                        <input type="number" name="monthly_income" value="<?= $_POST['monthly_income'] ?? 0 ?>" min="0" step="0.01">
                    </div>
                    <div class="form-group full-width">
                        <label>Educational Attainment</label>
                        <select name="educational_attainment">
                            <option value="">Select</option>
                            <?php foreach (['No Formal Education','Elementary Level','Elementary Graduate','High School Level','High School Graduate','Senior High Level','Senior High Graduate','Vocational','College Level','College Graduate','Post Graduate'] as $e): ?>
                            <option value="<?= $e ?>" <?= ($_POST['educational_attainment']??'')===$e?'selected':'' ?>><?= $e ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Contact -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-phone"></i> Contact Information</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="tel" name="contact_no" value="<?= htmlspecialchars($_POST['contact_no'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                </div>
            </div>

            <!-- Special Classifications -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-star"></i> Special Classifications</div>
                <div style="display:flex;gap:28px;flex-wrap:wrap;">
                    <?php
                    $checks = [
                        'voter_status' => 'Registered Voter',
                        'pwd' => 'Person with Disability (PWD)',
                        'solo_parent' => 'Solo Parent'
                    ];
                    foreach ($checks as $name => $label):
                    ?>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;text-transform:none;letter-spacing:0;font-size:14px;font-weight:500;">
                        <input type="checkbox" name="<?= $name ?>" style="width:auto;cursor:pointer;" <?= isset($_POST[$name])?'checked':'' ?>>
                        <?= $label ?>
                    </label>
                    <?php endforeach; ?>
                    <p style="font-size:12px;color:var(--gray-500);margin-top:4px;width:100%;">Note: Senior Citizen status is automatically determined based on age (60+).</p>
                </div>
            </div>

            <div class="btn-group" style="justify-content:flex-end;">
                <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Resident</button>
            </div>
        </form>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
