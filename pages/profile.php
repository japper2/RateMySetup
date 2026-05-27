<?php
require "../api/db.php";
require "../api/auth.php";

$userId = currentUserId();

if (!$userId) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT name, email, profile_image FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php");
    exit;
}

$name = $user["name"] ?? "User";
$email = $user["email"] ?? "";
$profileImage = $user["profile_image"] ?? null;
$navLoggedIn = true;
$navName = $name;
$navProfileImage = $profileImage ?? "";
?>
<!doctype html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body data-loggedin="true" data-username="<?= htmlspecialchars($navName) ?>" data-profileimage="<?= htmlspecialchars($navProfileImage) ?>">
  <header>
    <nav id="navbar"></nav>
  </header>

  <main class="form-page">
    <section class="panel form-card profile-card">
      <?php if ($profileImage): ?>
        <div class="profile-avatar profile-avatar-image" style="background-image:url('../<?= htmlspecialchars($profileImage) ?>')"></div>
      <?php else: ?>
        <div class="profile-avatar">
          <?= htmlspecialchars(strtoupper(substr($name, 0, 1))) ?>
        </div>
      <?php endif; ?>

      <h1>Your profile</h1>

      <p class="desc">
        Logged in as <strong><?= htmlspecialchars($name) ?></strong><br>
        <?= htmlspecialchars($email) ?>
      </p>

      <form id="profileImageForm" class="profile-upload-form" enctype="multipart/form-data">
        <p class="profile-upload-title">Profile picture</p>

        <label class="file-picker" for="profileImageInput">
          <span>
            <strong>Choose image</strong>
            <small id="selectedFileName">PNG, JPG or WebP</small>
          </span>
        </label>

        <input
          id="profileImageInput"
          class="visually-hidden-file"
          type="file"
          name="profile_image"
          accept="image/*"
          required
        >

        <button class="btn primary" type="submit">Save profile picture</button>
      </form>

      <div class="profile-actions">
        <a class="btn primary" href="upload.php">+ Add Setup</a>
        <a class="btn" href="../api/logout.php">Log out</a>
      </div>
    </section>
  </main>

  <script src="../js/nav.js"></script>

  <script>
    const imageInput = document.getElementById('profileImageInput');
    const selectedFileName = document.getElementById('selectedFileName');
    const profileAvatar = document.querySelector('.profile-avatar');

    imageInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      selectedFileName.textContent = file.name;

      if (profileAvatar) {
        profileAvatar.textContent = '';
        profileAvatar.style.backgroundImage = `url('${URL.createObjectURL(file)}')`;
        profileAvatar.classList.add('profile-avatar-image');
      }
    });

    document.getElementById('profileImageForm').addEventListener('submit', async function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      const response = await fetch('../api/upload_profile_picture.php', {
        method: 'POST',
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        window.location.reload();
      } else {
        alert(data.message || 'Could not save profile picture');
      }
    });
  </script>
</body>

</html>
