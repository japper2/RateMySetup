(function () {
  const BASE_URL = '/github/RateMySetup';

  const pathName = location.pathname.replace(BASE_URL + '/', '').replace(/^\//, '');
  const current = pathName || 'home';

  const navbar = document.getElementById('navbar');
  if (!navbar) return;

  const body = document.body;
  const loggedIn = body.dataset.loggedin === 'true';
  const userName = body.dataset.username || 'User';
  const profileImage = body.dataset.profileimage || '';

  function imageSrc(path) {
    if (!path) return '';
    if (path.startsWith('http')) return path;
    return BASE_URL + '/' + path.replace(/^\.\.\//, '').replace(/^\//, '');
  }

  function active(page) {
    return current === page ? 'active' : '';
  }

  const authLinks = loggedIn
    ? ''
    : `
      <a href="${BASE_URL}/login" class="${active('login')}">Login</a>
      <a href="${BASE_URL}/register" class="${active('register')}">Register</a>
    `;

  const profileAvatar = profileImage
    ? `<a class="avatar avatar-photo" href="${BASE_URL}/profile" aria-label="Profile" title="Profile: ${userName}" style="background-image:url('${imageSrc(profileImage)}')"></a>`
    : `<a class="avatar avatar-initial" href="${BASE_URL}/profile" aria-label="Profile" title="Profile: ${userName}">${String(userName).charAt(0).toUpperCase()}</a>`;

  const loggedInActions = loggedIn
    ? `
      <a class="btn primary" href="${BASE_URL}/upload">+ Add Setup</a>
      ${profileAvatar}
    `
    : '';

  navbar.innerHTML = `
    <div class="navbar">
      <div class="container nav-inner">
        <a class="logo" href="${BASE_URL}/home"><span class="logo-mark"></span><span>RATE <strong>MY</strong> SETUP</span></a>
        <div class="nav-links" id="navLinks">
          <a href="${BASE_URL}/home" class="${active('home')}">Home</a>
          <a href="${BASE_URL}/explore" class="${active('explore')}">Explore</a>
          ${authLinks}
        </div>
        <div class="nav-actions" id="navActions">
          <input class="search" type="search" placeholder="Search setups..." />
          ${loggedInActions}
        </div>
      </div>
    </div>`;

  const searchInput = navbar.querySelector('.search');

  if (searchInput) {
    const query = new URLSearchParams(window.location.search).get('q') || '';
    searchInput.value = query;

    searchInput.addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
        event.preventDefault();
        const value = searchInput.value.trim();

        if (value) {
          window.location.href = BASE_URL + '/explore?q=' + encodeURIComponent(value);
        } else {
          window.location.href = BASE_URL + '/explore';
        }
      }
    });
  }
})();
