<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','treasurer','captain']);
$page_title = 'Financial Report';

$month = (int)($_GET['month'] ?? date('m'));
$year  = (int)($_GET['year']  ?? date('Y'));

$stmt = $pdo->prepare("SELECT t.*, CONCAT(r.first_name,' ',r.last_name) as resident_name FROM transactions t JOIN residents r ON t.resident_id=r.id WHERE MONTH(t.transaction_date)=? AND YEAR(t.transaction_date)=? ORDER BY t.transaction_date");
$stmt->execute([$month, $year]);
$transactions = $stmt->fetchAll();

$totalAmount = array_sum(array_column($transactions, 'amount'));

// Breakdown by type
$byMethod = [];
foreach ($transactions as $t) {
    $byMethod[$t['payment_method']] = ($byMethod[$t['payment_method']] ?? 0) + $t['amount'];
}

$monthName = date('F Y', mktime(0,0,0,$month,1,$year));

include '../../includes/header.php';
?>
<style>
@media print {
    .no-print { display: none !important; }
    .main-wrapper { margin-left: 0 !important; }
    .topbar { display: none !important; }
    .sidebar { display: none !important; }
    .page-content { padding: 0 !important; }
    body { background: white !important; }
}
</style>

<div class="page-header flex-between no-print">
    <div>
        <h1><i class="fas fa-chart-bar"></i> Financial Report</h1>
        <p>Monthly income summary — <?= $monthName ?></p>
    </div>
    <div class="btn-group">
        <a href="index.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back</a>
        <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Report</button>
    </div>
</div>

<!-- FILTER (no print) -->
<div class="card mb-3 no-print">
    <div class="card-body" style="padding:16px;">
        <form method="GET" class="filter-bar">
            <select name="month" style="width:160px;">
                <?php for ($m=1;$m<=12;$m++): ?>
                <option value="<?= $m ?>" <?= $month==$m?'selected':'' ?>><?= date('F', mktime(0,0,0,$m,1)) ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" style="width:120px;">
                <?php for ($y=date('Y');$y>=date('Y')-3;$y--): ?>
                <option value="<?= $y ?>" <?= $year==$y?'selected':'' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Generate</button>
        </form>
    </div>
</div>

<!-- PRINTABLE REPORT -->
<div id="reportContent" style="max-width:900px;margin:0 auto;">
    <!-- Report Header -->
    <div style="text-align:center;border-bottom:3px double #333;padding-bottom:16px;margin-bottom:20px;font-family:'Times New Roman',serif;">
        <div style="font-size:11px;letter-spacing:2px;">REPUBLIC OF THE PHILIPPINES</div>
        <div style="font-weight:bold;font-size:20px;margin:4px 0;">BARANGAY SAN MARINO</div>
        <div style="font-size:12px;color:#555;">City of Manila, Metro Manila</div>
        <div style="font-size:14px;font-weight:bold;margin-top:12px;letter-spacing:2px;border:2px solid #333;display:inline-block;padding:5px 24px;">
            MONTHLY FINANCIAL REPORT
        </div>
        <div style="margin-top:8px;font-size:13px;"><strong>For the Month of: <?= $monthName ?></strong></div>
        <div style="font-size:11px;color:#555;">Generated: <?= date('F d, Y h:i A') ?></div>
    </div>

    <!-- Summary Boxes -->
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
        <div style="border:2px solid var(--blue);border-radius:8px;padding:16px;text-align:center;background:var(--blue-faint);">
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--blue);font-weight:700;">Total Collection</div>
            <div style="font-size:28px;font-weight:800;color:var(--blue);">₱<?= number_format($totalAmount, 2) ?></div>
        </div>
        <div style="border:2px solid var(--success);border-radius:8px;padding:16px;text-align:center;background:#f0fff4;">
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--success);font-weight:700;">No. of Transactions</div>
            <div style="font-size:28px;font-weight:800;color:var(--success);"><?= count($transactions) ?></div>
        </div>
        <div style="border:2px solid var(--gold-dark);border-radius:8px;padding:16px;text-align:center;background:#fffdf0;">
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:1px;color:var(--gold-dark);font-weight:700;">Average per Transaction</div>
            <div style="font-size:28px;font-weight:800;color:var(--gold-dark);">₱<?= count($transactions) ? number_format($totalAmount/count($transactions), 2) : '0.00' ?></div>
        </div>
    </div>

    <!-- Breakdown by Payment Method -->
    <?php if ($byMethod): ?>
    <div style="margin-bottom:20px;">
        <div style="font-size:13px;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:1px;border-bottom:2px solid var(--blue-faint);padding-bottom:6px;margin-bottom:12px;">
            Breakdown by Payment Method
        </div>
        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <?php foreach ($byMethod as $method => $amount): ?>
            <div style="background:var(--gray-100);border-radius:8px;padding:10px 18px;font-size:13.5px;">
                <strong><?= $method ?></strong>: ₱<?= number_format($amount, 2) ?>
                <span style="color:var(--gray-500);font-size:11px;"> (<?= count($transactions) ? round($amount/$totalAmount*100) : 0 ?>%)</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Transactions Table -->
    <div style="font-size:13px;font-weight:700;color:var(--blue);text-transform:uppercase;letter-spacing:1px;border-bottom:2px solid var(--blue-faint);padding-bottom:6px;margin-bottom:12px;">
        Transaction Details
    </div>
    <table style="width:100%;border-collapse:collapse;font-size:12.5px;">
        <thead>
            <tr style="background:#2d4d80;color:white;">
                <th style="padding:9px 12px;text-align:left;">#</th>
                <th style="padding:9px 12px;text-align:left;">O.R. Number</th>
                <th style="padding:9px 12px;text-align:left;">Resident</th>
                <th style="padding:9px 12px;text-align:left;">Description</th>
                <th style="padding:9px 12px;text-align:left;">Method</th>
                <th style="padding:9px 12px;text-align:right;">Amount</th>
                <th style="padding:9px 12px;text-align:left;">Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $i => $t): ?>
            <tr style="<?= $i%2 ? 'background:#f9f9f9' : '' ?>">
                <td style="padding:8px 12px;border-bottom:1px solid #eee;"><?= $i+1 ?></td>
                <td style="padding:8px 12px;border-bottom:1px solid #eee;font-weight:600;"><?= $t['or_number'] ?></td>
                <td style="padding:8px 12px;border-bottom:1px solid #eee;"><?= htmlspecialchars($t['resident_name']) ?></td>
                <td style="padding:8px 12px;border-bottom:1px solid #eee;font-size:12px;"><?= htmlspecialchars($t['description']) ?></td>
                <td style="padding:8px 12px;border-bottom:1px solid #eee;"><?= $t['payment_method'] ?></td>
                <td style="padding:8px 12px;border-bottom:1px solid #eee;text-align:right;font-weight:700;">₱<?= number_format($t['amount'], 2) ?></td>
                <td style="padding:8px 12px;border-bottom:1px solid #eee;font-size:11px;"><?= date('M d, Y', strtotime($t['transaction_date'])) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($transactions)): ?>
            <tr><td colspan="7" style="padding:20px;text-align:center;color:#888;">No transactions found for this period.</td></tr>
            <?php endif; ?>
        </tbody>
        <?php if (!empty($transactions)): ?>
        <tfoot>
            <tr style="background:#2d4d80;color:white;">
                <td colspan="5" style="padding:12px;font-weight:700;text-align:right;">TOTAL COLLECTION FOR <?= strtoupper($monthName) ?>:</td>
                <td style="padding:12px;text-align:right;font-weight:800;font-size:16px;color:#FBC531;">₱<?= number_format($totalAmount, 2) ?></td>
                <td></td>
            </tr>
        </tfoot>
        <?php endif; ?>
    </table>

    <!-- Signature Area -->
    <div style="margin-top:50px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:20px;text-align:center;font-family:'Times New Roman',serif;font-size:13px;">
        <div>
            <div style="border-top:1.5px solid #333;padding-top:6px;margin-top:50px;">
                <strong>Juan Garcia</strong><br>
                <span style="font-size:11.5px;color:#555;">Barangay Treasurer</span>
            </div>
        </div>
        <div>
            <div style="border-top:1.5px solid #333;padding-top:6px;margin-top:50px;">
                <strong>Maria Santos</strong><br>
                <span style="font-size:11.5px;color:#555;">Barangay Secretary</span>
            </div>
        </div>
        <div>
            <div style="border-top:1.5px solid #333;padding-top:6px;margin-top:50px;">
                <strong>Hon. Roberto Reyes</strong><br>
                <span style="font-size:11.5px;color:#555;">Barangay Captain</span>
            </div>
        </div>
    </div>

    <div style="text-align:center;margin-top:20px;font-size:10.5px;color:#888;border-top:1px solid #ddd;padding-top:10px;font-family:'Times New Roman',serif;">
        This is an official financial report of Barangay San Marino, City of Manila. Generated via the Barangay San Marino Digital Management System.
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
