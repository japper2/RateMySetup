<?php
require_once "../api/auth.php";
$navLoggedIn = currentUserId() !== null;
$navName = $_SESSION["name"] ?? "";
$navProfileImage = $_SESSION["profile_image"] ?? "";
?>
<!doctype html>
<html lang="nl">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Setup</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body class="upload-page" data-loggedin="<?= $navLoggedIn ? 'true' : 'false' ?>" data-username="<?= htmlspecialchars($navName) ?>" data-profileimage="<?= htmlspecialchars($navProfileImage) ?>">
  <header>
    <nav id="navbar"></nav>
  </header>

  <main class="form-page">
    <form id="uploadForm" class="panel form-card" enctype="multipart/form-data">
      <h1>Add your setup</h1>

      <label class="field">
        Title
        <input type="text" name="title" placeholder="Setup title" required>
      </label>

      <label class="field">
        Category
        <select name="category">
          <option>Gaming</option>
          <option>Productivity</option>
          <option>Minimal</option>
          <option>Creative</option>
          <option>Cozy</option>
        </select>
      </label>

      <div class="field">
        <span>Image</span>

        <label class="file-picker" for="setupImageInput">
          <span>
            <strong>Choose image</strong>
            <small id="selectedSetupImageName">PNG, JPG or WebP</small>
          </span>
        </label>

        <input
          id="setupImageInput"
          class="visually-hidden-file"
          type="file"
          name="image"
          accept="image/*"
          required
        >
      </div>

      <label class="field">
        Description
        <textarea name="description" rows="5" placeholder="Describe your setup" required></textarea>
      </label>

      <button class="btn primary" type="submit">Upload Setup</button>
    </form>
  </main>

  <script src="../js/nav.js"></script>

  <script>
    const setupImageInput = document.getElementById("setupImageInput");
    const selectedSetupImageName = document.getElementById("selectedSetupImageName");

    setupImageInput.addEventListener("change", function () {
      const file = this.files[0];
      selectedSetupImageName.textContent = file ? file.name : "PNG, JPG or WebP";
    });

    document.getElementById("uploadForm").addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      const response = await fetch("../api/upload_setup.php", {
        method: "POST",
        body: formData
      });

      const data = await response.json();

      if (response.status === 401) {
        alert(data.message);
        window.location.href = "login.php";
        return;
      }

      if (data.success) {
        window.location.href = "home.php";
      } else {
        alert(data.message);
      }
    });
  </script>
</body>

</html>
