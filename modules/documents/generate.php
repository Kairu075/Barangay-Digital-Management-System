<?php
require_once '../../includes/config.php';
requireLogin();
$page_title = 'Generate Document';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: index.php'); exit; }

$stmt = $pdo->prepare("SELECT dr.*, r.*, CONCAT(r.first_name,' ',r.middle_name,' ',r.last_name) as full_name, r.birthdate, h.address, h.purok FROM document_requests dr JOIN residents r ON dr.resident_id=r.id LEFT JOIN households h ON r.household_id=h.id WHERE dr.id=?");
$stmt->execute([$id]);
$doc = $stmt->fetch();

if (!$doc) { header('Location: index.php'); exit; }

$age = getAge($doc['birthdate']);
$doc_date = date('F d, Y');
$control_no = 'CTRL-' . date('Y') . '-' . str_pad($id, 5, '0', STR_PAD_LEFT);

include '../../includes/header.php';
?>
<style>
.doc-preview { background: white; max-width: 780px; margin: 0 auto; box-shadow: 0 4px 30px rgba(0,0,0,0.15); border-radius: 8px; overflow: hidden; }
.doc-watermark { position: relative; }
.doc-watermark::before { content: '<?= strtoupper(preg_replace('/[^A-Za-z ]/', '', $doc['document_type'])) ?>'; position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%) rotate(-30deg); font-size: 80px; font-weight: 900; color: rgba(68,108,172,0.04); pointer-events: none; z-index: 0; white-space: nowrap; }
.doc-inner { position: relative; z-index: 1; padding: 50px 60px; font-family: 'Times New Roman', serif; }
.doc-gold-bar { height: 6px; background: linear-gradient(90deg, #2d4d80, #446CAC, #FBC531, #446CAC, #2d4d80); }
.doc-header-strip { background: linear-gradient(135deg, #1e3557, #2d4d80); padding: 20px 50px; display: flex; align-items: center; gap: 20px; }
.header-seal { width: 70px; height: 70px; background: #FBC531; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; overflow: hidden; }
.header-seal img { width: 100%; height: 100%; object-fit: cover; }
.header-text { color: white; }
.header-text .republic { font-size: 11px; letter-spacing: 2px; opacity: 0.7; }
.header-text .brgy-name { font-size: 22px; font-weight: 900; font-family: 'Lora', serif; letter-spacing: 0.5px; }
.header-text .brgy-addr { font-size: 11.5px; opacity: 0.7; }
.header-right { margin-left: auto; text-align: right; color: rgba(255,255,255,0.7); font-size: 11px; }

.doc-title-area { text-align: center; margin-bottom: 30px; }
.doc-title-area .cert-title { font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 3px; border: 2.5px solid #333; display: inline-block; padding: 7px 30px; margin-bottom: 5px; }
.doc-title-area .ctrl { font-size: 11px; color: #666; }

.doc-body-text { font-size: 13.5px; line-height: 1.9; color: #222; text-align: justify; }
.doc-body-text .highlight { font-weight: bold; text-transform: uppercase; border-bottom: 1.5px solid #333; padding: 0 2px; }
.doc-sig-area { margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }
.sig-box { text-align: center; }
.sig-name { font-weight: bold; text-transform: uppercase; font-size: 13px; margin-top: 40px; border-top: 1.5px solid #333; padding-top: 6px; }
.sig-title { font-size: 11.5px; color: #555; }
.doc-notarize { margin-top: 30px; border: 1px solid #ddd; padding: 15px 20px; font-size: 12px; background: #fafafa; }
.doc-footer-strip { background: linear-gradient(135deg, #1e3557, #2d4d80); padding: 12px 50px; color: rgba(255,255,255,0.6); font-size: 10.5px; display: flex; justify-content: space-between; }

@media print {
    .no-print { display: none !important; }
    .doc-preview { box-shadow: none; }
    body, html { margin: 0; padding: 0; }
}
</style>

<div class="page-header flex-between no-print">
    <div>
        <h1><i class="fas fa-file-certificate"></i> Document Generator</h1>
        <p><?= $doc['request_no'] ?> — <?= $doc['document_type'] ?></p>
    </div>
    <div class="btn-group">
        <a href="index.php" class="btn btn-outline no-print"><i class="fas fa-arrow-left"></i> Back</a>
        <button onclick="window.print()" class="btn btn-primary no-print"><i class="fas fa-print"></i> Print Document</button>
    </div>
</div>

<?php if ($doc['status'] !== 'Approved' && $doc['status'] !== 'Released'): ?>
<div class="alert alert-warning no-print"><i class="fas fa-triangle-exclamation"></i> This document is <strong><?= $doc['status'] ?></strong>. It must be approved before it can be officially released. You may still print a draft for review.</div>
<?php endif; ?>

<div class="doc-preview" id="printDoc">
    <div class="doc-gold-bar"></div>
    <div class="doc-header-strip">
<div class="header-seal"><img src="../../assets/img/logo.png" alt="Barangay Logo"></div>
        <div class="header-text">
            <div class="republic">REPUBLIC OF THE PHILIPPINES</div>
            <div class="brgy-name">Barangay Nangka</div>
            <div class="brgy-addr">City of Marikina, Metro Manila</div>
        </div>
        <div class="header-right">
            <div>Contact: (02) 8123-4567</div>
            <div>Email: bnangka@gmail.com</div>
            <div>Date: <?= $doc_date ?></div>
        </div>
    </div>

    <div class="doc-watermark">
        <div class="doc-inner">
            <div class="doc-title-area">
                <div class="cert-title"><?= $doc['document_type'] ?></div>
                <div class="ctrl">Control No.: <?= $control_no ?> &nbsp;|&nbsp; Request No.: <?= $doc['request_no'] ?></div>
            </div>

            <div class="doc-body-text">
                <p style="text-indent:50px;">
                    <strong>TO WHOM IT MAY CONCERN:</strong>
                </p>

                <?php if ($doc['document_type'] === 'Barangay Clearance'): ?>
                <p style="text-indent:50px;">
                    This is to certify that <span class="highlight"><?= strtoupper($doc['full_name']) ?></span>, 
                    <?= $age ?> years old, <?= $doc['civil_status'] ?>, a bonafide resident of 
                    <span class="highlight"><?= htmlspecialchars($doc['address'] ?? 'Barangay Nangka') ?></span>, 
                    this Barangay, is personally known to this office to be of good moral character and has no 
                    derogatory record on file as of this date.
                </p>
                <p style="text-indent:50px;">
                    This certification is issued upon the request of the above-named person for the purpose of 
                    <span class="highlight"><?= strtoupper($doc['purpose']) ?></span> and for whatever legal 
                    purpose it may serve.
                </p>

                <?php elseif ($doc['document_type'] === 'Certificate of Residency'): ?>
                <p style="text-indent:50px;">
                    This is to certify that <span class="highlight"><?= strtoupper($doc['full_name']) ?></span>, 
                    <?= $age ?> years old, <?= $doc['gender'] ?>, <?= $doc['civil_status'] ?>, with Resident 
                    ID No. <strong><?= $doc['resident_id'] ?></strong>, is a bonafide resident and legal 
                    occupant of <span class="highlight"><?= htmlspecialchars($doc['address'] ?? 'Barangay Nangka, City of Marikina') ?></span>.
                </p>
                <p style="text-indent:50px;">
                    The above-named individual has been residing in this Barangay for a considerable period 
                    of time and is known to the community.
                </p>
                <p style="text-indent:50px;">
                    This certification is issued upon the request of the above-named person in connection with 
                    <span class="highlight"><?= strtoupper($doc['purpose']) ?></span>.
                </p>

                <?php elseif ($doc['document_type'] === 'Indigency Certificate'): ?>
                <p style="text-indent:50px;">
                    This is to certify that <span class="highlight"><?= strtoupper($doc['full_name']) ?></span>, 
                    <?= $age ?> years old, <?= $doc['civil_status'] ?>, residing at 
                    <span class="highlight"><?= htmlspecialchars($doc['address'] ?? 'Barangay Nangka') ?></span>, 
                    belongs to an indigent family in this Barangay.
                </p>
                <p style="text-indent:50px;">
                    Based on our records, the above-named individual and their family are in need of financial 
                    assistance and is hereby certified as an indigent member of this community.
                </p>
                <p style="text-indent:50px;">
                    This certification is issued in connection with 
                    <span class="highlight"><?= strtoupper($doc['purpose']) ?></span> and for whatever 
                    legal purpose it may serve.
                </p>

                <?php else: ?>
                <p style="text-indent:50px;">
                    This is to certify that <span class="highlight"><?= strtoupper($doc['full_name']) ?></span>, 
                    <?= $age ?> years old, <?= $doc['civil_status'] ?>, a resident of 
                    <span class="highlight"><?= htmlspecialchars($doc['address'] ?? 'Barangay Nangka') ?></span> 
                    has requested the issuance of this <strong><?= $doc['document_type'] ?></strong> for the 
                    purpose of <span class="highlight"><?= strtoupper($doc['purpose']) ?></span>.
                </p>
                <?php endif; ?>

                <p style="text-indent:50px;">
                    Issued this <strong><?= date('jS') ?> day of <?= date('F Y') ?></strong> at Barangay Nangka, 
                    City of Marikina, Philippines.
                </p>
            </div>

            <div class="doc-sig-area">
                <div class="sig-box">
                    <div style="font-size:12px;color:#555;">Prepared by:</div>
                    <div class="sig-name">Maria Santos</div>
                    <div class="sig-title">Barangay Secretary</div>
                </div>
                <div class="sig-box">
                    <div style="font-size:12px;color:#555;">Approved by:</div>
                    <div class="sig-name">Hon. Roberto Reyes</div>
                    <div class="sig-title">Barangay Captain</div>
                    <div class="sig-title">Barangay Nangka, City of Marikina</div>
                </div>
            </div>

            <div class="doc-notarize">
                <div style="font-weight:bold;margin-bottom:6px;font-size:12px;">OFFICIAL RECEIPT INFORMATION</div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:10px;font-size:12px;">
                    <div><strong>O.R. No.:</strong> <?= $doc['or_number'] ?: 'PENDING' ?></div>
                    <div><strong>Amount:</strong> ₱<?= number_format($doc['amount'], 2) ?></div>
                    <div><strong>Payment:</strong> <?= $doc['payment_status'] ?></div>
                    <div><strong>Doc Status:</strong> <?= $doc['status'] ?></div>
                    <div><strong>Requested:</strong> <?= date('M d, Y', strtotime($doc['requested_at'])) ?></div>
                    <div><strong>Purpose:</strong> <?= $doc['purpose'] ?></div>
                </div>
                <div style="margin-top:10px;font-size:11px;color:#888;font-style:italic;">
                    ⚠️ This document is valid only for the purpose stated herein. Any alteration renders this document null and void.
                </div>
            </div>
        </div>
    </div>

    <div class="doc-gold-bar"></div>
    <div class="doc-footer-strip">
        <span>Barangay Nangka | City of Marikina, Metro Manila | Tel: (02) 8123-4567</span>
        <span>Generated: <?= date('Y-m-d H:i:s') ?> | <?= $control_no ?></span>
    </div>
</div>

<script>
// Auto SITE_URL for JS
var SITE_URL = '<?= SITE_URL ?>';
</script>

<?php include '../../includes/footer.php'; ?>
