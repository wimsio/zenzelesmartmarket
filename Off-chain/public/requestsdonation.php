<?php
// 1. Initialisation de la session et sécurisation de la page
session_start();
if (!isset($_SESSION['user_id'])) {
    // Rediriger vers la page de connexion si l'utilisateur n'est pas connecté
    // header("Location: login.html");
    // exit;
}

// 2. Connexion à la base de données
require_once '../app/config/db.php'; 
require_once 'header/username.php'; // Pour récupérer le nom d'utilisateur

$demandes = [];
$erreur = "";

try {
    // 3. Récupération de toutes les demandes de dons (les plus récentes en premier)
    $stmt = $pdo->query("SELECT id, amount, motif, dateLimit, benefit, created_at FROM donations ORDER BY id DESC");
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $erreur = "Erreur lors de la récupération des demandes : " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZenZele – Demandes de Dons</title>
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
    --success:    #2e7d32;
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
  .logo { padding: 0 20px 28px; display: flex; flex-direction: column; }
  .logo-text { font-family: 'Syne', sans-serif; font-size: 22px; font-weight: 800; letter-spacing: -.5px; }
  .logo-text span { color: var(--accent); }
  .logo-sub { font-size: 9px; letter-spacing: 2px; color: var(--muted); text-transform: uppercase; margin-top: 2px; }
  .nav-group { padding: 0 12px 4px; }
  .nav-label { font-size: 9px; letter-spacing: 2px; text-transform: uppercase; color: var(--muted); padding: 10px 8px 6px; }
  .nav-item { display: flex; align-items: center; gap: 10px; padding: 9px 10px; border-radius: 8px; color: var(--muted); font-size: 13px; text-decoration: none; transition: all .18s; }
  .nav-item:hover, .nav-item.active { color: var(--text); background: rgba(255,255,255,.04); }
  .nav-item.active { background: rgba(200,121,65,.15); color: var(--text); }
  .nav-item svg { width: 16px; height: 16px; }

  /* ── MAIN CONTENT ── */
  .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; }
  .content { padding: 40px 28px 60px; max-width: 1100px; width: 100%; margin: 0 auto; }

  .header-section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
  }
  .header-section h1 {
    font-family: 'Syne', sans-serif;
    font-size: 28px;
    font-weight: 800;
  }
  .header-section p {
    color: var(--muted);
    margin-top: 4px;
  }

  .btn-add {
    background: var(--accent);
    color: #fff;
    text-decoration: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    transition: background .2s;
  }
  .btn-add:hover { background: #d98c52; }

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

  /* ── GRILLE DES DONS ── */
  .dons-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 24px;
  }

  .don-card {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius);
    padding: 24px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    position: relative;
    transition: transform .2s, border-color .2s;
  }
  .don-card:hover {
    transform: translateY(-2px);
    border-color: rgba(200,121,65, 0.4);
  }

  .don-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 16px;
  }

  .amount-badge {
    background: rgba(200,121,65, 0.15);
    color: var(--accent);
    padding: 6px 14px;
    border-radius: 30px;
    font-family: 'Syne', sans-serif;
    font-weight: 700;
    font-size: 16px;
  }

  .date-badge {
    font-size: 11px;
    color: var(--muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(255,255,255,0.03);
    padding: 4px 8px;
    border-radius: 4px;
    border: 1px solid var(--card-border);
  }

  .don-body {
    flex: 1;
    margin-bottom: 20px;
  }

  .don-title {
    font-family: 'Syne', sans-serif;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 8px;
    color: var(--text);
  }

  .don-desc {
    color: #b3b0aa;
    line-height: 1.5;
    font-size: 13.5px;
    white-space: pre-line;
  }

  .don-footer {
    border-top: 1px solid var(--card-border);
    padding-top: 14px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }

  .benefit-lbl {
    font-size: 11px;
    text-transform: uppercase;
    color: var(--muted);
    display: block;
    margin-bottom: 2px;
  }

  .benefit-val {
    font-weight: 500;
    color: var(--text);
  }

  .btn-participer {
    background: transparent;
    border: 1px solid var(--accent);
    color: var(--accent);
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
  }
  .btn-participer:hover {
    background: var(--accent);
    color: #fff;
  }

  .error-box {
    background: rgba(211, 47, 47, 0.1);
    border: 1px solid #d32f2f;
    color: #ef5350;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 24px;
  }

  .empty-box {
    background: var(--card-bg);
    border: 1px solid var(--card-border);
    border-radius: var(--radius);
    padding: 40px;
    text-align: center;
    color: var(--muted);
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

  @media (max-width: 768px) {
    .sidebar { display: none; }
    .main { margin-left: 0; }
    .header-section { flex-direction: column; align-items: flex-start; gap: 16px; }
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
      donations
    </a>
    <a class="nav-item" href="#">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/></svg>
      Certificats
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
      Communauty
    </a>
  </div>

  <div class="nav-group">
    <div class="nav-label">Account</div>
    <a class="nav-item" href="profile2.php">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
      Profil
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
        <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Invité'; ?> ! <span>👋</span>
      </span>
      <span class="user-role">Apprenante Pro</span>
    </div>
  </div>
</aside>

<!-- ── MAIN ── -->
<div class="main">

  <!-- Topbar -->
  <header class="topbar">
    <div class="topbar-greeting">Bonjour, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Invité'; ?> <span>👋</span></div>
    <div class="topbar-actions">
      <span class="topbar-date">Mardi, 26 mai 2026</span>
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

  <div class="content">

    <div class="header-section">
      <div>
        <h1>Donations requests </h1>
        <p>Support the projects and entrepreneurs of the Cardano community.</p>
      </div>
      <a href="donations.php" class="btn-add">+ Create a request</a>
    </div>

    <?php if (!empty($erreur)): ?>
      <div class="error-box"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if (count($demandes) > 0): ?>
      <div class="dons-grid">
        <?php foreach ($demandes as $don): ?>
          <div class="don-card">
            
            <div>
              <div class="don-header">
                <div class="amount-badge">
                  <?= htmlspecialchars(number_format($don['amount'], 2)) ?> ADA
                </div>
                <div class="date-badge">
                  ⏳ Limite : <?= htmlspecialchars($don['dateLimit'] !== '0000-00-00' ? date('d/m/Y', strtotime($don['dateLimit'])) : 'Aucune') ?>
                </div>
              </div>

              <div class="don-body">
                <h3 class="don-title"><?= htmlspecialchars($don['motif']) ?></h3>
                <p class="don-desc"><?= htmlspecialchars($don['benefit']) ?></p>
              </div>
            </div>

            <div class="don-footer">
              <div>
                <span class="benefit-lbl">Statut</span>
                <span class="benefit-val" style="color: var(--success);">● Actif</span>
              </div>
              <button class="btn-participer" onclick="participerDon(<?= $don['id'] ?>)">Support</button>
            </div>

          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-box">
        <p>Aucune demande de don n'a été publiée pour le moment.</p>
      </div>
    <?php endif; ?>

  </div>
</div>

<script>
function participerDon(id) {
    // Pour connecter la logique avec les portefeuilles Cardano (Lucid, MeshJS, Cardano Wallet)
    alert("Donation transaction integration for request ID: " + id);
}
</script>
</body>
</html>