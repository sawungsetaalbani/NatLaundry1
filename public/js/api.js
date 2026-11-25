const API_URL = '/api';

const Auth = {
  getToken: () => localStorage.getItem('token'),
  isLoggedIn: () => !!localStorage.getItem('token'),
  setSession(token, pengguna) {
    localStorage.setItem('token', token);
    localStorage.setItem('pengguna', JSON.stringify(pengguna));
  },
  getUser() {
    try { return JSON.parse(localStorage.getItem('pengguna')); } catch { return null; }
  },
  clear() {
    localStorage.removeItem('token');
    localStorage.removeItem('pengguna');
  },
};

async function api(path, { method = 'GET', body = null } = {}) {
  const headers = { Accept: 'application/json' };
  if (body) headers['Content-Type'] = 'application/json';
  const token = Auth.getToken();
  if (token) headers['Authorization'] = 'Bearer ' + token;

  const res = await fetch(API_URL + path, {
    method,
    headers,
    body: body ? JSON.stringify(body) : null,
  });

  let json = null;
  try { json = await res.json(); } catch {}

  if (!res.ok) {
    if (res.status === 401 && token) {
      Auth.clear();
      if (location.pathname.startsWith('/admin')) location.href = '/login';
    }
    const err = new Error(json?.message || 'Terjadi kesalahan.');
    err.status = res.status;
    err.errors = json?.errors;
    throw err;
  }
  return json;
}

// Unduh file (PDF). Token hanya dikirim kalau ada (halaman publik tidak butuh)
async function downloadFile(path, filename) {
  const headers = { Accept: 'application/json, application/pdf' };
  const token = Auth.getToken();
  if (token) headers['Authorization'] = 'Bearer ' + token;

  const res = await fetch(API_URL + path, { headers });
  if (!res.ok) {
    let msg = 'Gagal mengunduh file.';
    try { msg = (await res.json()).message || msg; } catch {}
    throw new Error(msg);
  }
  const url = URL.createObjectURL(await res.blob());
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  document.body.appendChild(a);
  a.click();
  a.remove();
  URL.revokeObjectURL(url);
}

const rupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');

const esc = (s) =>
  String(s ?? '').replace(/[&<>"']/g, (c) => (
    { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]
  ));

async function logout() {
  try { await api('/auth/logout', { method: 'POST' }); } catch {}
  Auth.clear();
  location.href = '/';
}

// Navbar: tombol Login untuk pengunjung DIHAPUS.
// Hanya admin yang sedang login yang melihat menu Admin + Logout.
function renderNavAuth() {
  const el = document.getElementById('nav-auth');
  if (!el) return;
  const user = Auth.getUser();
  if (Auth.isLoggedIn() && user?.role === 'admin') {
    el.innerHTML = `
      <a href="/admin" class="text-xs hover:text-brand-light">Admin</a>
      <button onclick="logout()" class="bg-brand-light text-white text-xs px-4 py-1.5 rounded-lg hover:opacity-90">Logout</button>`;
  } else {
    el.innerHTML = '';
  }
}
document.addEventListener('DOMContentLoaded', renderNavAuth);