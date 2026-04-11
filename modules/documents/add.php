<?php
require_once '../../includes/config.php';
requireLogin();
$page_title = 'New Document Request';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident_id = (int)$_POST['resident_id'];
    $doc_type = sanitize($_POST['document_type']);
    $purpose = sanitize($_POST['purpose']);

    if (!$resident_id || !$doc_type || !$purpose) {
        $error = 'Please fill in all required fields.';
    } else {
        // Get fee
        $fee_stmt = $pdo->prepare("SELECT amount FROM document_fees WHERE document_type=?");
        $fee_stmt->execute([$doc_type]);
        $fee = $fee_stmt->fetchColumn() ?: 0;

        $year = date('Y');
        $count = $pdo->query("SELECT COUNT(*) FROM document_requests WHERE request_no LIKE 'REQ-$year-%'")->fetchColumn() + 1;
        $req_no = 'REQ-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("INSERT INTO document_requests (request_no, resident_id, document_type, purpose, amount, requested_by) VALUES (?,?,?,?,?,?)");
        $stmt->execute([$req_no, $resident_id, $doc_type, $purpose, $fee, $_SESSION['user_id']]);

        $newId = $pdo->lastInsertId();
        $success = "Request <strong>$req_no</strong> has been submitted successfully.";
    }
}

include '../../includes/header.php';
?>

<div class="page-header">
    <h1><i class="fas fa-file-plus"></i> New Document Request</h1>
    <p>Submit a new document request for a resident.</p>
</div>

<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?>
<div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?> 
    <a href="index.php">View All Requests</a> | <a href="add.php">New Request</a>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-file-circle-plus"></i> Document Request Form</h3>
    </div>
    <div class="card-body">
        <form method="POST">
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-user-search"></i> Find Resident</div>
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label>Search Resident <span style="color:red;">*</span></label>
                        <input type="text" id="resident_search" placeholder="Type name or resident ID to search..." autocomplete="off">
                        <input type="hidden" name="resident_id" id="resident_id">
                        <div id="resident_results" style="border:1px solid #dee2e6;border-top:none;border-radius:0 0 8px 8px;max-height:200px;overflow-y:auto;display:none;position:relative;z-index:10;background:white;"></div>
                        <p style="font-size:11.5px;color:var(--gray-500);margin-top:4px;">Type at least 2 characters to search</p>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-file-alt"></i> Document Details</div>
                <div class="form-grid">
                    <div class="form-group">
                        <label>Document Type <span style="color:red;">*</span></label>
                        <select name="document_type" id="document_type" required>
                            <option value="">Select document type</option>
                            <?php
                            $fees = $pdo->query("SELECT * FROM document_fees")->fetchAll();
                            foreach ($fees as $f):
                            ?>
                            <option value="<?= $f['document_type'] ?>"><?= $f['document_type'] ?> — ₱<?= number_format($f['amount'], 2) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Processing Fee</label>
                        <div style="padding:10px 14px;background:var(--blue-faint);border-radius:8px;border:1.5px solid var(--blue);font-weight:700;color:var(--blue);font-size:15px;" id="fee_display">₱0.00</div>
                        <input type="hidden" name="amount" id="amount" value="0">
                    </div>
                    <div class="form-group full-width">
                        <label>Purpose <span style="color:red;">*</span></label>
                        <input type="text" name="purpose" placeholder="e.g., Employment, Bank Account Opening, Scholarship..." required>
                    </div>
                </div>
            </div>

            <div style="background:var(--blue-faint);border-radius:8px;padding:14px;margin-bottom:20px;font-size:13px;color:var(--blue-dark);">
                <strong><i class="fas fa-info-circle"></i> Processing Workflow:</strong>
                Pending → Processing → For Approval → Approved → Released
                <br>Standard processing time: 5–10 minutes upon approval.
            </div>

            <div class="btn-group" style="justify-content:flex-end;">
                <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
var SITE_URL = '<?= SITE_URL ?>';

// Inline resident search
const searchInput = document.getElementById('resident_search');
const resultsDiv = document.getElementById('resident_results');
let timer;

searchInput.addEventListener('input', function() {
    clearTimeout(timer);
    const q = this.value.trim();
    if (q.length < 2) { resultsDiv.style.display = 'none'; return; }
    timer = setTimeout(function() {
        fetch(SITE_URL + '/api/search_residents.php?q=' + encodeURIComponent(q))
            .then(r => r.json())
            .then(data => {
                resultsDiv.innerHTML = '';
                if (!data.length) {
                    resultsDiv.innerHTML = '<div style="padding:12px;color:#888;font-size:13px;">No residents found</div>';
                } else {
                    data.forEach(function(res) {
                        const d = document.createElement('div');
                        d.style.cssText = 'padding:10px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:13.5px;';
                        d.innerHTML = '<strong>' + res.full_name + '</strong> <span style="color:#888;font-size:12px;">(' + res.resident_id + ')</span><br><span style="font-size:12px;color:#666;">' + (res.address||'') + '</span>';
                        d.addEventListener('mouseenter', function(){this.style.background='#e8eef7';});
                        d.addEventListener('mouseleave', function(){this.style.background='';});
                        d.addEventListener('click', function() {
                            searchInput.value = res.full_name + ' (' + res.resident_id + ')';
                            document.getElementById('resident_id').value = res.id;
                            resultsDiv.style.display = 'none';
                        });
                        resultsDiv.appendChild(d);
                    });
                }
                resultsDiv.style.display = 'block';
            });
    }, 300);
});

document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target)) resultsDiv.style.display = 'none';
});
</script>

<?php include '../../includes/footer.php'; ?>
