<?php
// 1. Initialisation de la session si nécessaire
// session_start();
// $userId = $_SESSION['user_id'] ?? 1;

// 2. Connexion à la base de données
require_once '../app/config/db.php'; // Ajustez le chemin selon votre structure
require_once 'header/username.php';

// 3. Valeurs par défaut
$profile = [
  'username' => 'Not set',
  'entreprise_name' => 'Not set',
  'country' => 'Not set',
  'city' => 'Not set',
  'langue' => 'fr',
  'activity' => 'Not set',
  'biographie' => 'No biography written yet.',
  'entreprise_desc' => 'No company description.',
  'competence' => 'Not specified.',
  'wallet_address' => 'Not provided',
  'adresslinkedin' => '',
  'photo' => '',
  'audio' => ''
];

try {
    // 4. Récupération du profil
    $stmt = $pdo->query("SELECT * FROM profiles ORDER BY id DESC LIMIT 1");
    $dbProfile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($dbProfile) {
        $profile = array_merge($profile, $dbProfile);
    }
} catch (Exception $e) {
    // Gestion d'erreur silencieuse en production
}

// Extraction des initiales pour l'avatar par défaut
$words = explode(' ', $profile['username']);
$initials = strtoupper((substr($words[0] ?? 'Z', 0, 1)) . (substr($words[1] ?? '', 0, 1)));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZenZele – My Entrepreneurial Profile</title>
<link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;700&display=swap" rel="stylesheet">
<style>
  :root {
    --bg:         #0e0e0f;
    --sidebar-bg: #131314;
    --card-bg:    #1a1a1b;
    --card-border:#2a2a2c;
    --accent:     #c87941;
    --text:       #f0ede8;
    --muted:      #7a7875;
    --radius:     14px;
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

  /* ── SIDEBAR (Identique) ── */
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
  .logo { padding: 0 20px 28px; display: flex; flex-direction: column; }
  .logo-text { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; letter-spacing: -.5px; }
  .logo-text span { color: var(--accent); }
  .logo-sub { font-size: 9px; letter-spacing: 2px; color: var(--muted); text-transform: uppercase; margin-top: 2px; }
  .nav-group { padding: 0 12px 4px; }
  .nav-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); padding: 10px 8px 6px; }
  .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 8px; color: var(--muted); font-size: 13px; text-decoration: none; transition: all .18s; }
  .nav-item:hover, .nav-item.active { color: var(--text); background: rgba(255,255,255,.04); }
  .nav-item.active { background: rgba(200,121,65,.15); }
  .nav-item svg { width: 16px; height: 16px; }

  /* ── MAIN CONTENT ── */
  .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; }
  .content { padding: 40px 28px 60px; max-width: 900px; width: 100%; margin: 0 auto; }

  /* Header du Profil */
  .profile-header-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius);
    padding: 32px;
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 28px;
    position: relative;
  }
  .profile-avatar-big {
    width: 100px; height: 100px; border-radius: 50%;
    object-fit: cover; border: 3px solid var(--accent);
    background: linear-gradient(135deg, #c87941, #7a4520);
    display: flex; align-items: center; justify-content: center;
    font-family: 'Syne', sans-serif; font-size: 32px; font-weight: 700; color: #fff;
  }
  .profile-title-info h1 { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 800; margin-bottom: 6px; }
  .company-badge {
    display: inline-block; background: rgba(200,121,65,.15); color: var(--accent);
    padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; margin-bottom: 8px;
  }
  .location-txt { color: var(--muted); font-size: 13px; display: flex; align-items: center; gap: 4px; }

  /* Bouton Modifier */
  .edit-profile-btn {
    position: absolute; top: 32px; right: 32px;
    background: var(--accent); color: #fff; text-decoration: none;
    padding: 10px 18px; border-radius: 8px; font-size: 13px; font-weight: 600;
    transition: background .2s;
  }
  .edit-profile-btn:hover { background: #d98c52; }

  /* Grille de Blocs d'informations */
  .profile-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 28px; }
  .main-info-column, .side-info-column { display: flex; flex-direction: column; gap: 28px; }

  .info-block {
    background: var(--card-bg); border: 1px solid var(--card-border);
    border-radius: var(--radius); padding: 28px;
  }
  .info-block h3 { font-family: 'Syne', sans-serif; font-size: 16px; margin-bottom: 16px; color: var(--accent); border-bottom: 1px solid var(--card-border); padding-bottom: 8px; }
  .info-block p { line-height: 1.6; color: var(--text); font-size: 14px; white-space: pre-line; }

  /* Éléments Clés Métadonnées */
  .meta-item { margin-bottom: 16px; }
  .meta-item:last-child { margin-bottom: 0; }
  .meta-label { font-size: 11px; text-transform: uppercase; color: var(--muted); letter-spacing: .5px; margin-bottom: 4px; display: block;}
  .meta-value { font-size: 14px; font-weight: 500; }

  /* Lecteur Audio Intégré */
  .audio-card {
    background: rgba(200,121,65,.04); border: 1px solid rgba(200,121,65,.15);
    border-radius: var(--radius); padding: 20px; display: flex; flex-direction: column; gap: 12px;
  }
  .audio-player-wrap {
    display: flex; align-items: center; gap: 12px; background: var(--bg);
    border: 1px solid var(--card-border); padding: 10px 16px; border-radius: 8px;
  }
  .play-btn { cursor: pointer; color: var(--accent); font-size: 16px; user-select: none; }
  .progress-bar { flex: 1; height: 4px; background: var(--card-border); border-radius: 2px; overflow: hidden; position: relative; }
  .progress-fill { height: 100%; width: 0%; background: var(--accent); }
  .time-txt { font-size: 11px; color: var(--muted); font-variant-numeric: tabular-nums; }

  /* Lien de réseau social */
  .linkedin-link {
    display: inline-flex; align-items: center; gap: 8px; color: #0077b5;
    text-decoration: none; font-weight: 500; font-size: 14px; transition: opacity .2s;
  }
  .linkedin-link:hover { opacity: 0.8; }

  @media (max-width: 800px) {
    .sidebar { display: none; }
    .main { margin-left: 0; }
    .profile-grid { grid-template-columns: 1fr; }
    .profile-header-card { flex-direction: column; text-align: center; padding-top: 60px; }
    .edit-profile-btn { position: static; margin-top: 10px; width: 100%; text-align: center; }
  }
</style>
</head>
<body>

<aside class="sidebar">

    <div class="sidebar-logo">
      <div class="sidebar-logo-text">Zen<span>Zele</span></div>
      <div class="sidebar-logo-sub">Smart Market</div>
    </div>

    <nav class="sidebar-nav">
      <div class="sidebar-section">General</div>

      <a href="acceuil.html" class="sidebar-item active">
        <i class="ti ti-layout-dashboard"></i>
        Dashboard
      </a>
      <a href="formations.html" class="sidebar-item">
        <i class="ti ti-school"></i>
        Trainings
        <span class="sidebar-badge">3</span>
      </a>
      <a href="donations.php" class="sidebar-item">
        <i class="ti ti-heart"></i>
        Donations
      </a>
      <a href="certificats.html" class="sidebar-item">
        <i class="ti ti-certificate"></i>
        Certificates
      </a>

      <div class="sidebar-section">Market</div>

      <a href="nfts.php" class="sidebar-item">
        <i class="ti ti-photo"></i>
        NFTs
      </a>
      <a href="audio.html" class="sidebar-item">
        <i class="ti ti-music"></i>
        Audio
      </a>
      <a href="communaute.html" class="sidebar-item">
        <i class="ti ti-social"></i>
        Community
      </a>

      <div class="sidebar-section">Account</div>

      <a href="profile2.php" class="sidebar-item">
        <i class="ti ti-user"></i>
        Profile
      </a>
      <a href="auth/logout.php" class="sidebar-item">
        <i class="ti ti-settings"></i>
        Logout
      </a>
    </nav>

    <div class="sidebar-user">
      <div class="sidebar-avatar">AK</div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name">Aminata Koné</div>
        <div class="sidebar-user-role">Pro Learner</div>
      </div>
      <div class="sidebar-online"></div>
    </div>

  </aside>

<!-- ── MAIN ── -->
<div class="main">
  <div class="content">

    <!-- Fiche Entête de Profil -->
    <div class="profile-header-card">
      <?php if (!empty($profile['photo'])): ?>
      <img class="profile-avatar-big" src="../<?= htmlspecialchars($profile['photo']) ?>" alt="Profile photo">
      <?php else: ?>
        <div class="profile-avatar-big"><?= $initials ?></div>
      <?php endif; ?>

      <div class="profile-title-info">
        <span class="company-badge"><?= htmlspecialchars($profile['entrepriseName'] ?: 'Individual Business') ?></span>
        <h1><?= htmlspecialchars($profile['username']) ?></h1>
        <div class="location-txt">
          <span>📍</span> <?= htmlspecialchars($profile['city']) ?>, <?= htmlspecialchars($profile['country']) ?>
        </div>
      </div>

      <!-- Link back to the edit form -->
      <a href="profile2.php" class="edit-profile-btn">Edit Profile</a>
    </div>

    <!-- Grille de Contenu -->
    <div class="profile-grid">
      
      <!-- Colonne Principale (Textes longs) -->
      <div class="main-info-column">
        
        <div class="info-block">
          <h3>Biography</h3>
          <p><?= htmlspecialchars($profile['biographie']) ?></p>
        </div>

        <div class="info-block">
          <h3>About the Company</h3>
          <p><?= htmlspecialchars($profile['entrepriseDesc']) ?></p>
        </div>

        <div class="info-block">
          <h3>Skills & Qualifications</h3>
          <p><?= htmlspecialchars($profile['competence']) ?></p>
        </div>

      </div>

      <!-- Colonne Latérale (Métadonnées, Audio et Réseaux) -->
      <div class="side-info-column">
        
        <!-- Fiche détails -->
        <div class="info-block">
          <h3>Key Details</h3>
          
          <div class="meta-item">
            <span class="meta-label">Industry</span>
            <span class="meta-value" style="color: var(--accent);"><?= htmlspecialchars($profile['activity']) ?></span>
          </div>

          <div class="meta-item">
            <span class="meta-label">Communication Language</span>
            <span class="meta-value"><?= strtoupper(htmlspecialchars($profile['langue'])) ?></span>
          </div>

          <div class="meta-item">
            <span class="meta-label">Coxy Wallet Address</span>
            <span class="meta-value" style="font-family: monospace; font-size:12px; word-break: break-all;"><?= htmlspecialchars($profile['wallet_address']) ?></span>
          </div>

          <?php if (!empty($profile['adresslinkedin'])): ?>
          <div class="meta-item" style="margin-top: 20px;">
              <a href="<?= htmlspecialchars($profile['adresslinkedin']) ?>" target="_blank" class="linkedin-link">
              <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                LinkedIn Profile
            </a>
          </div>
          <?php endif; ?>
        </div>

        <!-- Fiche Présentation Audio (S'il y a un audio en BDD) -->
        <?php if (!empty($profile['audio'])): ?>
        <div class="info-block audio-card">
          <span class="meta-label" style="color: var(--text);">🎙️ Audio Pitch</span>
          
          <audio id="profileAudio" src="../<?= htmlspecialchars($profile['audio']) ?>" preload="auto"></audio>
          
          <div class="audio-player-wrap">
            <div id="playBtn" class="play-btn">▶</div>
            <div class="progress-bar">
              <div id="progressFill" class="progress-fill"></div>
            </div>
            <div id="timeTxt" class="time-txt">0:00</div>
          </div>
        </div>
        <?php endif; ?>

      </div>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const audio = document.getElementById('profileAudio');
  const playBtn = document.getElementById('playBtn');
  const progressFill = document.getElementById('progressFill');
  const timeTxt = document.getElementById('timeTxt');

  if (!audio) return;

  function fmtTime(s) {
    if (isNaN(s)) return '0:00';
    const m = Math.floor(s / 60);
    return m + ':' + String(Math.floor(s % 60)).padStart(2, '0');
  }

  audio.addEventListener('loadedmetadata', () => {
    timeTxt.textContent = fmtTime(audio.duration);
  });

  playBtn.addEventListener('click', () => {
    if (audio.paused) {
      audio.play();
      playBtn.textContent = '⏸';
    } else {
      audio.pause();
      playBtn.textContent = '▶';
    }
  });

  audio.addEventListener('timeupdate', () => {
    const pct = (audio.currentTime / audio.duration) * 100;
    progressFill.style.width = pct + '%';
    timeTxt.textContent = fmtTime(audio.currentTime);
  });

  audio.addEventListener('ended', () => {
    playBtn.textContent = '▶';
    progressFill.style.width = '0%';
  });
});
</script>
</body>
</html>