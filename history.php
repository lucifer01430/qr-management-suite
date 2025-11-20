<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM uploads WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$uploads = $stmt->fetchAll();
?>

<div class="content-wrapper px-4 py-3 content-page">
  <section class="content-header">
    <div class="container-fluid">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h3 class="fw-bold mb-1"><i class="fas fa-history text-primary me-2"></i>Uploads history</h3>
          <p class="text-muted mb-0">Review your uploaded PDFs and custom links with their QR codes.</p>
        </div>
        <a href="upload.php" class="btn btn-outline-primary btn-sm">
          <i class="fas fa-plus me-1"></i>New upload
        </a>
      </div>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <?php if (count($uploads) === 0): ?>
            <div class="alert alert-info text-center soft-alert">
              <i class="fas fa-circle-info me-2"></i>You haven't uploaded anything yet.
            </div>
          <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Type</th>
                  <th>Title</th>
                  <th>Uploaded On</th>
                  <th>Open</th>
                  <th>QR Code</th>
                  <th>Download QR</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $count = 1; 
                foreach ($uploads as $file): 

                // Detect Type
                $isPDF = !empty($file['file_path']); // PDF has file_path
                $type = $isPDF ? "PDF" : "Link";

                // Button Label
                $openLabel = $isPDF ? "Open PDF" : "Open Link";
                ?>
                
                <tr>
                  <td><?= $count++ ?></td>

                  <!-- TYPE BADGE -->
                  <td>
                    <span class="badge bg-<?= $isPDF ? 'primary' : 'success' ?>">
                      <?= $type ?>
                    </span>
                  </td>

                  <!-- TITLE -->
                  <td><?= htmlspecialchars($file['file_name']) ?></td>

                  <!-- DATE -->
                  <td><?= date("d M Y, h:i A", strtotime($file['created_at'])) ?></td>

                  <!-- OPEN LINK -->
                  <td>
                    <a href="<?= htmlspecialchars($file['file_url']) ?>" 
                       target="_blank" 
                       class="btn btn-sm btn-outline-primary">
                       <?= $openLabel ?>
                    </a>
                  </td>

                  <!-- QR CODE -->
                  <td>
                    <div class="qr-box mx-auto" 
                         id="qr_<?= $file['id'] ?>" 
                         data-url="<?= htmlspecialchars($file['file_url']) ?>">
                    </div>
                  </td>

                  <!-- DOWNLOAD -->
                  <td>
                    <button class="btn btn-sm btn-outline-success" 
                            onclick="downloadQR('qr_<?= $file['id'] ?>')">
                      <i class="fas fa-download me-1"></i>Download
                    </button>
                  </td>
                </tr>

                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script src="assets/js/qrcode.min.js"></script>
<script>
// Generate QR for each row
document.querySelectorAll('.qr-box').forEach(box => {
  new QRCode(box, {
    text: box.dataset.url,
    width: 80,
    height: 80
  });
});

function downloadQR(id) {
  const qrDiv = document.getElementById(id);
  const img = qrDiv.querySelector("img") || qrDiv.querySelector("canvas");
  const a = document.createElement("a");
  a.href = img.src || img.toDataURL("image/png");
  a.download = "QR_Code.png";
  a.click();
}
</script>
