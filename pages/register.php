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
  <title>Register</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body data-loggedin="<?= $navLoggedIn ? 'true' : 'false' ?>" data-username="<?= htmlspecialchars($navName) ?>" data-profileimage="<?= htmlspecialchars($navProfileImage) ?>">
  <header>
    <nav id="navbar"></nav>
  </header>

  <main class="form-page">
    <form id="registerForm" class="panel form-card">
      <h1>Create account</h1>

      <label class="field">
        Name
        <input type="text" name="name" placeholder="Your name" required>
      </label>

      <label class="field">
        Email
        <input type="email" name="email" placeholder="you@example.com" required>
      </label>

      <label class="field">
        Password
        <input type="password" name="password" placeholder="Password" required>
      </label>

      <button class="btn primary" type="submit">Register</button>

      <p class="desc" style="margin-top:16px">
        Already have an account? <a href="login.php">Log in here</a>.
      </p>
    </form>
  </main>

  <script src="../js/nav.js"></script>

  <script>
    document.getElementById("registerForm").addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      const response = await fetch("../api/register.php", {
        method: "POST",
        body: formData
      });

      const data = await response.json();

      if (data.success) {
        window.location.href = "home.php";
      } else {
        alert(data.message);
      }
    });
  </script>
</body>

</html>
