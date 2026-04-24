<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','treasurer']);
$page_title = 'Record Payment';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT dr.*, CONCAT(r.first_name,' ',r.last_name) as resident_name, r.resident_id as res_id FROM document_requests dr JOIN residents r ON dr.resident_id=r.id WHERE dr.id=?");
$stmt->execute([$id]);
$req = $stmt->fetch();
if (!$req) { header('Location: index.php'); exit; }

$receipt = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];
    $method = sanitize($_POST['payment_method']);

    $year = date('Y');
    $count = $pdo->query("SELECT COUNT(*) FROM transactions WHERE or_number LIKE 'OR-$year-%'")->fetchColumn() + 1;
    $or_no = 'OR-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

    $pdo->prepare("INSERT INTO transactions (or_number, document_request_id, resident_id, amount, payment_method, description, collected_by) VALUES (?,?,?,?,?,?,?)")
        ->execute([$or_no, $id, $req['resident_id'], $amount, $method, 'Payment for ' . $req['document_type'], $_SESSION['user_id']]);

    $pdo->prepare("UPDATE document_requests SET payment_status='Paid', or_number=? WHERE id=?")->execute([$or_no, $id]);

    $receipt = [
        'or_no' => $or_no,
        'resident_name' => $req['resident_name'],
        'res_id' => $req['res_id'],
        'doc_type' => $req['document_type'],
        'amount' => $amount,
        'method' => $method,
        'date' => date('F d, Y h:i A'),
        'req_no' => $req['request_no'],
        'cashier' => $_SESSION['full_name'],
    ];
}

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-receipt"></i> Record Payment</h1>
        <p><?= $req['request_no'] ?> — <?= $req['document_type'] ?></p>
    </div>
    <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<?php if ($receipt): ?>
<!-- RECEIPT DISPLAY -->
<div class="card mb-3 no-print">
    <div class="card-header" style="background:var(--success);color:white;">
        <h3 style="color:white;"><i class="fas fa-check-circle"></i> Payment Recorded Successfully!</h3>
        <button onclick="window.print()" class="btn btn-sm" style="background:white;color:var(--success);"><i class="fas fa-print"></i> Print Receipt</button>
    </div>
</div>

<div id="receiptContent" style="max-width:420px;margin:0 auto;">
    <div class="or-receipt" style="border:2px solid #333;padding:24px;font-family:'Courier New',monospace;font-size:13px;background:white;border-radius:8px;box-shadow:var(--shadow);">
        <div style="text-align:center;border-bottom:2px dashed #333;padding-bottom:14px;margin-bottom:14px;">
            <div style="font-size:9px;letter-spacing:2px;">REPUBLIC OF THE PHILIPPINES</div>
            <div style="font-weight:bold;font-size:15px;margin:4px 0;">BARANGAY Nangka</div>
            <div style="font-size:10px;color:#555;">City of Marikina, Metro Manila</div>
            <div style="font-size:18px;font-weight:bold;margin:8px 0;letter-spacing:1px;">OFFICIAL RECEIPT</div>
            <div style="font-size:11px;font-weight:bold;">OR No.: <?= $receipt['or_no'] ?></div>
        </div>

        <table style="width:100%;font-size:12px;">
            <tr><td style="color:#555;padding:3px 0;">Date:</td><td style="font-weight:bold;"><?= $receipt['date'] ?></td></tr>
            <tr><td style="color:#555;padding:3px 0;">Received from:</td><td style="font-weight:bold;"><?= $receipt['resident_name'] ?></td></tr>
            <tr><td style="color:#555;padding:3px 0;">Resident ID:</td><td><?= $receipt['res_id'] ?></td></tr>
            <tr><td style="color:#555;padding:3px 0;">Request No.:</td><td><?= $receipt['req_no'] ?></td></tr>
            <tr><td style="color:#555;padding:3px 0;">For:</td><td style="font-weight:bold;"><?= $receipt['doc_type'] ?></td></tr>
            <tr><td style="color:#555;padding:3px 0;">Payment Method:</td><td><?= $receipt['method'] ?></td></tr>
        </table>

        <div style="border-top:2px dashed #333;border-bottom:2px dashed #333;padding:12px 0;margin:14px 0;text-align:center;">
            <div style="font-size:11px;color:#555;">AMOUNT PAID</div>
            <div style="font-size:26px;font-weight:bold;">₱<?= number_format($receipt['amount'], 2) ?></div>
            <div style="font-size:11px;color:#555;"><?= strtoupper(numberToWords($receipt['amount'])) ?> PESOS</div>
        </div>

        <div style="display:flex;justify-content:space-between;margin-top:30px;font-size:11px;">
            <div style="text-align:center;">
                <div style="border-top:1px solid #333;padding-top:4px;width:120px;"><?= $receipt['cashier'] ?></div>
                <div style="color:#555;">Cashier / Treasurer</div>
            </div>
            <div style="text-align:center;">
                <div style="border-top:1px solid #333;padding-top:4px;width:120px;">__________________</div>
                <div style="color:#555;">Received by</div>
            </div>
        </div>

        <div style="text-align:center;margin-top:14px;font-size:9px;color:#888;border-top:1px dashed #ccc;padding-top:10px;">
            Thank you for transacting with Barangay Nangka.<br>
            This is your official receipt. Please keep it for your records.
        </div>
    </div>
</div>

<div style="text-align:center;margin-top:16px;">
    <a href="generate.php?id=<?= $id ?>" class="btn btn-primary no-print"><i class="fas fa-file-certificate"></i> View/Print Document</a>
    <a href="index.php" class="btn btn-outline no-print"><i class="fas fa-list"></i> Back to List</a>
</div>

<?php else: ?>
<div class="card" style="max-width:500px;">
    <div class="card-header">
        <h3><i class="fas fa-peso-sign"></i> Payment Details</h3>
    </div>
    <div class="card-body">
        <div style="background:var(--blue-faint);border-radius:8px;padding:14px;margin-bottom:20px;font-size:13.5px;">
            <div><strong>Resident:</strong> <?= htmlspecialchars($req['resident_name']) ?> (<?= $req['res_id'] ?>)</div>
            <div><strong>Document:</strong> <?= $req['document_type'] ?></div>
            <div><strong>Request No.:</strong> <?= $req['request_no'] ?></div>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Amount (₱)</label>
                <input type="number" name="amount" value="<?= $req['amount'] ?>" step="0.01" min="0" required>
            </div>
            <div class="form-group">
                <label>Payment Method</label>
                <select name="payment_method" required>
                    <option value="Cash">Cash</option>
                    <option value="GCash">GCash</option>
                    <option value="Maya">Maya</option>
                    <option value="Bank Transfer">Bank Transfer</option>
                </select>
            </div>
            <div class="btn-group" style="justify-content:flex-end;">
                <a href="index.php" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-success"><i class="fas fa-check"></i> Record Payment & Issue OR</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php
function numberToWords($num) {
    $ones = ['','ONE','TWO','THREE','FOUR','FIVE','SIX','SEVEN','EIGHT','NINE','TEN','ELEVEN','TWELVE','THIRTEEN','FOURTEEN','FIFTEEN','SIXTEEN','SEVENTEEN','EIGHTEEN','NINETEEN'];
    $tens = ['','','TWENTY','THIRTY','FORTY','FIFTY','SIXTY','SEVENTY','EIGHTY','NINETY'];
    if ($num < 20) return $ones[$num];
    if ($num < 100) return $tens[intval($num/10)] . ($num%10 ? ' '.$ones[$num%10] : '');
    if ($num < 1000) return $ones[intval($num/100)] . ' HUNDRED' . ($num%100 ? ' '.numberToWords($num%100) : '');
    return (int)$num . '';
}
?>

<?php include '../../includes/footer.php'; ?>
