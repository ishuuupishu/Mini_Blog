// ---- Simple state ----
let CURRENT_USER = null;

// DOM elements
const authArea   = document.getElementById('auth-area');
const createArea = document.getElementById('create-area');
const postsBox   = document.getElementById('posts');
const createForm = document.getElementById('createForm');

// Helper: POST form encoded
async function postForm(url, data) {
  const body = new URLSearchParams();
  Object.entries(data).forEach(([k, v]) => body.append(k, v));
  const res = await fetch(url, { method: 'POST', body });
  return res.json();
}

// Load current user
async function loadUser() {
  const res = await fetch('api/auth/whoami.php');
  const json = await res.json();
  CURRENT_USER = json?.data?.user || null;
  renderAuth();
}

function renderAuth() {
  if (CURRENT_USER) {
    authArea.innerHTML = `
      <div class="logged">
        <span>👤 ${CURRENT_USER.username}</span>
        <button id="logoutBtn">Logout</button>
      </div>
    `;
    document.getElementById('logoutBtn').onclick = logout;
    createArea.style.display = 'block';
  } else {
    authArea.innerHTML = `
      <div class="auth-forms">
        <form id="loginForm">
          <input name="username" placeholder="Username" required />
          <input name="password" type="password" placeholder="Password" required />
          <button type="submit">Login</button>
        </form>
        <form id="registerForm">
          <input name="username" placeholder="New Username" required />
          <input name="password" type="password" placeholder="New Password" required />
          <button type="submit">Register</button>
        </form>
      </div>
    `;
    document.getElementById('loginForm').onsubmit = async (e) => {
      e.preventDefault();
      const f = e.target;
      const resp = await postForm('api/auth/login.php', {
        username: f.username.value,
        password: f.password.value
      });
      alert(resp.data?.message || (resp.success ? 'Logged in' : 'Login failed'));
      await loadUser();
      await loadPosts();
    };
    document.getElementById('registerForm').onsubmit = async (e) => {
      e.preventDefault();
      const f = e.target;
      const resp = await postForm('api/auth/register.php', {
        username: f.username.value,
        password: f.password.value
      });
      alert(resp.data?.message || (resp.success ? 'Registered' : 'Register failed'));
    };
    createArea.style.display = 'none';
  }
}

async function logout() {
  await fetch('api/auth/logout.php');
  CURRENT_USER = null;
  renderAuth();
  await loadPosts();
}

// Load & render posts
async function loadPosts() {
  postsBox.innerHTML = '<p>Loading...</p>';
  const res = await fetch('api/posts/read.php', { method: 'POST' });
  const json = await res.json();
  const posts = json?.data?.posts || [];
  if (posts.length === 0) {
    postsBox.innerHTML = '<p>No posts yet.</p>';
    return;
  }
  postsBox.innerHTML = posts.map(p => `
    <article class="card">
      <div class="card-header">
        <h3>${p.title}</h3>
        <small>by ${p.author} · ${new Date(p.created_at).toLocaleString()}</small>
      </div>
      <div class="card-body">${p.body}</div>
      ${p.can_edit ? `
      <div class="card-actions">
        <button onclick="onEdit(${p.id})">Edit</button>
        <button onclick="onDelete(${p.id})">Delete</button>
      </div>` : ''}
    </article>
  `).join('');
}

// Create post
createForm.onsubmit = async (e) => {
  e.preventDefault();
  const title = document.getElementById('title').value.trim();
  const body  = document.getElementById('body').value.trim();
  if (!title || !body) return alert('Title and content are required.');
  const resp = await postForm('api/posts/create.php', { title, body });
  if (resp.success) {
    createForm.reset();
    await loadPosts();
  } else {
    alert(resp.data?.message || 'Create failed');
  }
};

// Edit post (simple prompt UI to keep code minimal)
async function onEdit(id) {
  const title = prompt('New title:');
  if (title === null) return; // cancel
  const body = prompt('New content:');
  if (body === null) return;
  const resp = await postForm('api/posts/update.php', { id, title, body });
  alert(resp.data?.message || (resp.success ? 'Updated' : 'Update failed'));
  await loadPosts();
}

// Delete post
async function onDelete(id) {
  if (!confirm('Delete this post?')) return;
  const resp = await postForm('api/posts/delete.php', { id });
  alert(resp.data?.message || (resp.success ? 'Deleted' : 'Delete failed'));
  await loadPosts();
}

// Init
window.addEventListener('DOMContentLoaded', async () => {
  await loadUser();
  await loadPosts();
});
