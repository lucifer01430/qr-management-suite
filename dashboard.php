<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

$pdo = db();

// Total uploads
$stmt = $pdo->prepare("SELECT COUNT(*) FROM uploads WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$total_uploads = $stmt->fetchColumn();

// Last upload date
$stmt = $pdo->prepare("SELECT MAX(created_at) FROM uploads WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$last_upload = $stmt->fetchColumn() ?: 'No uploads yet';

// User details
$stmt = $pdo->prepare("SELECT created_at FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$joined_on = $stmt->fetchColumn();

$flash = $_SESSION['message'] ?? '';
$flashType = $_SESSION['message_type'] ?? 'success';
$flashTitle = $_SESSION['message_title'] ?? 'Success';
if ($flash) {
  unset($_SESSION['message'], $_SESSION['message_type'], $_SESSION['message_title']);
}
?>

<?php if ($flash): ?>
<div class="d-none" data-flash-message="<?= htmlspecialchars($flash, ENT_QUOTES) ?>" data-flash-type="<?= htmlspecialchars($flashType, ENT_QUOTES) ?>" data-flash-title="<?= htmlspecialchars($flashTitle, ENT_QUOTES) ?>"></div>
<?php endif; ?>

<div class="content-wrapper px-4 py-3 content-dashboard">
  <section class="content-header">
    <div class="container-fluid">
      <h3 class="fw-bold mb-1">Welcome back, <?= htmlspecialchars($_SESSION['user_name']); ?></h3>
      <p class="text-muted mb-0">Here's your QR Portal snapshot.</p>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">

      <!-- Metrics -->
      <div class="row g-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card metric-card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <span class="text-uppercase text-muted small">Total uploads</span>
                  <h3 class="fw-bold mt-2 mb-0"><?= $total_uploads ?></h3>
                </div>
                <span class="icon-circle bg-primary-subtle text-primary">
                  <i class="fas fa-file-upload"></i>
                </span>
              </div>
              <a href="upload.php"
                 class="stretched-link text-decoration-none text-primary small fw-semibold mt-3 d-inline-flex align-items-center gap-2"
                 data-confirm="Head over to the upload workspace to add a new PDF and QR code."
                 data-confirm-title="Open upload page?"
                 data-confirm-button="Go to upload">
                Upload new <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card metric-card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <span class="text-uppercase text-muted small">Last upload</span>
                  <h3 class="fw-bold mt-2 mb-0"><?= $last_upload !== 'No uploads yet' ? date('d M Y', strtotime($last_upload)) : '&mdash;' ?></h3>
                </div>
                <span class="icon-circle bg-success-subtle text-success">
                  <i class="fas fa-clock"></i>
                </span>
              </div>
              <a href="history.php"
                 class="stretched-link text-decoration-none text-success small fw-semibold mt-3 d-inline-flex align-items-center gap-2"
                 data-confirm="View the complete record of your uploaded files and QR codes."
                 data-confirm-title="Open upload history?"
                 data-confirm-button="View history">
                View history <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card metric-card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <span class="text-uppercase text-muted small">QRs generated</span>
                  <h3 class="fw-bold mt-2 mb-0"><?= $total_uploads ?></h3>
                </div>
                <span class="icon-circle bg-warning-subtle text-warning">
                  <i class="fas fa-qrcode"></i>
                </span>
              </div>
              <a href="history.php"
                 class="stretched-link text-decoration-none text-warning small fw-semibold mt-3 d-inline-flex align-items-center gap-2"
                 data-confirm="Review the QR codes generated for your uploads."
                 data-confirm-title="Open QR list?"
                 data-confirm-button="Show QRs">
                Open QRs <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>

        <div class="col-sm-6 col-xl-3">
          <div class="card metric-card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <span class="text-uppercase text-muted small">Joined on</span>
                  <h3 class="fw-bold mt-2 mb-0"><?= date('d M Y', strtotime($joined_on)) ?></h3>
                </div>
                <span class="icon-circle bg-info-subtle text-info">
                  <i class="fas fa-user-check"></i>
                </span>
              </div>
              <a href="profile.php"
                 class="stretched-link text-decoration-none text-info small fw-semibold mt-3 d-inline-flex align-items-center gap-2"
                 data-confirm="Update your personal details, bio, and profile photo."
                 data-confirm-title="Go to profile?"
                 data-confirm-button="Manage profile">
                Manage profile <i class="fas fa-arrow-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Analytics -->
      <div class="row mt-1 g-4">
        <div class="col-md-8">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent border-0 pb-0">
              <h5 class="mb-0 fw-semibold text-primary"><i class="fas fa-chart-bar me-2"></i>Uploads per month</h5>
              <p class="text-muted small mb-0">Track how often you share files.</p>
            </div>
            <div class="card-body pt-3">
              <canvas id="uploadsChart" height="120"></canvas>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-transparent border-0 pb-0">
              <h5 class="mb-0 fw-semibold text-warning"><i class="fas fa-chart-pie me-2"></i>File type split</h5>
              <p class="text-muted small mb-0">See how your uploads break down.</p>
            </div>
            <div class="card-body pt-3">
              <canvas id="fileTypeChart" height="120"></canvas>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<?php
// Prepare chart data
$months = [];
$upload_counts = [];

for ($i = 5; $i >= 0; $i--) {
  $month_label = date('M Y', strtotime("-$i month"));
  $months[] = $month_label;
  $start = date('Y-m-01', strtotime("-$i month"));
  $end = date('Y-m-t', strtotime("-$i month"));
  $stmt = $pdo->prepare("SELECT COUNT(*) FROM uploads WHERE user_id = ? AND created_at BETWEEN ? AND ?");
  $stmt->execute([$_SESSION['user_id'], $start, $end]);
  $upload_counts[] = $stmt->fetchColumn();
}
$stmt = $pdo->prepare("SELECT file_name FROM uploads WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$pdfs = count($stmt->fetchAll());
$others = 0;
?>

<script>
const ctx1 = document.getElementById('uploadsChart').getContext('2d');
new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: <?= json_encode($months) ?>,
    datasets: [{
      label: 'Uploads',
      data: <?= json_encode($upload_counts) ?>,
      backgroundColor: '#007bff',
      borderRadius: 6
    }]
  },
  options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
});

const ctx2 = document.getElementById('fileTypeChart').getContext('2d');
new Chart(ctx2, {
  type: 'pie',
  data: {
    labels: ['PDF Files', 'Other Files'],
    datasets: [{ data: [<?= $pdfs ?>, <?= $others ?>], backgroundColor: ['#28a745', '#ffc107'] }]
  },
  options: { plugins: { legend: { position: 'bottom' } } }
});
</script>
