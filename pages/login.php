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
  <title>Login</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body data-loggedin="<?= $navLoggedIn ? 'true' : 'false' ?>" data-username="<?= htmlspecialchars($navName) ?>" data-profileimage="<?= htmlspecialchars($navProfileImage) ?>">
  <header>
    <nav id="navbar"></nav>
  </header>

  <main class="form-page">
    <form id="loginForm" class="panel form-card">
      <h1>Log in</h1>

      <label class="field">
        Email
        <input type="email" name="email" placeholder="you@example.com" required>
      </label>

      <label class="field">
        Password
        <input type="password" name="password" placeholder="Password" required>
      </label>

      <button class="btn primary" type="submit">Log in</button>

      <p class="desc" style="margin-top:16px">
        No account? <a href="register.php">Register here</a>.
      </p>
    </form>
  </main>

  <script src="../js/nav.js"></script>

  <script>
    document.getElementById("loginForm").addEventListener("submit", async function (e) {
      e.preventDefault();

      const formData = new FormData(this);

      const response = await fetch("../api/login.php", {
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
