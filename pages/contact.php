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
  <title>Contact</title>
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
            <h1>Contact</h1>
            <p>If you have any questions or feedback, feel free to reach out to us!</p>
            <p>You can contact us at info@ratemysetup.com</p>
            <p>our phone number is +31 6 12345678</p>
            </div>
          </div>
        </section>



  <script src="../js/nav.js"></script>
  <script src="../js/setups.js"></script>
</body>

</html>