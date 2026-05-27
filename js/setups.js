const state = {
  loggedIn: false,
  userName: null,
  order: 'latest',
  category: ''
};

function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

function imageSrc(path) {
  if (!path) return '../assets/setup-preview.png';
  return path.startsWith('uploads/') || path.startsWith('assets/') ? `../${path}` : `../${path}`;
}

function userAvatarMarkup(name, imagePath, className = 'user-avatar') {
  if (imagePath) {
    return `<span class="${className} avatar-image" style="background-image:url('${escapeHtml(imageSrc(imagePath))}')"></span>`;
  }

  const initial = String(name || 'U').charAt(0).toUpperCase();
  return `<span class="${className}">${escapeHtml(initial)}</span>`;
}

async function apiPost(url, data) {
  const formData = new FormData();
  Object.entries(data).forEach(([key, value]) => formData.append(key, value));

  const response = await fetch(url, {
    method: 'POST',
    body: formData
  });

  const result = await response.json();

  if (response.status === 401) {
    alert(result.message || 'You must be logged in first');
    window.location.href = 'login.php';
    return null;
  }

  if (!result.success) {
    alert(result.message || 'Something went wrong');
    return null;
  }

  return result;
}

async function loadSession() {
  try {
    const response = await fetch('../api/me.php');
    const data = await response.json();
    state.loggedIn = Boolean(data.logged_in);
    state.userName = data.name || null;
  } catch (_) {
    state.loggedIn = false;
  }
}

async function loadSetups(order = state.order, options = {}) {
  state.order = order;

  if (typeof options.category === 'string') {
    state.category = options.category;
  }

  const list = document.querySelector('[data-setups-list]');
  if (!list) return;

  const scrollY = window.scrollY;

  if (!options.preserveScroll) {
    list.innerHTML = '<p class="desc">Loading setups...</p>';
  }

  const params = new URLSearchParams({ order: state.order });
  if (state.category) params.set('category', state.category);

  const response = await fetch(`../api/get_setups.php?${params.toString()}`);
  const data = await response.json();

  if (!data.success) {
    list.innerHTML = '<p class="desc">Could not load setups.</p>';
    return;
  }

  if (!data.setups.length) {
    const label = state.category ? ` in ${escapeHtml(state.category)}` : '';
    list.innerHTML = `<p class="desc">No setups found${label}.</p>`;
    return;
  }

  list.innerHTML = data.setups.map(renderSetupCard).join('');

  if (options.reopenComments) {
    const panel = document.getElementById(`comments-${options.reopenComments}`);
    if (panel) {
      panel.hidden = false;
      await loadComments(options.reopenComments);
    }
  }

  if (options.preserveScroll) {
    requestAnimationFrame(() => window.scrollTo(0, scrollY));
  }
}

function renderSetupCard(setup) {
  const rating = Number(setup.average_rating || 0).toFixed(1);
  const myRating = setup.my_rating ? Number(setup.my_rating) : 0;
  const category = setup.category || 'General';
  const likedClass = Number(setup.liked_by_me) ? 'liked' : '';

  return `
    <article class="setup-card" data-setup-id="${setup.id}">
      <img class="card-img" src="${escapeHtml(imageSrc(setup.image_path))}" alt="${escapeHtml(setup.title)}">
      <div class="card-body">
        <div class="card-meta">
          ${userAvatarMarkup(setup.user_name, setup.user_profile_image)}
          <strong>${escapeHtml(setup.user_name)}</strong>
          <span>${escapeHtml(new Date(setup.created_at).toLocaleDateString())}</span>
        </div>

        <div class="card-title-row">
          <h3>${escapeHtml(setup.title)}</h3>
          <button class="tag tag-button" onclick="filterCategory('${escapeHtml(category)}')">#${escapeHtml(category.toLowerCase())}</button>
        </div>

        <p class="desc">${escapeHtml(setup.description)}</p>

        <div class="stats action-stats">
          <button class="stat-button" onclick="toggleRatingPanel(${setup.id})">★ ${rating}</button>
          <button class="stat-button ${likedClass}" onclick="likeSetup(${setup.id})">♡ ${setup.likes_count}</button>
          <button class="stat-button" onclick="toggleComments(${setup.id})">◯ ${setup.comments_count}</button>
        </div>

        <div class="rating-panel" id="rating-${setup.id}" hidden>
          ${[1, 2, 3, 4, 5].map(score => `
            <button class="rate-star ${score <= myRating ? 'selected' : ''}" onclick="rateSetup(${setup.id}, ${score})">★</button>
          `).join('')}
        </div>

        <div class="comments-panel" id="comments-${setup.id}" hidden>
          <div class="comments-list" data-comments-for="${setup.id}"></div>
          <form class="comment-form" onsubmit="submitComment(event, ${setup.id})">
            <input type="text" name="comment" placeholder="Write a comment..." required>
            <button class="btn primary small" type="submit">Post</button>
          </form>
        </div>
      </div>
    </article>
  `;
}

function toggleRatingPanel(setupId) {
  const panel = document.getElementById(`rating-${setupId}`);
  panel.hidden = !panel.hidden;
}

async function rateSetup(setupId, score) {
  const result = await apiPost('../api/rate_setup.php', { setup_id: setupId, score });
  if (result) await loadSetups(state.order, { preserveScroll: true });
}

async function likeSetup(setupId) {
  const result = await apiPost('../api/like_setup.php', { setup_id: setupId });
  if (result) await loadSetups(state.order, { preserveScroll: true });
}

async function toggleComments(setupId) {
  const panel = document.getElementById(`comments-${setupId}`);
  panel.hidden = !panel.hidden;

  if (!panel.hidden) {
    await loadComments(setupId);
  }
}

async function loadComments(setupId) {
  const target = document.querySelector(`[data-comments-for="${setupId}"]`);
  if (!target) return;

  target.innerHTML = '<p class="desc">Loading comments...</p>';

  const response = await fetch(`../api/get_comments.php?setup_id=${setupId}`);
  const data = await response.json();

  if (!data.success || !data.comments.length) {
    target.innerHTML = '<p class="desc">No comments yet.</p>';
    return;
  }

  target.innerHTML = data.comments.map((comment) => `
    <div class="comment">
      <strong>${escapeHtml(comment.user_name)}</strong>
      <p>${escapeHtml(comment.comment)}</p>
    </div>
  `).join('');
}

async function submitComment(event, setupId) {
  event.preventDefault();

  const input = event.target.elements.comment;
  const result = await apiPost('../api/add_comment.php', {
    setup_id: setupId,
    comment: input.value
  });

  if (result) {
    input.value = '';
    await loadSetups(state.order, {
      preserveScroll: true,
      reopenComments: setupId
    });
  }
}

function setActiveCategory(category) {
  document.querySelectorAll('[data-category]').forEach((button) => {
    button.classList.toggle('active', button.dataset.category === category);
  });
}

function filterCategory(category) {
  state.category = category;
  setActiveCategory(category);
  loadSetups(state.order, { preserveScroll: true, category });
}

function clearCategory() {
  state.category = '';
  setActiveCategory('');
  loadSetups(state.order, { preserveScroll: true, category: '' });
}

document.addEventListener('DOMContentLoaded', async () => {
  await loadSession();
  await loadSetups();

  document.querySelectorAll('[data-order]').forEach((button) => {
    button.addEventListener('click', () => {
      document.querySelectorAll('[data-order]').forEach((btn) => btn.classList.remove('active'));
      button.classList.add('active');
      loadSetups(button.dataset.order, { preserveScroll: true });
    });
  });

  document.querySelectorAll('[data-category]').forEach((button) => {
    button.addEventListener('click', () => {
      filterCategory(button.dataset.category);
    });
  });

  const clearButton = document.querySelector('[data-clear-category]');
  if (clearButton) clearButton.addEventListener('click', clearCategory);
});
