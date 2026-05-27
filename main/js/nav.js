(function () {
  const current = location.pathname.split("/").pop() || "home.html";
  let host = document.getElementById("navbar");
  if (!host) {
    host = document.createElement("nav");
    host.id = "navbar";
    document.body.insertBefore(host, document.body.firstChild);
  }

  const FALLBACK_MARKUP = `
<div class="navbar">
  <a class="nav-logo" href="home.html" aria-label="Rate My Setup">
    <span class="logo-mark"></span>
    <span class="logo-text">RATE MY SETUP</span>
  </a>
  <input type="checkbox" id="nav-toggle" />
  <label class="nav-burger" for="nav-toggle" aria-label="Toggle navigation">
    <span></span><span></span><span></span>
  </label>
  <div class="nav-links">
    <a href="home.html">Home</a>
    <a href="explore.html">Explore</a>
  </div>
  <div class="nav-actions">
    <input class="nav-search" placeholder="Search setups..." aria-label="Search setups" />
    <a class="btn upload" href="upload.html">+ Add Setup</a>
    <a class="btn login" href="login.html">Login</a>
  </div>
</div>`;

  function setActive() {
    const links = host.querySelectorAll(".nav-links a");
    links.forEach((a) => {
      if (a.getAttribute("href") === current) a.classList.add("active");
    });
  }

  const candidates = [
    "nav.html",
    "./nav.html",
    "../nav.html",
    "../../nav.html",
    "/nav.html",
  ];

  function tryFetchCandidates(list) {
    host.classList.add("loading");

    let chain = Promise.reject();
    for (const p of list) {
      chain = chain.catch(() =>
        fetch(p, { cache: "no-cache" }).then((r) => {
          if (!r.ok) throw new Error("HTTP " + r.status + " for " + p);
          return r.text().then((t) => ({ path: p, text: t }));
        }),
      );
    }

    return chain
      .then((res) => {
        host.innerHTML = res.text || FALLBACK_MARKUP;
        host.classList.remove("loading");
        setActive();
      })
      .catch((err) => {
        console.warn("Nav load failed from candidates, using fallback", err);
        host.classList.remove("loading");
        host.innerHTML = FALLBACK_MARKUP;
        setActive();
      });
  }

  tryFetchCandidates(candidates);
})();

function initSidebarToggle() {
  const btn = document.getElementById("bottomToggle");
  const sidebar = document.getElementById("sidebar");
  if (!btn || !sidebar) return;

  let isOpen = false;
  let isAnimating = false;

  if (location.hash === "#sidebar") {
    sidebar.classList.add("open-bottom");
    sidebar.setAttribute("aria-hidden", "false");
    document.documentElement.classList.add("sidebar-open");
    document.body.classList.add("sidebar-open");
    btn.setAttribute("aria-expanded", "true");
    isOpen = true;
  }

  function open() {
    if (isOpen || isAnimating) return;

    sidebar.classList.remove("closing-bottom");

    try {
      history.replaceState(null, "", "#sidebar");
    } catch (e) {}

    sidebar.classList.add("open-bottom");
    sidebar.setAttribute("aria-hidden", "false");

    document.documentElement.classList.add("sidebar-open");
    document.body.classList.add("sidebar-open");

    btn.setAttribute("aria-expanded", "true");
    isOpen = true;
  }

  function close() {
    if (!isOpen || isAnimating) return;
    isAnimating = true;

    sidebar.classList.add("closing-bottom");

    let done = false;
    const finish = () => {
      if (done) return;
      done = true;

      sidebar.classList.remove("open-bottom", "closing-bottom");
      sidebar.setAttribute("aria-hidden", "true");

      document.documentElement.classList.remove("sidebar-open");
      document.body.classList.remove("sidebar-open");

      btn.setAttribute("aria-expanded", "false");

      try {
        history.replaceState(null, "", location.pathname + location.search);
      } catch (e) {}

      isOpen = false;
      isAnimating = false;
    };

    sidebar.addEventListener("transitionend", function te(e) {
      if (e.propertyName !== "transform") return;
      sidebar.removeEventListener("transitionend", te);
      finish();
    });

    setTimeout(finish, 500);
  }

  btn.addEventListener("click", (e) => {
    e.preventDefault();
    e.stopPropagation();
    isOpen ? close() : open();
  });

  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") close();
  });

  document.addEventListener("click", (e) => {
    if (!isOpen) return;
    if (!sidebar.contains(e.target) && !btn.contains(e.target)) close();
  });
}

document.readyState === "loading"
  ? document.addEventListener("DOMContentLoaded", initSidebarToggle)
  : initSidebarToggle();

function updateNavHeight() {
  const navbar = document.querySelector(".navbar");
  if (!navbar) return;
  const apply = () => {
    const h = Math.round(navbar.getBoundingClientRect().height) || 72;
    document.documentElement.style.setProperty("--nav-height", h + "px");
  };

  apply();

  if (window.ResizeObserver) {
    const ro = new ResizeObserver(apply);
    ro.observe(navbar);
  } else {
    window.addEventListener("resize", apply);
    setInterval(apply, 1000);
  }

  const toggle = navbar.querySelector("#nav-toggle");
  if (toggle) toggle.addEventListener("change", () => setTimeout(apply, 260));
}

if (document.readyState === "loading") {
  document.addEventListener("DOMContentLoaded", updateNavHeight);
} else {
  updateNavHeight();
}
