<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';

$pdo = db();
$user_id = $_SESSION['user_id'];

// Fetch user data
$stmt = $pdo->prepare("SELECT name, email, bio, profile_pic, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$uploadStmt = $pdo->prepare("SELECT COUNT(*) FROM uploads WHERE user_id = ?");
$uploadStmt->execute([$user_id]);
$uploadCount = (int) $uploadStmt->fetchColumn();

// Update Profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $bio = trim($_POST['bio']);

    // Handle image upload
    $profile_pic = $user['profile_pic'];
    if (!empty($_FILES['profile_pic']['name'])) {
        $fileName = 'uploads/profile_' . time() . '_' . basename($_FILES['profile_pic']['name']);
        $targetPath = __DIR__ . '/' . $fileName;
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $targetPath);
        $profile_pic = $fileName;
    }

    $stmt = $pdo->prepare("UPDATE users SET name = ?, bio = ?, profile_pic = ? WHERE id = ?");
    $stmt->execute([$name, $bio, $profile_pic, $user_id]);

    $_SESSION['user_name'] = $name;
    header("Location: profile.php?success=1");
    exit;
}
?>

<div class="content-wrapper px-4 py-3 content-page">
  <section class="content-header mb-3">
    <div class="container-fluid d-flex flex-wrap justify-content-between align-items-center gap-3">
      <h3 class="mb-0"><i class="fas fa-user me-2 text-primary"></i>My profile</h3>
      <a href="dashboard.php" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left me-1"></i>Back to dashboard
      </a>
    </div>
  </section>

  <section class="content">
    <div class="container-fluid">
      <?php if (isset($_GET['success'])): ?>
        <div class="d-none" data-flash-message="Profile updated successfully!" data-flash-type="success" data-flash-title="Profile saved"></div>
      <?php endif; ?>

      <?php
        $defaultPic = 'assets/default-avatar.png';
        $profilePic = (!empty($user['profile_pic']) && file_exists(__DIR__ . '/' . $user['profile_pic']))
            ? $user['profile_pic']
            : $defaultPic;
        $memberSince = !empty($user['created_at']) ? date('F Y', strtotime($user['created_at'])) : '—';
      ?>

      <div class="profile-shell">
        <div class="card border-0 shadow-sm profile-card">
          <form method="POST" enctype="multipart/form-data" class="row g-4 g-xl-5 align-items-start">
            <div class="col-xl-4">
              <div class="profile-summary h-100">
                <div class="profile-summary__avatar">
                  <div class="profile-avatar-wrap position-relative">
                    <img src="<?= $profilePic ?>" alt="Profile picture" class="profile-avatar-lg shadow-sm">
                    <label for="profilePicInput" class="profile-avatar-btn" role="button">
                      <i class="fas fa-camera me-2"></i>Change photo
                    </label>
                  </div>
                  <input type="file" id="profilePicInput" name="profile_pic" class="visually-hidden" accept="image/png, image/jpeg">
                  <p class="profile-upload-hint mt-3 mb-0" data-profile-upload-hint>PNG or JPG up to 2MB.</p>
                </div>
                <div class="profile-summary__meta">
                  <div class="profile-meta-item">
                    <span class="profile-meta-label">Member since</span>
                    <strong class="profile-meta-value"><?= $memberSince ?></strong>
                  </div>
                  <div class="profile-meta-item">
                    <span class="profile-meta-label">Total uploads</span>
                    <strong class="profile-meta-value"><?= $uploadCount ?></strong>
                  </div>
                  <div class="profile-meta-item">
                    <span class="profile-meta-label">Account email</span>
                    <strong class="profile-meta-value text-truncate"><?= htmlspecialchars($user['email']) ?></strong>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-xl-8">
              <div class="profile-form">
                <div class="profile-form__intro">
                  <span class="badge bg-primary-subtle text-primary fw-semibold px-3 py-2">Profile</span>
                  <h4 class="mt-3 mb-2">Personal details</h4>
                  <p class="text-muted mb-0">Make sure your information is accurate so team members know who they’re collaborating with.</p>
                </div>
                <div class="profile-form__fields mt-4">
                  <div class="row g-4">
                    <div class="col-md-6">
                      <label for="profileName" class="form-label">Full name</label>
                      <input type="text" id="profileName" name="name" class="form-control form-control-lg" value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label class="form-label">Primary email</label>
                      <div class="input-group input-group-lg">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
                      </div>
                    </div>
                    <div class="col-12">
                      <label for="profileBio" class="form-label">Bio</label>
                      <textarea id="profileBio" name="bio" class="form-control" rows="4" placeholder="Share a short summary about your role, experience, or interests."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                      <div class="form-text">Tip: Highlight recent projects or responsibilities to personalize your workspace.</div>
                    </div>
                  </div>
                  <div class="d-flex flex-wrap gap-3 mt-4">
                    <button type="submit" class="btn btn-primary px-4">
                      <i class="fas fa-save me-2"></i>Save changes
                    </button>
                    <a href="profile.php" class="btn btn-outline-secondary px-4">
                      <i class="fas fa-rotate-left me-2"></i>Reset
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
