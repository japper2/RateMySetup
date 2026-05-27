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
  <title>Rate My Setup</title>
  <link rel="stylesheet" href="../style.css">
</head>

<body data-loggedin="<?= $navLoggedIn ? 'true' : 'false' ?>" data-username="<?= htmlspecialchars($navName) ?>" data-profileimage="<?= htmlspecialchars($navProfileImage) ?>">
  <header>
    <nav id="navbar"></nav>
  </header>

  <main class="page container">
    <section class="layout">
      <div>
        <section class="hero">
          <div class="hero-content">
            <h1>Show off your setup.<br>Get <span>rated</span> by the community.</h1>
            <p>Join creators sharing their desk, gaming and productivity setups. Rate, like and comment on real uploads.</p>
            <div class="hero-actions">
              <a class="btn primary" href="upload.php">↑ Add Your Setup</a>
              <a class="btn" href="explore.php">Explore Setups</a>
            </div>
          </div>
        </section>

        <section class="panel content-card">
          <div class="section-head">
            <h2>Latest Setups</h2>
            <div class="tabs">
              <button class="tab active" data-order="latest">Latest</button>
              <button class="tab" data-order="rated">Top Rated</button>
              <button class="tab" data-order="liked">Most Liked</button>
            </div>
          </div>

          <div class="cards" data-setups-list></div>

          <a class="btn more" href="explore.php">View More Setups</a>
        </section>
      </div>

      <aside class="side">
        <section class="panel side-card">
          <h2>How it works</h2>
          <div class="category-list">
            <div class="category"><span>1. Register or log in</span></div>
            <div class="category"><span>2. Upload your setup</span></div>
            <div class="category"><span>3. Rate, like and comment</span></div>
          </div>
        </section>

        <section class="panel side-card" style="margin-top:20px">
          <h2>Categories</h2>
          <div class="category-list">
            <button class="category category-button active" type="button" data-category=""><span>All categories</span></button>
            <button class="category category-button" type="button" data-category="Gaming"><span>🎮 Gaming</span></button>
            <button class="category category-button" type="button" data-category="Productivity"><span>🖥️ Productivity</span></button>
            <button class="category category-button" type="button" data-category="Minimal"><span>▣ Minimal</span></button>
            <button class="category category-button" type="button" data-category="Creative"><span>✒ Creative</span></button>
            <button class="category category-button" type="button" data-category="Cozy"><span>⌂ Cozy</span></button>
          </div>
        </section>
      </aside>
    </section>
  </main>

  <footer class="footer">
    <div class="container footer-inner">
      <strong>RATE MY SETUP</strong>
      <div class="footer-links">
        <a>About</a>
        <a>Contact</a>
        <a>Privacy Policy</a>
        <a>Terms of Service</a>
      </div>
    </div>
  </footer>

  <script src="../js/nav.js"></script>
  <script src="../js/setups.js"></script>
</body>

</html>
