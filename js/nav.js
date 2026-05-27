(function () {
  const current = location.pathname.split('/').pop() || 'home.php';
  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const body = document.body;
  const loggedIn = body.dataset.loggedin === 'true';
  const userName = body.dataset.username || 'User';
  const profileImage = body.dataset.profileimage || '';

  function imageSrc(path) {
    if (!path) return '';
    return path.startsWith('../') ? path : `../${path}`;
  }

  function active(page) {
    return current === page ? 'active' : '';
  }

  const authLinks = loggedIn
    ? ''
    : `
      <a href="login.php" class="${active('login.php')}">Login</a>
      <a href="register.php" class="${active('register.php')}">Register</a>
    `;

  const profileAvatar = profileImage
    ? `<a class="avatar avatar-photo" href="profile.php" aria-label="Profile" title="Profile: ${userName}" style="background-image:url('${imageSrc(profileImage)}')"></a>`
    : `<a class="avatar avatar-initial" href="profile.php" aria-label="Profile" title="Profile: ${userName}">${String(userName).charAt(0).toUpperCase()}</a>`;

  const loggedInActions = loggedIn
    ? `
      <a class="btn primary" href="upload.php">+ Add Setup</a>
      ${profileAvatar}
    `
    : '';

  navbar.innerHTML = `
    <div class="navbar">
      <div class="container nav-inner">
        <a class="logo" href="home.php"><span class="logo-mark"></span><span>RATE <strong>MY</strong> SETUP</span></a>
        <div class="nav-links" id="navLinks">
          <a href="home.php" class="${active('home.php')}">Home</a>
          <a href="explore.php" class="${active('explore.php')}">Explore</a>
          ${authLinks}
        </div>
        <div class="nav-actions" id="navActions">
          <input class="search" type="search" placeholder="Search setups..." />
          ${loggedInActions}
        </div>
      </div>
    </div>`;
})();
