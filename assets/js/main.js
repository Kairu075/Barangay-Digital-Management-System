// ============================================================
// BARANGAY SAN MARINO - MAIN JS
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

    // Date & Time
    function updateClock() {
        const now = new Date();
        const dateEl = document.getElementById('currentDate');
        const timeEl = document.getElementById('currentTime');
        if (dateEl) {
            dateEl.textContent = now.toLocaleDateString('en-PH', {
                weekday: 'short', year: 'numeric', month: 'short', day: 'numeric'
            });
        }
        if (timeEl) {
            timeEl.textContent = now.toLocaleTimeString('en-PH', {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Sidebar Toggle (desktop)
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const mainWrapper = document.getElementById('mainWrapper');

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('collapsed');
            mainWrapper.classList.toggle('expanded');
        });
    }

    // Mobile Toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const overlay = document.getElementById('overlay');

    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            sidebar.classList.toggle('open');
            overlay.classList.toggle('show');
        });
    }

    if (overlay) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
        });
    }

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            alert.style.transition = 'all 0.4s ease';
            setTimeout(function () { alert.remove(); }, 400);
        }, 4000);
    });

    // Modal Functions
    window.openModal = function (id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.add('show');
    };

    window.closeModal = function (id) {
        const modal = document.getElementById(id);
        if (modal) modal.classList.remove('show');
    };

    // Close modal on backdrop click
    document.querySelectorAll('.modal-backdrop').forEach(function (backdrop) {
        backdrop.addEventListener('click', function (e) {
            if (e.target === backdrop) backdrop.classList.remove('show');
        });
    });

    // Search / Filter Table
    window.filterTable = function (inputId, tableId) {
        const input = document.getElementById(inputId);
        const table = document.getElementById(tableId);
        if (!input || !table) return;

        input.addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    };

    // Print Document
    window.printDocument = function (contentId) {
        const content = document.getElementById(contentId);
        if (!content) return;
        const win = window.open('', '_blank');
        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Barangay San Marino - Document</title>
                <style>
                    body { font-family: 'Times New Roman', serif; margin: 30px; font-size: 13px; line-height: 1.8; }
                    .doc-header { text-align: center; border-bottom: 3px double #333; padding-bottom: 16px; margin-bottom: 20px; }
                    .doc-header .barangay-name { font-size: 20px; font-weight: bold; }
                    .doc-header .doc-title { font-size: 15px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; border: 2px solid #333; display: inline-block; padding: 4px 20px; margin-top: 10px; }
                    .doc-body p { margin-bottom: 12px; text-align: justify; }
                    .signature-line { margin-top: 50px; text-align: center; }
                    .signature-line .sig-name { font-weight: bold; text-transform: uppercase; }
                    table { width: 100%; border-collapse: collapse; }
                    td, th { padding: 6px 10px; border: 1px solid #ddd; }
                    .or-receipt { border: 2px solid #333; padding: 20px; max-width: 400px; font-family: 'Courier New', monospace; }
                    .text-center { text-align: center; }
                    .fw-bold { font-weight: bold; }
                    @media print { body { margin: 0; } }
                </style>
            </head>
            <body>
                ${content.innerHTML}
                <script>window.onload = function(){ window.print(); }<\/script>
            </body>
            </html>
        `);
        win.document.close();
    };

    // Confirm Delete
    window.confirmDelete = function (message, formId) {
        if (confirm(message || 'Are you sure you want to delete this record?')) {
            if (formId) document.getElementById(formId).submit();
            return true;
        }
        return false;
    };

    // Dynamic fee loader
    const docTypeSelect = document.getElementById('document_type');
    const feeDisplay = document.getElementById('fee_display');
    const amountInput = document.getElementById('amount');

    const fees = {
        'Barangay Clearance': 100,
        'Certificate of Residency': 50,
        'Indigency Certificate': 0,
        'Business Clearance': 200,
        'Certificate of Good Moral Character': 75
    };

    if (docTypeSelect) {
        docTypeSelect.addEventListener('change', function () {
            const fee = fees[this.value] || 0;
            if (feeDisplay) feeDisplay.textContent = '₱' + fee.toFixed(2);
            if (amountInput) amountInput.value = fee.toFixed(2);
        });
    }

    // Resident search for document request
    const residentSearch = document.getElementById('resident_search');
    const residentResults = document.getElementById('resident_results');

    if (residentSearch) {
        let searchTimeout;
        residentSearch.addEventListener('keyup', function () {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            if (query.length < 2) {
                if (residentResults) residentResults.innerHTML = '';
                return;
            }
            searchTimeout = setTimeout(function () {
                fetch(SITE_URL + '/api/search_residents.php?q=' + encodeURIComponent(query))
                    .then(r => r.json())
                    .then(data => {
                        if (!residentResults) return;
                        residentResults.innerHTML = '';
                        if (data.length === 0) {
                            residentResults.innerHTML = '<div class="search-result-item text-muted">No residents found</div>';
                            return;
                        }
                        data.forEach(function (res) {
                            const item = document.createElement('div');
                            item.className = 'search-result-item';
                            item.textContent = res.full_name + ' — ' + res.resident_id;
                            item.style.cssText = 'padding:10px;cursor:pointer;border-bottom:1px solid #eee;';
                            item.addEventListener('mouseenter', function () { this.style.background = '#e8eef7'; });
                            item.addEventListener('mouseleave', function () { this.style.background = ''; });
                            item.addEventListener('click', function () {
                                document.getElementById('resident_id').value = res.id;
                                residentSearch.value = res.full_name + ' (' + res.resident_id + ')';
                                residentResults.innerHTML = '';
                            });
                            residentResults.appendChild(item);
                        });
                    });
            }, 300);
        });
    }
});

// Global vars (set per-page)
var SITE_URL = SITE_URL || 'http://localhost/barangay';
