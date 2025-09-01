let CURRENT_USER = null;

const authArea   = document.getElementById('auth-area');
const createArea = document.getElementById('create-area');
const postsBox   = document.getElementById('posts');
const createForm = document.getElementById('createForm');
const refreshBtn = document.getElementById('refreshBtn');



// Helpers
async function postForm(url, formDataOrObj, isMultipart = false) {
  let options = { method: 'POST' };
  if (isMultipart) {
    options.body = formDataOrObj;
  } else {
    const body = new URLSearchParams();
    Object.entries(formDataOrObj).forEach(([k, v]) => body.append(k, v));
    options.body = body;
  }
  const res = await fetch(url, options);
  return res.json();
}

async function loadUser() {
  const res = await fetch('api/auth/whoami.php');
  const json = await res.json();
  CURRENT_USER = json?.data?.user || null;
  renderAuth();
}

function renderAuth() {
  if (CURRENT_USER) {
    authArea.innerHTML = `
      <div class="d-flex align-items-center gap-2">
        <span class="text-white-50 small">Hi,</span>
        <span class="badge bg-light text-dark">${CURRENT_USER.username}</span>
        <button class="btn btn-sm btn-outline-light" id="logoutBtn">Logout</button>
      </div>
    `;
    document.getElementById('logoutBtn').onclick = logout;
    createArea.style.display = 'block';
  } else {
    authArea.innerHTML = `
      <div class="d-flex gap-2">
        <form id="loginForm" class="d-flex gap-2">
          <input name="username" class="form-control form-control-sm" placeholder="Username" required />
          <input name="password" type="password" class="form-control form-control-sm" placeholder="Password" required />
          <button class="btn btn-sm btn-light" type="submit">Login</button>
        </form>
        <form id="registerForm" class="d-flex gap-2">
          <input name="username" class="form-control form-control-sm" placeholder="New Username" required />
          <input name="password" type="password" class="form-control form-control-sm" placeholder="New Password" required />
          <button class="btn btn-sm btn-outline-light" type="submit">Register</button>
        </form>
      </div>
    `;
    document.getElementById('loginForm').onsubmit = async (e) => {
      e.preventDefault();
      const f = e.target;
      const resp = await postForm('api/auth/login.php', { username: f.username.value, password: f.password.value });
      alert(resp.data?.message || (resp.success ? 'Logged in' : 'Login failed'));
      await loadUser();
      await loadPosts();
    };
    document.getElementById('registerForm').onsubmit = async (e) => {
      e.preventDefault();
      const f = e.target;
      const resp = await postForm('api/auth/register.php', { username: f.username.value, password: f.password.value });
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

function renderMedia(file) {
  if (!file || !file.path) return '';
  const type = (file.type || '').toLowerCase();
  if (type.startsWith('image/')) {
    return `<img src="${file.path}" alt="" class="img-fluid rounded">`;
  }
  if (type.startsWith('video/')) {
    return `<video src="${file.path}" class="w-100 rounded" controls></video>`;
  }
 
  const name = file.path.split('/').pop();
  return `<a class="btn btn-sm btn-outline-secondary" href="${file.path}" target="_blank" rel="noopener">Download ${name}</a>`;
}

async function loadPosts() {
  postsBox.innerHTML = `<div class="text-center text-muted py-4">Loading…</div>`;
  const res = await fetch('api/posts/read.php', { method: 'POST' });
  const json = await res.json();
  const posts = json?.data?.posts || [];
  if (posts.length === 0) {
    postsBox.innerHTML = `<div class="text-center text-muted py-4">No posts yet.</div>`;
    return;
  }
  postsBox.innerHTML = posts.map(p => `
    <div class="card shadow-sm">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <h5 class="card-title mb-1">${p.title}</h5>
            <div class="text-muted small mb-2">by <strong>${p.author}</strong> · ${new Date(p.created_at).toLocaleString()}</div>
          </div>
          ${p.can_edit ? `
            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-primary" onclick="onEdit(${p.id})">Edit</button>
              <button class="btn btn-outline-danger" onclick="onDelete(${p.id})">Delete</button>
            </div>` : ''}
        </div>
        <p class="card-text">${p.body}</p>
        <div class="mt-2">${renderMedia(p.file)}</div>
      </div>
    </div>
  `).join('');
}

createForm.onsubmit = async (e) => {
  e.preventDefault();
  const fd = new FormData(createForm);
  const resp = await postForm('api/posts/create.php', fd, true);
  alert(resp.data?.message || (resp.success ? 'Created' : 'Create failed'));
  if (resp.success) {
    createForm.reset();
    await loadPosts();
  }
};


async function onEdit(id) {
  const newTitle = prompt('New title:');
  if (newTitle === null) return;
  const newBody = prompt('New content:');
  if (newBody === null) return;

  const replace = confirm('Replace attachment? Click OK to choose a new file, or Cancel to keep existing.');
  if (!replace) {
    const resp = await postForm('api/posts/update.php', { id, title: newTitle, body: newBody });
    alert(resp.data?.message || (resp.success ? 'Updated' : 'Update failed'));
    return loadPosts();
  }

  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*,video/mp4,video/webm,application/pdf';
  input.onchange = async () => {
    const fd = new FormData();
    fd.append('id', id);
    fd.append('title', newTitle);
    fd.append('body', newBody);
    if (input.files[0]) fd.append('file', input.files[0]);
    const resp = await postForm('api/posts/update.php', fd, true);
    alert(resp.data?.message || (resp.success ? 'Updated' : 'Update failed'));
    await loadPosts();
  };
  input.click();
}

async function onDelete(id) {
  if (!confirm('Delete this post?')) return;
  const resp = await postForm('api/posts/delete.php', { id });
  alert(resp.data?.message || (resp.success ? 'Deleted' : 'Delete failed'));
  await loadPosts();
}

window.addEventListener('DOMContentLoaded', async () => {
  refreshBtn.onclick = loadPosts;
  await loadUser();
  await loadPosts();
});
