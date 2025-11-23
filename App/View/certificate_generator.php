<!DOCTYPE html>
<html lang="en">
    <?php
        // Access control consistent with other pages
        require_once __DIR__ . '/middleware/ProtectAuth.php';
        require_once __DIR__ . '/middleware/RBACProtect.php';

        include 'template/header.php';
    ?>
    <body class="sb-nav-fixed">

        <?php include 'template/header_navigation.php'; ?>

        <div id="layoutSidenav">
            <?php include 'template/sidebar_navigation.php'; ?>
            <div id="layoutSidenav_content">

                <main>
                    <div class="container-fluid px-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="mt-4">Certificate Generator</h1>
                                <ol class="breadcrumb mb-4">
                                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Certificate Generator</li>
                                </ol>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-5">
                                <div class="card">
                                    <div class="card-header">
                                        <i class="bi bi-file-earmark-medical"></i> Input Details
                                    </div>
                                    <div class="card-body">
                                        <form id="certForm">
                                            <div class="mb-3">
                                                <label class="form-label">Full Name</label>
                                                <input type="text" id="fullName" class="form-control" placeholder="Juan dela Cruz" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Age</label>
                                                <input type="number" id="age" class="form-control" placeholder="30" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <input type="text" id="address" class="form-control" placeholder="Purok 1, Brgy. Biga" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Purpose</label>
                                                <select id="purpose" class="form-select">
                                                    <option value="Certificate of Residency">Certificate of Residency</option>
                                                    <option value="Certificate of Indigency">Certificate of Indigency</option>
                                                    <option value="Barangay Clearance">Barangay Clearance</option>
                                                    <option value="Other">Other</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Date</label>
                                                <input type="date" id="certDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                            <div class="d-grid gap-2">
                                                <button type="button" id="generateBtn" class="btn btn-primary">Generate Preview</button>
                                                <button type="button" id="printBtn" class="btn btn-success no-print">Print Certificate</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <div class="card">
                                        <div class="card-header">Notes</div>
                                        <div class="card-body small text-muted">
                                            - Fill the form then click <strong>Generate Preview</strong> to auto-fill the certificate template.<br>
                                            - Click <strong>Print Certificate</strong> to open your browser print dialog. Only the certificate area will be printed.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-7">
                                <div class="card">
                                    <div class="card-header">Certificate Preview</div>
                                    <div class="card-body" id="printableArea">
                                        <!-- print-area: only this will be visible when printing -->
                                        <div class="print-area">
                                            <div id="certificate" class="certificate-wrapper">
                                                <div class="certificate-header">
                                                    <img src="../../assets/img/BIGA-LOGO.png" alt="Barangay Logo" class="barangay-logo" onerror="this.style.display='none'">
                                                    <div class="certificate-title">
                                                        <h6 style="margin-bottom:6px;">Republic of the Philippines</h6>
                                                        <h5 style="margin-bottom:6px; font-weight:700;">BARANGAY BIGA</h5>
                                                        <div style="font-size:0.9rem; color:#444;">Municipality / City Name - Province</div>
                                                    </div>
                                                </div>

                                                <hr style="border-top:2px solid #333; margin-top:6px; margin-bottom:16px;">

                                                <div style="text-align:center; margin-bottom:14px;">
                                                    <h4 style="text-decoration: underline; font-weight:700;">CERTIFICATE</h4>
                                                    <div style="font-size:0.95rem; color:#555;">(This is a system-generated certificate)</div>
                                                </div>

                                                <div class="certificate-body" id="certificateBody">
                                                    <p style="text-align:justify;">This is to certify that <strong id="p_name">[Full Name]</strong>, <strong id="p_age">[age]</strong> years old, residing at <strong id="p_address">[address]</strong>, is known to me to be of good moral character and a resident of Barangay Biga. This certification is issued upon the request of the above-named person for the purpose of <strong id="p_purpose">[purpose]</strong>.</p>

                                                    <p style="text-align:justify;">Issued this <strong id="p_date">[date]</strong> at Barangay Biga, Municipality / City Name.</p>
                                                </div>

                                                <div class="certificate-footer">
                                                    <div class="small text-muted">Barangay Seal</div>
                                                    <div class="official-sign">
                                                        <div style="height:60px;"></div>
                                                        <div style="font-weight:700;">Punong Barangay</div>
                                                        <div style="font-size:0.9rem;">(Signature over printed name)</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </main>

                <?php include 'template/footer.php'; ?>
            </div>
        </div>

        <!-- Page-specific styles and print rules -->
        <style>
            /* Global print settings */
            @page { margin: 10mm; }
            html, body { height: auto; margin: 0; padding: 0; background: white; }

            /* Certificate page styles */
            .certificate-wrapper { border: 1px solid #333; padding: 18px; background: #fff; color: #000; box-sizing: border-box; width: 100%; max-width: 190mm; margin: 0 auto; }
            .certificate-header { display:flex; align-items:center; gap:16px; margin-bottom: 12px; }
            .barangay-logo { width: 80px; height: 80px; object-fit: contain; }
            .certificate-title { text-align: center; width:100%; }
            .certificate-body { margin-top: 18px; font-size: 1.05rem; line-height: 1.6; }
            .certificate-footer { margin-top: 28px; display:flex; justify-content:space-between; align-items:center; }
            .official-sign { text-align:center; width:40%; }

            /* Print styles: hide everything except the print-area and ensure no extra blank pages */
            @media print {
                html, body { height: auto; margin: 0; padding: 0; }
                body * { display: none !important; }
                #printableArea, #printableArea * { display: block !important; visibility: visible !important; }
                #printableArea { position: relative !important; top: 0 !important; left: 0 !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }
                .print-area { width: 100% !important; max-width: 190mm; margin: 0 auto !important; padding: 0 !important; }
                .certificate-wrapper { border: none; padding: 12px !important; }
                .certificate-wrapper, .print-area { page-break-inside: avoid !important; }
                .no-print { display: none !important; }
            }
        </style>

        <!-- Page scripts -->
        <script>
            function formatDate(input) {
                if (!input) return '';
                const d = new Date(input);
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                return d.toLocaleDateString(undefined, options);
            }

            function generateCertificate() {
                const name = document.getElementById('fullName').value.trim() || '[Full Name]';
                const age = document.getElementById('age').value.trim() || '[age]';
                const address = document.getElementById('address').value.trim() || '[address]';
                const purposeSelect = document.getElementById('purpose');
                const purpose = purposeSelect.options[purposeSelect.selectedIndex].value;
                const dateVal = document.getElementById('certDate').value;
                const formattedDate = formatDate(dateVal) || '[date]';

                document.getElementById('p_name').textContent = name;
                document.getElementById('p_age').textContent = age;
                document.getElementById('p_address').textContent = address;
                document.getElementById('p_purpose').textContent = purpose;
                document.getElementById('p_date').textContent = formattedDate;

                const bodyEl = document.getElementById('certificateBody');
                if (purpose === 'Certificate of Indigency') {
                    bodyEl.innerHTML = `<p style="text-align:justify;">This is to certify that <strong id=\"p_name\">${name}</strong>, <strong id=\"p_age\">${age}</strong> years old, residing at <strong id=\"p_address\">${address}</strong>, is a bona fide resident of Barangay Biga and qualifies for indigency assistance. This certification is issued upon request for the purpose of <strong id=\"p_purpose\">${purpose}</strong>.</p><p style="text-align:justify;">Issued this <strong id=\"p_date\">${formattedDate}</strong> at Barangay Biga, Municipality / City Name.</p>`;
                } else if (purpose === 'Barangay Clearance') {
                    bodyEl.innerHTML = `<p style="text-align:justify;">This is to certify that <strong id=\"p_name\">${name}</strong>, <strong id=\"p_age\">${age}</strong> years old, residing at <strong id=\"p_address\">${address}</strong>, has no pending criminal case recorded at Barangay Biga as of this date. This clearance is issued for the purpose of <strong id=\"p_purpose\">${purpose}</strong>.</p><p style="text-align:justify;">Issued this <strong id=\"p_date\">${formattedDate}</strong> at Barangay Biga, Municipality / City Name.</p>`;
                } else {
                    bodyEl.innerHTML = `<p style="text-align:justify;">This is to certify that <strong id=\"p_name\">${name}</strong>, <strong id=\"p_age\">${age}</strong> years old, residing at <strong id=\"p_address\">${address}</strong>, is known to me to be a resident of Barangay Biga. This certification is issued for the purpose of <strong id=\"p_purpose\">${purpose}</strong>.</p><p style="text-align:justify;">Issued this <strong id=\"p_date\">${formattedDate}</strong> at Barangay Biga, Municipality / City Name.</p>`;
                }
            }

            document.getElementById('generateBtn').addEventListener('click', function() {
                generateCertificate();
                document.getElementById('printableArea').scrollIntoView({behavior:'smooth'});
            });

            document.getElementById('printBtn').addEventListener('click', function() {
                generateCertificate();
                window.print();
            });

            document.getElementById('certForm').addEventListener('submit', function(e){ e.preventDefault(); generateCertificate(); });
        </script>
    </body>
</html>
<?php
// Minimal access check (adjust as needed)
// If your system already includes authentication, you can remove or adapt this
if (file_exists(__DIR__ . '/../Config/Auth.php')) {
    require_once __DIR__ . '/../Config/Auth.php';
    if (function_exists('isAuthenticated') && !isAuthenticated()) {
        header('Location: ../../index.php');
        exit();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Certificate Generator</title>
    <link rel="stylesheet" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        /* Global print settings */
        @page { margin: 10mm; }
        html, body { height: auto; margin: 0; padding: 0; background: white; }

        /* Certificate page styles */
        .certificate-wrapper {
            border: 1px solid #333;
            padding: 18px;
            background: #fff;
            color: #000;
            box-sizing: border-box;
            width: 100%;
            max-width: 190mm; /* fit A4 minus margins */
            margin: 0 auto;
        }
        .certificate-header {
            display:flex;
            align-items:center;
            gap:16px;
            margin-bottom: 12px;
        }
        .barangay-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .certificate-title {
            text-align: center;
            width:100%;
        }
        .certificate-title h4 { margin:0; }
        .certificate-body {
            margin-top: 18px;
            font-size: 1.05rem;
            line-height: 1.6;
        }
        .certificate-footer {
            margin-top: 28px;
            display:flex;
            justify-content:space-between;
            align-items:center;
        }
        .official-sign {
            text-align:center;
            width:40%;
        }

        .print-hidden { display: none; }

        /* Print styles: hide everything except the print-area and ensure no extra blank pages */
        @media print {
            /* Reset page and body spacing */
            html, body { height: auto; margin: 0; padding: 0; }

            /* Hide all elements by default */
            body * { display: none !important; }

            /* Show only the printable container and its children */
            #printableArea, #printableArea * { display: block !important; visibility: visible !important; }

            /* Ensure printable container occupies top of page with no offsets */
            #printableArea { position: relative !important; top: 0 !important; left: 0 !important; width: 100% !important; margin: 0 !important; padding: 0 !important; }

            /* Center the certificate inside the printable area and remove extra spacing */
            .print-area { width: 100% !important; max-width: 190mm; margin: 0 auto !important; padding: 0 !important; }
            .certificate-wrapper { border: none; padding: 12px !important; }

            /* Remove any page breaks inside certificate */
            .certificate-wrapper, .print-area { page-break-inside: avoid !important; }

            /* Hide elements specifically not intended for print */
            .no-print { display: none !important; }
        }

        .form-label { font-weight:600; }
    </style>
</head>
<body>
    <div class="container-fluid px-4 mt-4">
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-file-earmark-medical"></i> Certificate Generator - Input Details
                    </div>
                    <div class="card-body">
                        <form id="certForm">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" id="fullName" class="form-control" placeholder="Juan dela Cruz" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Age</label>
                                <input type="number" id="age" class="form-control" placeholder="30" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <input type="text" id="address" class="form-control" placeholder="Purok 1, Brgy. Biga" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Purpose</label>
                                <select id="purpose" class="form-select">
                                    <option value="Certificate of Residency">Certificate of Residency</option>
                                    <option value="Certificate of Indigency">Certificate of Indigency</option>
                                    <option value="Barangay Clearance">Barangay Clearance</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Date</label>
                                <input type="date" id="certDate" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="button" id="generateBtn" class="btn btn-primary">Generate Preview</button>
                                <button type="button" id="printBtn" class="btn btn-success no-print">Print Certificate</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-3">
                    <div class="card">
                        <div class="card-header">Notes</div>
                        <div class="card-body small text-muted">
                            - Fill the form then click <strong>Generate Preview</strong> to auto-fill the certificate template.<br>
                            - Click <strong>Print Certificate</strong> to open your browser print dialog. Only the certificate area will be printed.
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header">Certificate Preview</div>
                    <div class="card-body" id="printableArea">
                        <div id="certificate" class="certificate-wrapper">
                            <div class="certificate-header">
                                <img src="../../assets/img/BIGA-LOGO.png" alt="Barangay Logo" class="barangay-logo" onerror="this.style.display='none'">
                                <div class="certificate-title">
                                    <h6 style="margin-bottom:6px;">Republic of the Philippines</h6>
                                    <h5 style="margin-bottom:6px; font-weight:700;">BARANGAY BIGA</h5>
                                    <div style="font-size:0.9rem; color:#444;">Municipality / City Name - Province</div>
                                </div>
                            </div>

                            <hr style="border-top:2px solid #333; margin-top:6px; margin-bottom:16px;">

                            <div style="text-align:center; margin-bottom:14px;">
                                <h4 style="text-decoration: underline; font-weight:700;">CERTIFICATE</h4>
                                <div style="font-size:0.95rem; color:#555;">(This is a system-generated certificate)</div>
                            </div>

                            <div class="certificate-body" id="certificateBody">
                                <!-- Auto-filled content will appear here -->
                                <p style="text-align:justify;">This is to certify that <strong id="p_name">[Full Name]</strong>, <strong id="p_age">[age]</strong> years old, residing at <strong id="p_address">[address]</strong>, is known to me to be of good moral character and a resident of Barangay Biga. This certification is issued upon the request of the above-named person for the purpose of <strong id="p_purpose">[purpose]</strong>.</p>

                                <p style="text-align:justify;">Issued this <strong id="p_date">[date]</strong> at Barangay Biga, Municipality / City Name.</p>
                            </div>

                            <div class="certificate-footer">
                                <div class="small text-muted">Barangay Seal</div>
                                <div class="official-sign">
                                    <div style="height:60px;"></div>
                                    <div style="font-weight:700;">Punong Barangay</div>
                                    <div style="font-size:0.9rem;">(Signature over printed name)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Utility: format date to a more readable form
        function formatDate(input) {
            if (!input) return '';
            const d = new Date(input);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            return d.toLocaleDateString(undefined, options);
        }

        function generateCertificate() {
            const name = document.getElementById('fullName').value.trim() || '[Full Name]';
            const age = document.getElementById('age').value.trim() || '[age]';
            const address = document.getElementById('address').value.trim() || '[address]';
            const purposeSelect = document.getElementById('purpose');
            const purpose = purposeSelect.options[purposeSelect.selectedIndex].value;
            const dateVal = document.getElementById('certDate').value;
            const formattedDate = formatDate(dateVal) || '[date]';

            document.getElementById('p_name').textContent = name;
            document.getElementById('p_age').textContent = age;
            document.getElementById('p_address').textContent = address;
            document.getElementById('p_purpose').textContent = purpose;
            document.getElementById('p_date').textContent = formattedDate;

            // Optionally adjust certificate body for specific certificate types
            const bodyEl = document.getElementById('certificateBody');
            if (purpose === 'Certificate of Indigency') {
                bodyEl.innerHTML = `<p style="text-align:justify;">This is to certify that <strong id=\"p_name\">${name}</strong>, <strong id=\"p_age\">${age}</strong> years old, residing at <strong id=\"p_address\">${address}</strong>, is a bona fide resident of Barangay Biga and qualifies for indigency assistance. This certification is issued upon request for the purpose of <strong id=\"p_purpose\">${purpose}</strong>.</p><p style="text-align:justify;">Issued this <strong id=\"p_date\">${formattedDate}</strong> at Barangay Biga, Municipality / City Name.</p>`;
            } else if (purpose === 'Barangay Clearance') {
                bodyEl.innerHTML = `<p style="text-align:justify;">This is to certify that <strong id=\"p_name\">${name}</strong>, <strong id=\"p_age\">${age}</strong> years old, residing at <strong id=\"p_address\">${address}</strong>, has no pending criminal case recorded at Barangay Biga as of this date. This clearance is issued for the purpose of <strong id=\"p_purpose\">${purpose}</strong>.</p><p style="text-align:justify;">Issued this <strong id=\"p_date\">${formattedDate}</strong> at Barangay Biga, Municipality / City Name.</p>`;
            } else {
                bodyEl.innerHTML = `<p style="text-align:justify;">This is to certify that <strong id=\"p_name\">${name}</strong>, <strong id=\"p_age\">${age}</strong> years old, residing at <strong id=\"p_address\">${address}</strong>, is known to me to be a resident of Barangay Biga. This certification is issued for the purpose of <strong id=\"p_purpose\">${purpose}</strong>.</p><p style="text-align:justify;">Issued this <strong id=\"p_date\">${formattedDate}</strong> at Barangay Biga, Municipality / City Name.</p>`;
            }
        }

        document.getElementById('generateBtn').addEventListener('click', function() {
            generateCertificate();
            // Move to preview area (optional): scroll into view
            document.getElementById('printableArea').scrollIntoView({behavior:'smooth'});
        });

        document.getElementById('printBtn').addEventListener('click', function() {
            // Ensure certificate is generated before printing
            generateCertificate();
            window.print();
        });

        // Allow Enter to generate when inside form
        document.getElementById('certForm').addEventListener('submit', function(e){
            e.preventDefault();
            generateCertificate();
        });

        // Prefill demo values for convenience (optional)
        // document.getElementById('fullName').value = 'Juan dela Cruz';
        // document.getElementById('age').value = '30';
        // document.getElementById('address').value = 'Purok 1, Brgy. Biga';
        // document.getElementById('purpose').value = 'Certificate of Residency';
        // generateCertificate();
    </script>
</body>
</html>
