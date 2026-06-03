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
  <title>Terms of Service</title>
  <link rel="stylesheet" href="/github/RateMySetup/style.css">
</head>

<body data-loggedin="<?= $navLoggedIn ? 'true' : 'false' ?>" data-username="<?= htmlspecialchars($navName) ?>" data-profileimage="<?= htmlspecialchars($navProfileImage) ?>">
  <header>
    <nav id="navbar"></nav>
  </header>

  <main class="page container">
    <section class="panel content-card">
      <div class="section-head">
          <div class="hero-content">
            <h1>Terms of Service</h1><br>
            <p>By using RateMySetup, you agree to follow these Terms of Service.
            Users are responsible for the content they upload.</p>
            <p>Do not post illegal, harmful, stolen, or spam content.
            We may remove content or suspend accounts at any time.</p>
            <p>You retain ownership of your uploads, but grant RateMySetup permission to display and share them on the platform.</p>
            <p>Do not attempt to hack, abuse, or disrupt the website.
            RateMySetup is provided “as is” without guarantees or warranties.</p>
            <p>We may update these terms occasionally. We will notify users of significant changes.</p>
            <p>Continued use of the platform means you accept these terms and any future updates.
            If you do not agree with these terms, please stop using RateMySetup.</p>
            </div>
          </div>
        </section>



  <script src="/github/RateMySetup/js/nav.js"></script>
  <script src="/github/RateMySetup/js/setups.js"></script>
</body>

</html>
