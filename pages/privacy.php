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
  <title>Privacy Policy</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body data-loggedin="<?= $navLoggedIn ? 'true' : 'false' ?>" data-username="<?= htmlspecialchars($navName) ?>" data-profileimage="<?= htmlspecialchars($navProfileImage) ?>">
  <header>
    <nav id="navbar"></nav>
  </header>

  <main class="page container">
    <section class="panel content-card">
      <div class="section-head">
          <div class="hero-content">
<h1>Privacy Policy</h1><br>

<p><strong>Effective Date:</strong> June 1, 2026</p><br><br>

<p>
RateMySetup collects only the information needed to run the site: account data, uploads, comments, ratings, and basic technical details for security.
</p>

<p>
Public content such as posted setups, comments, ratings, and profile names may be visible to others. Do not share sensitive personal information.
</p>

<br><h2>Cookies and sessions</h2>
<p>
We use cookies and browser storage to keep you logged in and improve the site experience.
</p>

<br><h2>Your control</h2>
<p>
You can stop using the service any time. If you want your account removed, contact support.
</p>
      </div>
    </section>

  <script src="../js/nav.js"></script>
  <script src="../js/setups.js"></script>
</body>

</html>