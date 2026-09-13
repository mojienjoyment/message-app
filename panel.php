<?php
session_start();
require 'db.php';

// 🔒 Guard: no login session → go to login
if (!isset($_SESSION['user_id'])) {
  header('Location: login.php');
  exit;
}

$error = $success = '';

/* ════════════════════════════════════════════════
   PART 1 — HANDLE THE UPDATE (runs on form submit)
   ════════════════════════════════════════════════ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = strtolower(trim($_POST['email'] ?? ''));
  $currentPass = $_POST['current_password'] ?? '';
  $newPass = $_POST['new_password'] ?? '';
  $confirmPass = $_POST['confirm_password'] ?? '';

  // fetch the current user from the DB
  $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
  $stmt->execute([$_SESSION['user_id']]);
  $user = $stmt->fetch();

  if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
  }

  // verify current password before allowing any change
  if (!password_verify($currentPass, $user['password'])) {
    $error = 'Current password is incorrect.';
  } else {
    $errors = [];

    if ($name === '' || mb_strlen($name) > 60)
      $errors[] = 'Name is required (max 60 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
      $errors[] = 'Enter a valid email address.';

    // optional password change
    $changePassword = false;
    if ($newPass !== '') {
      $changePassword = true;
      if (strlen($newPass) < 8)
        $errors[] = 'New password must be at least 8 characters.';
      if ($newPass !== $confirmPass)
        $errors[] = 'New passwords do not match.';
    }

    // handle profile picture upload
    $newProfilePic = null;
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
      $file = $_FILES['profile_pic'];
      $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
      $maxSize = 5 * 1024 * 1024; // 5 MB

      // validate MIME type
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mimeType = finfo_file($finfo, $file['tmp_name']);
      finfo_close($finfo);

      if (!in_array($mimeType, $allowedTypes)) {
        $errors[] = 'Only JPG, PNG, GIF, and WebP images are allowed.';
      } elseif ($file['size'] > $maxSize) {
        $errors[] = 'Image must be smaller than 5 MB.';
      } else {
        // generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $safeExt = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? $extension : 'jpg';
        $filename = 'user_' . $_SESSION['user_id'] . '_' . time() . '.' . $safeExt;
        $destination = __DIR__ . '/user_profiles/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
          $newProfilePic = $filename;
        } else {
          $errors[] = 'Failed to upload image.';
        }
      }
    }

    if (!$errors) {
      $updates = [];
      $params = [];

      $updates[] = 'name = ?';
      $params[] = $name;

      $updates[] = 'email = ?';
      $params[] = $email;

      if ($changePassword) {
        $updates[] = 'password = ?';
        $params[] = password_hash($newPass, PASSWORD_DEFAULT);
      }

      if ($newProfilePic !== null) {
        $updates[] = 'profile_pic = ?';
        $params[] = $newProfilePic;
      }

      $params[] = $_SESSION['user_id'];

      $sql = 'UPDATE users SET ' . implode(', ', $updates) . ' WHERE id = ?';
      $stmt = $pdo->prepare($sql);
      $stmt->execute($params);

      // keep the session in sync
      $_SESSION['name'] = $name;
      $_SESSION['email'] = $email;

      $success = 'Profile updated successfully.';
    } else {
      $error = implode(' ', $errors);
    }
  }
}

/* ════════════════════════════════════════════════
   PART 2 — FETCH FRESH DATA TO DISPLAY
   ════════════════════════════════════════════════ */
$stmt = $pdo->prepare('SELECT id, name, username, email, profile_pic, created_at FROM users WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
  session_destroy();
  header('Location: login.php');
  exit;
}

// build the image URL
$picUrl = 'user_profiles/' . htmlspecialchars($user['profile_pic']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Panel</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 0 1rem;
    }

    input[readonly] {
      background: #f0f1f5;
      color: #888;
      cursor: not-allowed;
    }

    .divider {
      margin: 1.5rem 0 1rem;
      border: 0;
      border-top: 1px dashed #d0d4e0;
    }

    .hint {
      font-size: .8rem;
      color: #888;
      margin-top: -.6rem;
      margin-bottom: 1rem;
    }

    .profile-pic-section {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      margin-bottom: 1.5rem;
      padding-bottom: 1.5rem;
      border-bottom: 1px dashed #d0d4e0;
    }

    .profile-pic-preview {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      object-fit: cover;
      border: 3px solid #4a6cf7;
      box-shadow: 0 4px 12px rgba(0, 0, 0, .1);
    }

    .profile-pic-controls {
      flex: 1;
    }

    .profile-pic-controls label {
      margin-bottom: .5rem;
    }

    .file-input-wrapper {
      position: relative;
      display: inline-block;
    }

    .file-input-wrapper input[type="file"] {
      width: 100%;
      padding: .5rem;
      border: 1px dashed #c9cdd8;
      border-radius: 6px;
      cursor: pointer;
    }

    .file-input-wrapper input[type="file"]:hover {
      border-color: #4a6cf7;
    }
  </style>
</head>

<body class="page-panel">

  <div class="topbar">
    <strong>My Panel</strong>
    <a href="logout.php">Logout</a>
  </div>

  <div class="wrap">
    <div class="card">
      <h1>Edit Profile</h1>

        <?php if ($success): ?>
        <p class="box ok">✅ <?= htmlspecialchars($success) ?></p><?php endif; ?>
        <?php if ($error): ?>
        <p class="box err"><?= htmlspecialchars($error) ?></p><?php endif; ?>

      <form method="post" action="panel.php" enctype="multipart/form-data">

        <!-- Profile Picture Section -->
        <div class="profile-pic-section">
          <img src="<?= $picUrl ?>" alt="Profile" class="profile-pic-preview" id="profilePreview">
          <div class="profile-pic-controls">
            <label>Profile Picture</label>
            <div class="file-input-wrapper">
              <input type="file" name="profile_pic" accept="image/jpeg,image/png,image/gif,image/webp"
                onchange="previewImage(event)">
            </div>
            <p class="hint">JPG, PNG, GIF, or WebP. Max 5 MB.</p>
          </div>
        </div>

        <div class="grid">
          <label>Name
            <input name="name" required maxlength="60" value="<?= htmlspecialchars($user['name']) ?>">
          </label>
          <label>Username
            <input value="<?= htmlspecialchars($user['username']) ?>" readonly>
          </label>
        </div>

        <label>Email
          <input type="email" name="email" required value="<?= htmlspecialchars($user['email']) ?>">
        </label>

        <label>Member since
          <input value="<?= htmlspecialchars($user['created_at']) ?>" readonly>
        </label>

        <hr class="divider">

        <label>Current password <span style="color:#b3261e">*</span>
          <input type="password" name="current_password" required placeholder="Required to save changes">
        </label>

        <div class="grid">
          <label>New password
            <input type="password" name="new_password" minlength="8" placeholder="Leave blank to keep current">
          </label>
          <label>Confirm new password
            <input type="password" name="confirm_password" minlength="8">
          </label>
        </div>
        <p class="hint">Fill in "New password" only if you want to change it.</p>

        <button type="submit">Save changes</button>
      </form>
    </div>
  </div>

  <script>
    function previewImage(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          document.getElementById('profilePreview').src = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    }
  </script>

</body>

</html>