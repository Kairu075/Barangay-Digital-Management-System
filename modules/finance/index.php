<?php
require_once '../../includes/config.php';
requireLogin();
requireRole(['admin','treasurer','captain']);
$page_title = 'Financial Records';

$month = (int)($_GET['month'] ?? date('m'));
$year = (int)($_GET['year'] ?? date('Y'));

$transactionsStmt = $pdo->prepare("
    SELECT t.*, 
           CONCAT(r.first_name,' ',r.last_name) as resident_name, 
           u.full_name as cashier_name 
    FROM transactions t 
    JOIN residents r ON t.resident_id=r.id 
    LEFT JOIN users u ON t.collected_by=u.id 
    WHERE MONTH(t.transaction_date)=? 
      AND YEAR(t.transaction_date)=? 
    ORDER BY t.transaction_date DESC
");
$transactionsStmt->execute([$month, $year]);
$transactions = $transactionsStmt->fetchAll();

$totalMonth = array_sum(array_column($transactions, 'amount'));

// YEARLY TOTAL (fixed)
$yearStmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE YEAR(transaction_date)=?");
$yearStmt->execute([date('Y')]);
$yearTotal = $yearStmt->fetchColumn();

// TODAY TOTAL
$todayTotal = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE DATE(transaction_date)=CURDATE()")->fetchColumn();

// Monthly chart data
$chartData = [];
for ($i = 5; $i >= 0; $i--) {
    $m = date('m', strtotime("-$i months"));
    $y = date('Y', strtotime("-$i months"));

    $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM transactions WHERE MONTH(transaction_date)=? AND YEAR(transaction_date)=?");
    $stmt->execute([$m, $y]);

    $chartData[] = [
        'label' => date('M Y', strtotime("-$i months")),
        'amount' => (float)$stmt->fetchColumn()
    ];
}

include '../../includes/header.php';
?>

<div class="page-header flex-between">
    <div>
        <h1><i class="fas fa-peso-sign"></i> Financial Records</h1>
        <p>Track all financial transactions and generate income reports.</p>
    </div>
    <div class="btn-group">
        <a href="report.php" class="btn btn-primary"><i class="fas fa-chart-bar"></i> Generate Report</a>
        <a href="add_transaction.php" class="btn btn-gold"><i class="fas fa-plus"></i> New Transaction</a>
    </div>
</div>

<!-- SUMMARY -->
<div class="stats-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-value">₱<?= number_format($totalMonth, 2) ?></div>
            <div class="stat-label">This Month's Collection</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-value"><?= count($transactions) ?></div>
            <div class="stat-label">Transactions</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-value">₱<?= number_format($todayTotal, 2) ?></div>
            <div class="stat-label">Today</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <div class="stat-value">₱<?= number_format($yearTotal, 2) ?></div>
            <div class="stat-label">This Year</div>
        </div>
    </div>
</div>

<!-- CHART + FILTER -->
<div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;margin-bottom:24px;">
    <div class="card">
        <div class="card-header"><h3>Monthly Collection</h3></div>
        <div class="card-body">
            <div style="height:200px;display:flex;align-items:flex-end;gap:12px;">
                <?php
                $maxVal = max(array_column($chartData, 'amount')) ?: 1;
                foreach ($chartData as $cd):
                    $pct = ($cd['amount'] / $maxVal) * 100;
                ?>
                <div style="flex:1;text-align:center;">
                    <div>₱<?= number_format($cd['amount'],0) ?></div>
                    <div style="height:<?= max($pct, 2) ?>%;background:blue;"></div>
                    <div><?= $cd['label'] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>Filter</h3></div>
        <div class="card-body">
            <form method="GET">
                <select name="month">
                    <?php for ($m=1;$m<=12;$m++): ?>
                    <option value="<?= $m ?>" <?= $month==$m?'selected':'' ?>>
                        <?= date('F', mktime(0,0,0,$m,1)) ?>
                    </option>
                    <?php endfor; ?>
                </select>

                <select name="year">
                    <?php for ($y=date('Y');$y>=date('Y')-3;$y--): ?>
                    <option value="<?= $y ?>" <?= $year==$y?'selected':'' ?>>
                        <?= $y ?>
                    </option>
                    <?php endfor; ?>
                </select>

                <button type="submit" class="btn btn-primary">View</button>
            </form>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="card">
    <div class="card-header">
        <h3>Transactions — <?= date('F Y', mktime(0,0,0,$month,1,$year)) ?></h3>
        <span>Total: ₱<?= number_format($totalMonth, 2) ?></span>
    </div>

    <div class="card-body">
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>OR</th>
                        <th>Resident</th>
                        <th>Description</th>
                        <th>Payment</th>
                        <th>Amount</th>
                        <th>Cashier</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $t): ?>
                    <tr>
                        <td><?= $t['or_number'] ?></td>
                        <td><?= htmlspecialchars($t['resident_name']) ?></td>
                        <td><?= htmlspecialchars($t['description']) ?></td>
                        <td><?= $t['payment_method'] ?></td>
                        <td>₱<?= number_format($t['amount'],2) ?></td>
                        <td><?= htmlspecialchars($t['cashier_name'] ?? 'N/A') ?></td>
                        <td><?= date('M d, Y h:i A', strtotime($t['transaction_date'])) ?></td>
                        <td>
                            <button onclick="openModal('receiptModal<?= $t['id'] ?>')" class="btn btn-sm btn-primary">
                                View
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="8">No data</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ✅ MODALS MOVED OUTSIDE TABLE -->
<?php foreach ($transactions as $t): ?>
<div class="modal-backdrop" id="receiptModal<?= $t['id'] ?>">
    <div class="modal">
        <h3>Receipt</h3>
        <p><strong>OR:</strong> <?= $t['or_number'] ?></p>
        <p><strong>Name:</strong> <?= htmlspecialchars($t['resident_name']) ?></p>
        <p><strong>Amount:</strong> ₱<?= number_format($t['amount'],2) ?></p>
        <button onclick="closeModal('receiptModal<?= $t['id'] ?>')">Close</button>
    </div>
</div>
<?php endforeach; ?>

<?php include '../../includes/footer.php'; ?>