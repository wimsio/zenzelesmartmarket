<?php 
session_start();
require_once 'header/username.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZenZele – Entrepreneurial Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
<style>
  :root {
    --bg:         #0e0e0f;
    --sidebar-bg: #131314;
    --card-bg:    #1a1a1b;
    --card-border:#2a2a2c;
    --accent:     #c87941;
    --accent-dim: #9a5c2e;
    --text:       #f0ede8;
    --muted:      #7a7875;
    --input-bg:   #1f1f21;
    --input-border:#333335;
    --input-focus: #c87941;
    --radius:     10px;
    --sidebar-w:  210px;
  }

  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

  body {
    font-family: 'DM Sans', sans-serif;
    background: var(--bg);
    color: var(--text);
    display: flex;
    min-height: 100vh;
    font-size: 14px;
  }

  /* ── SIDEBAR ── */
  .sidebar {
    width: var(--sidebar-w);
    background: var(--sidebar-bg);
    border-right: 1px solid var(--card-border);
    display: flex;
    flex-direction: column;
    padding: 24px 0 16px;
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 10;
  }

  .logo {
    padding: 0 20px 28px;
    display: flex; flex-direction: column;
  }
  .logo-text { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; letter-spacing: -.5px; }
  .logo-text span { color: var(--accent); }
  .logo-sub { font-size: 9px; letter-spacing: 2px; color: var(--muted); text-transform: uppercase; margin-top: 2px; }

  .nav-group { padding: 0 12px 4px; }
  .nav-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); padding: 10px 8px 6px; }
  .nav-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 10px; border-radius: 8px;
    color: var(--muted); font-size: 13px; font-weight: 400;
    cursor: pointer; transition: all .18s; position: relative;
    text-decoration: none;
  }
  .nav-item:hover { color: var(--text); background: rgba(255,255,255,.04); }
  .nav-item.active { color: var(--text); background: rgba(200,121,65,.15); }
  .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }
  .badge {
    margin-left: auto; background: var(--accent); color: #fff;
    font-size: 10px; font-weight: 700; border-radius: 20px;
    padding: 1px 6px; line-height: 16px;
  }

  .sidebar-user {
    margin-top: auto; padding: 12px 14px;
    border-top: 1px solid var(--card-border);
    display: flex; align-items: center; gap: 10px;
  }
  .avatar-sm {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, #c87941, #7a4520);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Syne', sans-serif; font-weight: 700; font-size: 12px;
    position: relative; flex-shrink: 0;
  }
  .avatar-sm::after {
    content: ''; position: absolute; bottom: 1px; right: 1px;
    width: 8px; height: 8px; border-radius: 50%;
    background: #3ecf5e; border: 2px solid var(--sidebar-bg);
  }
  .user-info { display: flex; flex-direction: column; }
  .user-name { font-size: 13px; font-weight: 500; }
  .user-role { font-size: 11px; color: var(--muted); }

  /* ── MAIN ── */
  .main {
    margin-left: var(--sidebar-w);
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
  }

  /* ── TOPBAR ── */
  .topbar {
    position: sticky; top: 0; z-index: 5;
    background: rgba(14,14,15,.85);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--card-border);
    padding: 0 28px;
    height: 56px;
    display: flex; align-items: center; justify-content: space-between;
  }
  .topbar-greeting { font-family: 'Syne', sans-serif; font-size: 15px; font-weight: 600; }
  .topbar-greeting span { font-size: 16px; }
  .topbar-actions { display: flex; align-items: center; gap: 16px; }
  .topbar-date { font-size: 12px; color: var(--muted); }
  .icon-btn {
    width: 32px; height: 32px; border-radius: 8px;
    background: var(--input-bg); border: 1px solid var(--input-border);
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--muted); transition: all .18s;
  }
  .icon-btn:hover { color: var(--text); border-color: var(--accent); }
  .icon-btn svg { width: 15px; height: 15px; }

   /* Bouton voir */
  .edit-profile-btn {
    position: absolute; top: 32px; right: 32px;
    background: var(--accent); color: #fff; text-decoration: none;
    padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;
    transition: background .2s;
  }
  .edit-profile-btn:hover { background: #d98c52; }

  /* ── CONTENT ── */
  .content { padding: 24px 28px 60px; }

  .hero-card {
    background: linear-gradient(120deg, #2a1a0d 0%, #1a1207 60%, #0e0e0f 100%);
    border: 1px solid rgba(200,121,65,.25);
    border-radius: 14px;
    padding: 24px 28px;
    margin-bottom: 28px;
    position: relative; overflow: hidden;
  }
  .hero-card::before {
    content: '';
    position: absolute; top: -40px; right: -40px;
    width: 180px; height: 180px; border-radius: 50%;
    background: radial-gradient(circle, rgba(200,121,65,.2) 0%, transparent 70%);
    pointer-events: none;
  }
  .hero-card h2 { font-family: 'Syne', sans-serif; font-size: 18px; font-weight: 700; margin-bottom: 4px; }
  .hero-card p { color: var(--muted); font-size: 13px; }

  /* ── FORM CARD ── */
  .form-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: 14px;
    padding: 36px 40px;
    max-width: 680px;
    margin: 0 auto;
  }

  .form-title {
    font-family: 'Syne', sans-serif;
    font-size: 20px; font-weight: 700;
    text-align: center;
    margin-bottom: 32px;
    color: var(--text);
  }

  .form-group { margin-bottom: 20px; }

  label {
    display: block;
    font-size: 12px; font-weight: 500;
    letter-spacing: .5px;
    color: var(--muted);
    text-transform: uppercase;
    margin-bottom: 8px;
  }

  input[type="text"],
  input[type="email"],
  select,
  textarea {
    width: 100%;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: var(--radius);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 14px;
    padding: 11px 14px;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    appearance: none;
  }
  input::placeholder, textarea::placeholder { color: var(--muted); }
  input:focus, select:focus, textarea:focus {
    border-color: var(--input-focus);
    box-shadow: 0 0 0 3px rgba(200,121,65,.12);
  }

  select {
    cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%237a7875' stroke-width='2.5'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 36px;
  }
  select option { background: #1f1f21; color: var(--text); }

  textarea { resize: vertical; min-height: 96px; line-height: 1.6; }

  /* File input */
  .file-wrapper {
    display: flex; align-items: center; gap: 12px;
  }
  .file-btn {
    flex-shrink: 0;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: var(--radius);
    color: var(--text);
    font-family: 'DM Sans', sans-serif;
    font-size: 13px;
    padding: 10px 16px;
    cursor: pointer;
    transition: border-color .2s;
  }
  .file-btn:hover { border-color: var(--accent); }
  .file-name { color: var(--muted); font-size: 13px; font-style: italic; }
  input[type="file"] { display: none; }

  /* Audio section */
  .audio-section {
    background: rgba(200,121,65,.06);
    border: 1px solid rgba(200,121,65,.2);
    border-radius: var(--radius);
    padding: 18px 20px;
    margin-bottom: 20px;
  }
  .audio-section p { font-size: 12px; color: var(--muted); margin-bottom: 14px; line-height: 1.6; }

  .audio-controls { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }

  .record-btn {
    display: flex; align-items: center; gap: 8px;
    background: rgba(220,60,60,.15);
    border: 1px solid rgba(220,60,60,.4);
    border-radius: 8px;
    color: #f55;
    font-size: 13px; font-weight: 500;
    padding: 9px 16px;
    cursor: pointer;
    transition: all .2s;
    font-family: 'DM Sans', sans-serif;
  }
  .record-btn:hover { background: rgba(220,60,60,.25); }
  .rec-dot {
    width: 9px; height: 9px; border-radius: 50%;
    background: #f55;
    animation: pulse 1.4s ease-in-out infinite;
  }
  @keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50%       { opacity: .5; transform: scale(.85); }
  }

  .music-select-wrap { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
  .music-label { font-size: 12px; color: var(--muted); white-space: nowrap; }
  .music-select-wrap select { width: auto; font-size: 12px; padding: 8px 32px 8px 12px; }

  /* Playback */
  .playback-wrap {
    display: flex; align-items: center; gap: 10px;
    margin-top: 14px;
  }
  .playback-label { font-size: 12px; color: var(--muted); white-space: nowrap; }
  .audio-player {
    flex: 1;
    height: 36px;
    background: var(--input-bg);
    border: 1px solid var(--input-border);
    border-radius: 8px;
    display: flex; align-items: center;
    padding: 0 12px; gap: 10px;
  }
  .play-icon { color: var(--muted); cursor: pointer; transition: color .18s; }
  .play-icon:hover { color: var(--text); }
  .track-bar {
    flex: 1; height: 3px; background: var(--input-border);
    border-radius: 99px; overflow: hidden;
  }
  .track-fill { height: 100%; width: 0%; background: var(--accent); border-radius: 99px; }
  .time-txt { font-size: 11px; color: var(--muted); font-variant-numeric: tabular-nums; }

  /* Divider */
  .divider {
    border: none; border-top: 1px solid var(--card-border);
    margin: 28px 0;
  }

  /* Submit */
  .submit-btn {
    width: 100%;
    background: var(--accent);
    color: #fff;
    border: none; border-radius: var(--radius);
    font-family: 'Syne', sans-serif;
    font-size: 15px; font-weight: 700;
    letter-spacing: .3px;
    padding: 14px;
    cursor: pointer;
    transition: background .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 4px 20px rgba(200,121,65,.3);
  }
  .submit-btn:hover {
    background: #d98c52;
    transform: translateY(-1px);
    box-shadow: 0 6px 26px rgba(200,121,65,.45);
  }
  .submit-btn:active { transform: translateY(0); }

  /* Responsive */
  @media (max-width: 700px) {
    .sidebar { display: none; }
    .main { margin-left: 0; }
    .form-card { padding: 24px 18px; }
    .content { padding: 16px 12px 40px; }
    .audio-controls { flex-direction: column; align-items: flex-start; }
    .edit-profile-btn { position: static; margin-top: 10px; width: 100%; text-align: center; }
  }

</style>
</head>
<body>

<!-- ── SIDEBAR ── -->
<aside class="sidebar">
  <div class="logo">
    <div class="logo-text">Zen<span>Zele</span></div>
    <div class="logo-sub">Smart Market</div>
  </div>

  <div class="nav-group">
    <div class="nav-label">General</div>
    <a class="nav-item active" href="acceuil.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
      Dashboard
    </a>
    <a class="nav-item" href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
      Trainings
      <span class="badge">3</span>
    </a>
    <a class="nav-item" href="donations.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
      Donations
    </a>
    <a class="nav-item" href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
      Certificates
    </a>
  </div>

  <div class="nav-group">
    <div class="nav-label">Market</div>
    <a class="nav-item" href="nft.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
      NFTs
    </a>
    <a class="nav-item" href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/></svg>
      Audio
    </a>
    <a class="nav-item" href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
      Community
    </a>
  </div>

  <div class="nav-group">
    <div class="nav-label">Account</div>
    <a class="nav-item" href="profile2.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Profile
    </a>
    <a class="nav-item" href="auth/logout.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93l-1.41 1.41M4.93 4.93l1.41 1.41M12 2v2M12 20v2M20 12h2M2 12h2M19.07 19.07l-1.41-1.41M4.93 19.07l1.41-1.41"/></svg>
      Logout
    </a>
  </div>

  <div class="sidebar-user">
    <div class="avatar-sm">AK</div>
    <div class="user-info">
      <span class="user-name">
        <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?> ! <span>👋</span>
      </span>
      <span class="user-role">Pro Learner</span>
    </div>
  </div>
</aside>

<!-- ── MAIN ── -->
<div class="main">

  <!-- Topbar -->
  <header class="topbar">
    <div class="topbar-greeting">Hello, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?> <span>👋</span></div>
    <div class="topbar-actions">
      <span class="topbar-date">Tuesday, 26 May 2026</span>
      <div class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
      </div>
      <div class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      </div>
      <div class="icon-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
      </div>
    </div>
  </header>


  <!-- Content -->
  <div class="content">

    <!-- Hero -->
    <div class="hero-card">
      <h2>User Profile</h2>
      <p>Welcome to your profile, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>!</p>
      <div class="profile-header-card">
      <a href="affiProfile.php" class="edit-profile-btn" >View my profile</a>
    </div>
    </div>
    
    <!-- Form Card -->
    <form id="profilesForm" onsubmit="return false;" enctype="multipart/form-data">
    <div class="form-card">
      <h3 class="form-title">Create your entrepreneurial profile</h3>

      <div class="form-group">
        <label>Your full name</label>
        <input type="text" id="username" placeholder="e.g. Amina Bakery">
      </div>

      <div class="form-group">
        <label>Company name</label>
        <input type="text" id="entrepriseName" placeholder="e.g. coxygene">
      </div>

      <div class="form-group">
        <label>Your country</label>
        <input type="text" id="regCountry" placeholder="e.g. Burkina Faso">
      </div>

      <div class="form-group">
        <label>Your city and region</label>
        <input type="text" id="city" placeholder="e.g. Ouagadougou, Kadiogo">
      </div>

      <div class="form-group">
            <label>Language</label>
            <select id="langue">
              <option value="" disabled selected>Select your language</option>
              <option value="en">English</option>
              <option value="zu">isiZulu</option>
              <option value="xh">isiXhosa</option>
              <option value="st">Sesotho</option>
              <option value="tn">Setswana</option>
              <option value="af">Afrikaans</option>
              <option value="sw">Swahili</option>
            </select>
      </div>

      <div class="form-group">
        <label>Industry</label>
        <select id="activity">
  <option value="agriculture">Agriculture & Food</option>
  <option value="entrepreneur">Crafts & Fashion</option>
  <option value="technology">Technology & Digital</option>
  <option value="health">Health & Wellness</option>
  <option value="education">Education & Training</option>
  <option value="other">Other</option>
</select>
      </div>

      <div class="form-group">
        <label>Your biography (short description)</label>
        <textarea id="biographie" placeholder="Tell what you do and what you are proud of…"></textarea>
      </div>

       <div class="form-group">
        <label>Company description (short)</label>
        <textarea id="entrepriseDesc" placeholder="Describe your company"></textarea>
      </div>

      <div class="form-group">
        <label>Skills</label>
        <textarea id="competence" placeholder="Describe your skills and qualifications"></textarea>
      </div>

      

      <div class="form-group">
        <label>LinkedIn profile</label>
        <input type="url" id="adresslinkedin" placeholder="https://example.com/profile">
      </div>

      <div class="form-group">
        <label>Profile photo <span style="color:var(--muted);font-size:11px;text-transform:none;letter-spacing:0">(optional)</span></label>
        <div class="file-wrapper">
          <button type="button" class="file-btn" onclick="document.getElementById('photo-upload').click()">
            &#128247; Choose an image
          </button>
          <input type="file" id="photo-upload" name="photo" accept="image/*" style="display:none">
          <span class="file-name" id="photo-name">No file selected</span>
        </div>
        <div id="photo-preview-wrap" style="display:none;margin-top:12px">
          <img id="photo-preview" src="" alt="Aperçu"
            style="width:90px;height:90px;border-radius:50%;object-fit:cover;border:2px solid var(--accent);display:block">
        </div>
      </div>

      <hr class="divider">

      <!-- Audio Section -->
      <div class="audio-section">
        <label style="text-transform:none;letter-spacing:0;font-size:14px;font-weight:600;color:var(--text);margin-bottom:6px">Audio Presentation</label>
        <p>Ideal if you prefer speaking rather than writing. Record a message up to one minute.</p>

        <div class="audio-controls">
          <button type="button" class="record-btn" id="recordBtn">
            <span class="rec-dot"></span>
            <span id="recLabel">Record my voice</span>
            <span id="recTimer" style="display:none;margin-left:6px;font-size:12px;opacity:.8"></span>
          </button>

          <div class="music-select-wrap">
            <span class="music-label">Add a soft background track:</span>
            <select id="musicTrack">
              <option>No music (Voice only)</option>
              <option>Soft melody</option>
              <option>Nature ambiance</option>
              <option>Calm piano</option>
            </select>
          </div>
        </div>

        <div id="recStatus" style="font-size:12px;margin-top:10px;min-height:18px;color:var(--muted)"></div>

        <audio id="audioPreview" style="display:none"></audio>

        <div class="playback-wrap" style="margin-top:10px">
          <span class="playback-label">Listen to your presentation before submitting:</span>
          <div class="audio-player">
            <span id="playBtn" class="play-icon" style="cursor:pointer;font-size:13px;user-select:none">▶</span>
            <div class="track-bar"><div class="track-fill" id="trackFill"></div></div>
            <span class="time-txt" id="timeTxt">0:00 / 0:00</span>
          </div>
        </div>

          <div style="margin-top:12px">
            <button
        type="button"
        id="downloadBtn"
        class="record-btn"
        style="display:none;background:rgba(200,121,65,.15);border-color:rgba(200,121,65,.4);color:var(--accent)"
      >
        ⬇ Download recording
      </button>
        </div>
      </div>

      <button type="submit" class="submit-btn">Submit my profile →</button>
    </div>
  </form>
  </div>
</div>
<script src="assets/js/i18n.js"></script>
<script src="assets/js/profiles.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', async () => {
    // await i18n.init();
    // Écouter la soumission du formulaire 
   document.getElementById('profilesForm').addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData();

    formData.append('username', document.getElementById('username').value);
    formData.append('entrepriseName', document.getElementById('entrepriseName').value);
    formData.append('regCountry', document.getElementById('regCountry').value);
    formData.append('city', document.getElementById('city').value);
    formData.append('langue', document.getElementById('langue').value);
    formData.append('activity', document.getElementById('activity').value);
    formData.append('biographie', document.getElementById('biographie').value);
    formData.append('entrepriseDesc', document.getElementById('entrepriseDesc').value);
    formData.append('competence', document.getElementById('competence').value);
    formData.append('adresslinkedin', document.getElementById('adresslinkedin').value);

    const photo = document.getElementById('photo-upload').files[0];

    if (photo) {
        formData.append('photo', photo);
    }

    if (audioBlob) {
        formData.append('audio', audioBlob, 'presentation.webm');
    }

    const response = await fetch('../api/profiles/profiles.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.json();
    if(result.success) {
      alert('Profile created successfully!');
      window.location.href = 'affiProfile.php';
    } else {
      alert(result.message);
    }
});

   // ── File input label ──
    document.getElementById('photo-upload').addEventListener('change', function() {
      if (this.files && this.files[0]) {
        const file = this.files[0];
        document.getElementById('photo-name').textContent = file.name;

        const reader = new FileReader();
        reader.onload = function(e) {
          const preview = document.getElementById('photo-preview');
          const wrap    = document.getElementById('photo-preview-wrap');
          preview.src       = e.target.result;
          wrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
      } else {
        document.getElementById('photo-name').textContent = 'No file selected';
        document.getElementById('photo-preview-wrap').style.display = 'none';
      }
    });  
    

    // ── Audio Recording (MediaRecorder API) ──
    let mediaRecorder = null;
    let audioChunks   = [];
    let audioBlob     = null;
    let audioURL      = null;
    let timerInterval = null;
    let elapsedSec    = 0;
    const MAX_SEC     = 180;

    const btn         = document.getElementById('recordBtn');
    const recLabel    = document.getElementById('recLabel');
    const recTimer    = document.getElementById('recTimer');
    const statusMsg   = document.getElementById('recStatus');
    const audioEl     = document.getElementById('audioPreview');
    const playBtn     = document.getElementById('playBtn');
    const trackFill   = document.getElementById('trackFill');
    const timeTxt     = document.getElementById('timeTxt');
    const downloadBtn = document.getElementById('downloadBtn');

    function fmtTime(s) {
      const m = Math.floor(s / 60);
      return m + ':' + String(Math.floor(s % 60)).padStart(2, '0');
    }

    function startTimer() {
      elapsedSec = 0;
      recTimer.textContent = '0:00 / 1:00';
      timerInterval = setInterval(() => {
        elapsedSec++;
        recTimer.textContent = fmtTime(elapsedSec) + ' / 1:00';
        if (elapsedSec >= MAX_SEC) stopRecording();
      }, 1000);
    }

    function stopTimer() {
      clearInterval(timerInterval);
      timerInterval = null;
    }

    async function startRecording() {
      try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        audioChunks  = [];

      // pick best supported format
      const mimeType = MediaRecorder.isTypeSupported('audio/webm;codecs=opus')
        ? 'audio/webm;codecs=opus'
        : MediaRecorder.isTypeSupported('audio/ogg;codecs=opus')
          ? 'audio/ogg;codecs=opus'
          : 'audio/webm';

      mediaRecorder = new MediaRecorder(stream, { mimeType });

      mediaRecorder.ondataavailable = e => {
        if (e.data.size > 0) audioChunks.push(e.data);
      };

      mediaRecorder.onstop = () => {
        stream.getTracks().forEach(t => t.stop());
        audioBlob = new Blob(audioChunks, { type: mimeType });
        if (audioURL) URL.revokeObjectURL(audioURL);
        audioURL = URL.createObjectURL(audioBlob);

        audioEl.src = audioURL;
        audioEl.load();
        statusMsg.textContent = '✅ Recording ready. Listen below.';
        statusMsg.style.color = '#3ecf5e';
        downloadBtn.style.display = 'inline-flex';
      };

      mediaRecorder.start(200);
      startTimer();

      btn.style.background = 'rgba(220,60,60,.35)';
      recLabel.textContent  = 'Stop recording';
      recTimer.style.display = 'inline';
      statusMsg.textContent  = '🎙️ Recording in progress…';
      statusMsg.style.color  = '#f55';
      downloadBtn.style.display = 'none';

    } catch (err) {
      if (err.name === 'NotAllowedError') {
        statusMsg.textContent = '⚠️ Microphone access denied. Allow it in your browser settings.';
      } else {
        statusMsg.textContent = '⚠️ Unable to access the microphone: ' + err.message;
      }
      statusMsg.style.color = '#f55';
    }
  }

  function stopRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
      mediaRecorder.stop();
    }
    stopTimer();
    btn.style.background  = 'rgba(220,60,60,.15)';
    recLabel.textContent  = 'Enregistrer ma voix';
    recTimer.style.display = 'none';
  }

  btn.addEventListener('click', () => {
    if (!mediaRecorder || mediaRecorder.state === 'inactive') {
      startRecording();
    } else {
      stopRecording();
    }
  });

  // ── Playback controls ──
  audioEl.addEventListener('timeupdate', () => {
    if (!audioEl.duration) return;
    const pct = (audioEl.currentTime / audioEl.duration) * 100;
    trackFill.style.width = pct + '%';
    timeTxt.textContent   = fmtTime(audioEl.currentTime) + ' / ' + fmtTime(audioEl.duration);
  });

  audioEl.addEventListener('ended', () => {
    playBtn.innerHTML = '▶';
    trackFill.style.width = '0%';
  });

  playBtn.addEventListener('click', () => {
    if (!audioEl.src) return;
    if (audioEl.paused) {
      audioEl.play();
      playBtn.innerHTML = '⏸';
    } else {
      audioEl.pause();
      playBtn.innerHTML = '▶';
    }
  });

  // ── Download ──
  document.getElementById('downloadBtn').addEventListener('click', () => {
    if (!audioBlob) return;
    const ext = audioBlob.type.includes('ogg') ? 'ogg' : 'webm';
    const a   = document.createElement('a');
    a.href     = audioURL;
    a.download = 'presentation-vocale.' + ext;
    a.click();
  });
});
</script>
</body>
</html>
