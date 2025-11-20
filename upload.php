<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

$message = '';
$file_url = '';
$file_name = '';
$alertType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qr_type = $_POST['qr_type'] ?? 'pdf';
    $pdo = db();

    // =========================
    // 📄 CASE 1 — PDF → QR
    // =========================
    if ($qr_type === 'pdf' && isset($_FILES['pdf_file'])) {
        $file = $_FILES['pdf_file'];
        $allowed = ['application/pdf'];

        if (in_array($file['type'], $allowed)) {

            $target_dir = __DIR__ . '/uploads/';
            $unique_name = uniqid('pdf_', true) . '.pdf';
            $target_file = $target_dir . $unique_name;

            if (move_uploaded_file($file['tmp_name'], $target_file)) {
                // Public URL generate
                $base_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}/qr-portal/uploads/";
                $file_url = $base_url . $unique_name;

                // DB save
                $stmt = $pdo->prepare("INSERT INTO uploads (user_id, file_name, file_path, file_url, created_at) VALUES (?,?,?,?,NOW())");
                $stmt->execute([$_SESSION['user_id'], $file['name'], 'uploads/' . $unique_name, $file_url]);

                $file_name = $file['name'];
                $message = "PDF uploaded successfully!";
                $alertType = 'success';

            } else {
                $message = "Failed to upload PDF.";
                $alertType = 'error';
            }
        } else {
            $message = "Only PDF files are allowed.";
            $alertType = 'error';
        }
    }

    // =========================
    // 🔗 CASE 2 — LINK → QR
    // =========================
    elseif ($qr_type === 'link' && !empty($_POST['url_input'])) {

        $file_url = trim($_POST['url_input']);

        if (!filter_var($file_url, FILTER_VALIDATE_URL)) {
            $message = "Invalid URL format.";
            $alertType = 'error';

        } else {
            // Save as URL record
            $stmt = $pdo->prepare("INSERT INTO uploads (user_id, file_name, file_path, file_url, created_at) VALUES (?,?,?,?,NOW())");
            $stmt->execute([$_SESSION['user_id'], 'Custom Link', '', $file_url]);

            $file_name = "Custom URL";
            $message = "QR generated successfully for your link!";
            $alertType = 'success';
        }
    }

    else {
        $message = "Please provide a valid PDF or URL.";
        $alertType = 'error';
    }
}
?>

<style>
.page-hero {
  background: linear-gradient(120deg, #0d6efd, #1f8efd);
  color: #fff;
  border-radius: 18px;
  position: relative;
  overflow: hidden;
}
.page-hero::after {
  content: "";
  position: absolute;
  inset: 10% -20% auto auto;
  width: 220px;
  height: 220px;
  background: radial-gradient(circle at center, rgba(255,255,255,0.25), rgba(255,255,255,0));
  transform: rotate(-8deg);
}
.helper-chip {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.18);
  padding: 8px 12px;
  border-radius: 999px;
  color: #f8f9fa;
}
.stat-chip {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 12px;
  background: #f6f8fb;
  border: 1px solid #edf1f7;
}
.upload-dropzone {
  border: 1px dashed #b7c2d0;
  border-radius: 16px;
  padding: 20px;
  transition: all 0.2s ease;
  background: #f8fbff;
}
.upload-dropzone.hover {
  border-color: #0d6efd;
  box-shadow: 0 10px 30px rgba(13, 110, 253, 0.15);
  background: #eef5ff;
}
.upload-dropzone__icon {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  background: rgba(13,110,253,0.08);
  color: #0d6efd;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 1.4rem;
}
.pill-toggle .btn {
  border-radius: 10px;
  padding: 10px 14px;
}
.accent-badge {
  background: rgba(13,110,253,0.1);
  color: #0d6efd;
  border-radius: 20px;
  padding: 4px 10px;
  font-weight: 600;
}
.step-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 12px;
}
.step-list .icon {
  width: 32px;
  height: 32px;
  border-radius: 10px;
  background: #e8f1ff;
  color: #0d6efd;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-size: 0.95rem;
}
.result-card {
  border: 1px solid #e9f3ff;
  border-radius: 16px;
  overflow: hidden;
}
</style>

<!-- ======================== -->
<!--  MAIN CONTENT WRAPPER   -->
<!-- ======================== -->
<div class="content-wrapper px-4 py-3 content-page">

  <!-- PAGE HEADER -->

  <section class="content">
    <div class="container-fluid">

      <!-- FLASH MESSAGE -->
      <?php if ($message): ?>
        <div class="d-none"
             data-flash-message="<?= htmlspecialchars($message, ENT_QUOTES) ?>"
             data-flash-type="<?= $alertType ?>"
             data-flash-title="<?= $alertType === 'success' ? 'Success' : 'Error' ?>">
        </div>
      <?php endif; ?>

      <div class="card border-0 shadow-sm overflow-hidden">
        <div class="row g-0 align-items-stretch">
          <div class="col-lg-7">
            <form method="POST" enctype="multipart/form-data" class="h-100 p-4 d-flex flex-column gap-3">
              <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                  <p class="mb-1 text-muted text-uppercase small fw-semibold">Builder mode</p>
                  <h4 class="mb-0 fw-semibold text-primary">Choose your source</h4>
                </div>
                <span class="accent-badge"><i class="fas fa-lock me-1"></i>Secure upload</span>
              </div>

              <div class="pill-toggle btn-group" role="group" aria-label="QR type" id="qrTypePills">
                <button type="button" class="btn btn-primary active" data-qr-type="pdf">
                  <i class="fas fa-file-pdf me-2"></i>PDF Document
                </button>
                <button type="button" class="btn btn-outline-primary" data-qr-type="link">
                  <i class="fas fa-link me-2"></i>Website URL
                </button>
              </div>
              <select class="form-select d-none" name="qr_type" id="qrType" required>
                <option value="pdf">PDF Document</option>
                <option value="link">Website URL</option>
              </select>

              <!-- PDF Upload Section -->
              <div id="pdfUploadSection">
                <label class="form-label fw-semibold text-muted text-uppercase small">PDF document</label>
                <label for="pdfFile" class="upload-dropzone d-block" data-upload-dropzone role="button">
                  <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="upload-dropzone__icon">
                      <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="upload-dropzone__text">
                      <div class="d-flex align-items-center gap-2">
                        <strong>Drop your PDF or browse</strong>
                        <span class="badge bg-primary-subtle text-primary">PDF only</span>
                      </div>
                      <p class="small text-muted mb-0">Drag a file here or click to pick one from your device.</p>
                      <span class="d-block mt-2 small text-muted" data-upload-filename>No file selected yet.</span>
                    </div>
                  </div>
                </label>
                <input type="file" id="pdfFile" name="pdf_file" class="form-control d-none" accept="application/pdf">
              </div>

              <!-- URL Input Section -->
              <div class="d-none" id="linkInputSection">
                <label class="form-label fw-semibold text-muted text-uppercase small">Enter URL</label>
                <div class="input-group input-group-lg">
                  <span class="input-group-text bg-white"><i class="fas fa-link text-primary"></i></span>
                  <input type="url" name="url_input" class="form-control" placeholder="https://example.com">
                </div>
                <p class="small text-muted mt-2 mb-0">Paste a secure, reachable link. We'll keep the QR crisp.</p>
              </div>

              <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-auto">
                <div class="d-flex align-items-center gap-2 text-muted small">
                  <i class="fas fa-shield-check text-success"></i>
                  <span>We never alter your file content.</span>
                </div>
                <div class="d-flex gap-2">
                  <a href="history.php" class="btn btn-outline-secondary">
                    <i class="fas fa-list me-1"></i>Recent QR
                  </a>
                  <button type="submit" class="btn btn-primary">
                    <i class="fas fa-qrcode me-2"></i>Generate QR
                  </button>
                </div>
              </div>
            </form>
          </div>

          <div class="col-lg-5 bg-light border-start p-4">
            <div class="h-100 d-flex flex-column gap-3">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <div class="d-flex align-items-center mb-3">
                    <div class="upload-dropzone__icon me-2" style="background: rgba(25,135,84,0.12); color: #198754;">
                      <i class="fas fa-magic"></i>
                    </div>
                    <div>
                      <p class="mb-0 text-muted small text-uppercase fw-semibold">Workflow</p>
                      <h6 class="mb-0 fw-semibold">Ready in three steps</h6>
                    </div>
                  </div>
                  <ul class="list-unstyled step-list mb-0">
                    <li><span class="icon"><i class="fas fa-cloud-arrow-up"></i></span><span>Upload a PDF or paste a URL.</span></li>
                    <li><span class="icon"><i class="fas fa-qrcode"></i></span><span>We render a clean QR instantly.</span></li>
                    <li><span class="icon"><i class="fas fa-share-alt"></i></span><span>Download and share your link.</span></li>
                  </ul>
                </div>
              </div>

              <div class="card border-0 shadow-sm flex-grow-1">
                <div class="card-body">
                  <p class="text-muted text-uppercase small fw-semibold mb-2">Quality tips</p>
                  <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-white border text-primary"><i class="fas fa-check me-1"></i>Clear filenames</span>
                    <span class="badge bg-white border text-primary"><i class="fas fa-lock me-1"></i>Trusted links only</span>
                    <span class="badge bg-white border text-primary"><i class="fas fa-bolt me-1"></i>Fast generation</span>
                  </div>
                  <div class="mt-4">
                    <div class="d-flex align-items-center gap-2 mb-2">
                      <i class="fas fa-lightbulb text-warning"></i>
                      <strong class="text-body">Need consistent branding?</strong>
                    </div>
                    <p class="text-muted small mb-0">Save your preferred flow in history and reuse the same QR style for future drops.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ======================== -->
      <!--       RESULT CARD        -->
      <!-- ======================== -->
      <?php if ($file_url): ?>
        <div class="result-card shadow-sm mt-4 upload-result bg-white">
          <div class="p-3 bg-success-subtle">
            <div class="d-flex align-items-center gap-2 text-success">
              <i class="fas fa-check-circle"></i>
              <h6 class="mb-0 fw-semibold">QR Generated Successfully</h6>
            </div>
          </div>

          <div class="card-body">
            <div class="row g-4 align-items-center">

              <div class="col-md-6">
                <p class="mb-1 text-body-secondary small text-uppercase fw-semibold">Input Type</p>
                <p class="fs-6 mb-3"><?= htmlspecialchars($file_name) ?></p>

                <p class="mb-1 text-body-secondary small text-uppercase fw-semibold">Shareable Link</p>
                <a href="<?= htmlspecialchars($file_url) ?>" target="_blank" class="link-primary">
                    <?= htmlspecialchars($file_url) ?>
                </a>
              </div>

              <div class="col-md-6 text-center">
                <div id="qrcode" class="d-inline-block border bg-white p-3 rounded-3 shadow-sm"></div>
                <button class="btn btn-success mt-3" id="downloadQR">
                  <i class="fas fa-download me-2"></i>Download QR
                </button>
              </div>

            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/qrcode.min.js"></script>

<script>
// Toggle PDF or Link form state
const qrSelect = document.getElementById('qrType');
const pdfSection = document.getElementById('pdfUploadSection');
const linkSection = document.getElementById('linkInputSection');
const typePills = document.querySelectorAll('[data-qr-type]');

const syncType = (type) => {
  qrSelect.value = type;
  pdfSection.classList.toggle('d-none', type !== 'pdf');
  linkSection.classList.toggle('d-none', type !== 'link');

  typePills.forEach((btn) => {
    const isActive = btn.dataset.qrType === type;
    btn.classList.toggle('active', isActive);
    btn.classList.toggle('btn-primary', isActive);
    btn.classList.toggle('btn-outline-primary', !isActive);
  });
};

qrSelect.addEventListener('change', (e) => syncType(e.target.value));
typePills.forEach((btn) => {
  btn.addEventListener('click', (e) => {
    e.preventDefault();
    syncType(btn.dataset.qrType);
  });
});
syncType(qrSelect.value || 'pdf');

// Drag & Drop Logic
const pdfInput = document.getElementById('pdfFile');
const fileNameHolder = document.querySelector('[data-upload-filename]');
const dropZone = document.querySelector('[data-upload-dropzone]');

if (pdfInput && fileNameHolder && dropZone) {
  const updateFileName = () => {
    fileNameHolder.textContent = pdfInput.files.length
      ? pdfInput.files[0].name
      : 'No file selected yet.';
  };

  ['dragenter', 'dragover'].forEach((evt) => {
    dropZone.addEventListener(evt, (event) => {
      event.preventDefault();
      dropZone.classList.add('hover');
    });
  });

  ['dragleave', 'drop'].forEach((evt) => {
    dropZone.addEventListener(evt, (event) => {
      event.preventDefault();
      dropZone.classList.remove('hover');
    });
  });

  dropZone.addEventListener('drop', (event) => {
    event.preventDefault();
    if (event.dataTransfer && event.dataTransfer.files.length) {
      pdfInput.files = event.dataTransfer.files;
      pdfInput.dispatchEvent(new Event('change'));
    }
  });

  pdfInput.addEventListener('change', updateFileName);
  updateFileName();
}

// QR Code Generate
<?php if ($file_url): ?>
  const qrContainer = document.getElementById("qrcode");

  new QRCode(qrContainer, {
    text: "<?= htmlspecialchars($file_url) ?>",
    width: 200,
    height: 200
  });

  document.getElementById("downloadQR").addEventListener("click", () => {
    const img = qrContainer.querySelector("img") || qrContainer.querySelector("canvas");
    const a = document.createElement("a");
    a.href = img.src || img.toDataURL("image/png");
    a.download = "qr_code.png";
    a.click();
  });
<?php endif; ?>
</script>
