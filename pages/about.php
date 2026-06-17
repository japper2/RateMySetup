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
  <title>About us</title>
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
            <h1>About us</h1>
            <p>We are 2 Deltion students that made this website for our school project.</p>
            </div>
          </div>
        </section>



  <script src="../js/nav.js"></script>
  <script src="../js/setups.js"></script>
</body>

</html>