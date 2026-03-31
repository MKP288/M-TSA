/* js/auth.js — McMaster Turkish Society */
'use strict';

// ── Utilities ─────────────────────────────────────────────

function showAlert(id, msg, type) {
  const el = document.getElementById(id);
  if (!el) return;
  el.textContent  = msg;
  el.className    = 'alert ' + type;
  el.style.display = 'block';
}

function clearAlert(id) {
  const el = document.getElementById(id);
  if (el) el.style.display = 'none';
}

function clearAllAlerts() {
  document.querySelectorAll('.alert').forEach(el => el.style.display = 'none');
}

function setLoading(btn, loading) {
  if (!btn) return;
  btn.disabled    = loading;
  btn.textContent = loading ? 'Please wait…' : (btn.dataset.label || btn.textContent);
}

async function postJSON(url, payload) {
  const res = await fetch(url, {
    method:  'POST',
    headers: { 'Content-Type': 'application/json' },
    body:    JSON.stringify(payload),
  });
  return res.json();
}

// ── Tab switching ─────────────────────────────────────────

function switchTab(tab) {
  document.querySelectorAll('.tab-btn').forEach((btn, i) => {
    btn.classList.toggle('active',
      (i === 0 && tab === 'login') || (i === 1 && tab === 'signup')
    );
  });
  document.getElementById('login-section').classList.toggle('active',  tab === 'login');
  document.getElementById('signup-section').classList.toggle('active', tab === 'signup');
  clearAllAlerts();
}

// ── Password show/hide ────────────────────────────────────

function togglePw(inputId, btn) {
  const input = document.getElementById(inputId);
  const hide  = input.type === 'password';
  input.type  = hide ? 'text' : 'password';
  btn.textContent = hide ? 'Hide' : 'Show';
}

// ── Password strength ─────────────────────────────────────

function checkStrength(pw) {
  let score = 0;
  if (pw.length >= 8)           score++;
  if (/[A-Z]/.test(pw))        score++;
  if (/[0-9]/.test(pw))        score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;

  const colors = ['', '#E24B4A', '#EF9F27', '#639922', '#1D9E75'];
  const labels = ['', 'Weak', 'Fair', 'Good', 'Strong'];

  for (let i = 1; i <= 4; i++) {
    const bar = document.getElementById('sb' + i);
    if (bar) bar.style.background = i <= score ? colors[score] : 'var(--border)';
  }
  const lbl = document.getElementById('strength-label');
  if (lbl) { lbl.textContent = pw.length ? labels[score] : ''; lbl.style.color = colors[score]; }
}

// ── Client-side validation ────────────────────────────────

function validateLogin(email, password) {
  if (!email || !password)  return 'Please fill in all fields.';
  if (!email.includes('@')) return 'Please enter a valid email address.';
  return null;
}

function validateSignup(d) {
  if (!d.first_name || !d.last_name)       return 'First and last name are required.';
  if (!d.email.endsWith('@mcmaster.ca'))   return 'Please use your @mcmaster.ca email.';
  if (d.password.length < 8)              return 'Password must be at least 8 characters.';
  if (d.password !== d.password2)         return 'Passwords do not match.';
  if (d.student_id && !/^\d{9}$/.test(d.student_id)) return 'Student ID must be exactly 9 digits.';
  return null;
}

// ── Login ─────────────────────────────────────────────────

async function handleLogin() {
  const btn        = document.getElementById('login-btn');
  const email      = document.getElementById('login-email').value.trim().toLowerCase();
  const password   = document.getElementById('login-password').value;
  const rememberMe = document.getElementById('remember-me')?.checked ?? false;

  clearAlert('login-alert');

  const err = validateLogin(email, password);
  if (err) { showAlert('login-alert', err, 'error'); return; }

  setLoading(btn, true);
  try {
    const data = await postJSON('api/login.php', { email, password, remember_me: rememberMe });
    if (data.success) {
      showAlert('login-alert', data.message, 'success');
      setTimeout(() => window.location.href = data.redirect || '/member/dashboard.php', 800);
    } else {
      showAlert('login-alert', data.message, 'error');
    }
  } catch (e) {
    showAlert('login-alert', 'Connection error. Please try again.', 'error');
  } finally {
    setLoading(btn, false);
  }
}

// ── Signup ────────────────────────────────────────────────

async function handleSignup() {
  const btn = document.getElementById('signup-btn');

  const payload = {
    first_name: document.getElementById('first-name').value.trim(),
    last_name:  document.getElementById('last-name').value.trim(),
    email:      document.getElementById('signup-email').value.trim().toLowerCase(),
    student_id: document.getElementById('student-id').value.trim(),
    password:   document.getElementById('signup-password').value,
    password2:  document.getElementById('confirm-password').value,
  };

  clearAlert('signup-alert');

  const err = validateSignup(payload);
  if (err) { showAlert('signup-alert', err, 'error'); return; }

  setLoading(btn, true);
  try {
    const data = await postJSON('api/signup.php', payload);
    if (data.success) {
      showAlert('signup-alert', data.message, 'success');
      document.getElementById('signup-section').querySelectorAll('input').forEach(i => i.value = '');
      checkStrength('');
    } else {
      showAlert('signup-alert', data.message, 'error');
    }
  } catch (e) {
    showAlert('signup-alert', 'Connection error. Please try again.', 'error');
  } finally {
    setLoading(btn, false);
  }
}

// ── Forgot password ───────────────────────────────────────

async function handleForgot(e) {
  e.preventDefault();
  const email = document.getElementById('login-email')?.value.trim().toLowerCase() ?? '';
  clearAlert('login-alert');

  if (!email) {
    showAlert('login-alert', 'Enter your email address above, then click Forgot Password.', 'error');
    return;
  }

  try {
    const data = await postJSON('api/forgot_password.php', { email });
    showAlert('login-alert', data.message, 'success');
  } catch (e) {
    showAlert('login-alert', 'Connection error. Please try again.', 'error');
  }
}

// ── Init ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  ['login-btn', 'signup-btn'].forEach(id => {
    const btn = document.getElementById(id);
    if (btn) btn.dataset.label = btn.textContent;
  });
});
