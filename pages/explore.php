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
  <title>Explore</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body data-loggedin="<?= $navLoggedIn ? 'true' : 'false' ?>" data-username="<?= htmlspecialchars($navName) ?>" data-profileimage="<?= htmlspecialchars($navProfileImage) ?>">
  <header>
    <nav id="navbar"></nav>
  </header>

  <main class="page container">
    <section class="panel content-card">
      <div class="section-head">
        <h1>Explore Setups</h1>
        <div class="tabs">
          <button class="tab active" data-order="latest">Latest</button>
          <button class="tab" data-order="rated">Top Rated</button>
          <button class="tab" data-order="liked">Most Liked</button>
        </div>
      </div>

      <div class="category-filter-row">
        <button class="tab active" type="button" data-category="">All</button>
        <button class="tab" type="button" data-category="Gaming">Gaming</button>
        <button class="tab" type="button" data-category="Productivity">Productivity</button>
        <button class="tab" type="button" data-category="Minimal">Minimal</button>
        <button class="tab" type="button" data-category="Creative">Creative</button>
        <button class="tab" type="button" data-category="Cozy">Cozy</button>
      </div>

      <div class="cards" data-setups-list></div>
    </section>
  </main>

  <script src="../js/nav.js"></script>
  <script src="../js/setups.js"></script>
</body>

</html>
