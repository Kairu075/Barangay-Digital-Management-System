<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','treasurer']);
$page_title = 'New Transaction';
$error = '';
$success = '';

$residents = $pdo->query("SELECT id, resident_id, CONCAT(first_name,' ',last_name) as full_name FROM residents WHERE is_active=1 ORDER BY last_name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resident_id = (int)$_POST['resident_id'];
    $amount = (float)$_POST['amount'];
    $method = sanitize($_POST['payment_method']);
    $description = sanitize($_POST['description']);

    if (!$resident_id || !$amount || !$description) {
        $error = 'Please fill in all required fields.';
    } else {
        $year = date('Y');
        $count = $pdo->query("SELECT COUNT(*) FROM transactions WHERE or_number LIKE 'OR-$year-%'")->fetchColumn() + 1;
        $or_no = 'OR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

        $pdo->prepare("INSERT INTO transactions (or_number, resident_id, amount, payment_method, description, collected_by) VALUES (?,?,?,?,?,?)")
            ->execute([$or_no, $resident_id, $amount, $method, $description, $_SESSION['user_id']]);

        $success = "Transaction recorded. O.R. Number: <strong>$or_no</strong>";
    }
}
include '../../includes/header.php';
?>
<div class="page-header">
    <h1><i class="fas fa-plus-circle"></i> New Transaction</h1>
    <p>Record a manual payment transaction.</p>
</div>

<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?> <a href="index.php">View Transactions</a></div><?php endif; ?>

<div class="card" style="max-width:500px;">
    <div class="card-header"><h3><i class="fas fa-receipt"></i> Transaction Form</h3></div>
    <div class="card-body">
        <form method="POST">
            <div class="form-group">
                <label>Resident <span style="color:red;">*</span></label>
                <select name="resident_id" required>
                    <option value="">-- Select Resident --</option>
                    <?php foreach ($residents as $r): ?>
                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['full_name']) ?> (<?= $r['resident_id'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Description <span style="color:red;">*</span></label>
                <input type="text" name="description" placeholder="e.g., Barangay Clearance fee" required>
            </div>
            <div class="form-group">
                <label>Amount (₱) <span style="color:red;">*</span></label>
                <input type="number" name="amount" min="0" step="0.01" required>
            </div>
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method">
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="Maya">Maya</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="btn-group" style="justify-content:flex-end;">
                <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Record Transaction</button>
            </div>
        </form>
    </div>
</div>
<?php include '../../includes/footer.php'; ?>
