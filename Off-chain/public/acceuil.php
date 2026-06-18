<?php 
require_once 'header/username.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — ZenZele Smart Market</title>

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style2.css">
  <!-- Tabler Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.31.0/dist/tabler-icons.min.css">

</head>
<body>

  <!-- ═══════════════════════════════════════
       SIDEBAR
  ════════════════════════════════════════ -->
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
      <a href="formation.php" class="sidebar-item">
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

      <a href="nft.php" class="sidebar-item">
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
        <div class="sidebar-user-name"> <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?> </div>
        <div class="sidebar-user-role">Pro Learner</div>
      </div>
      <div class="sidebar-online"></div>
    </div>

  </aside>

  <!-- ═══════════════════════════════════════
       MAIN
  ════════════════════════════════════════ -->
  <main class="main">

    <!-- Topbar -->
    <div class="topbar">
      <div class="topbar-title">Hello, <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?> 👋</div>
      <!-- <div class="topbar-title"> <?php echo isset($_SESSION['wallet_address']) ? $_SESSION['wallet_address'] : 'No wallet address'; ?> 👋</div> -->
      <div class="topbar-date">Tuesday, 26 May 2026</div>
      <div class="topbar-actions">
        <div class="topbar-btn notif"><i class="ti ti-bell"></i></div>
        <div class="topbar-btn"><i class="ti ti-search"></i></div>
        <div class="topbar-btn"><i class="ti ti-message-circle"></i></div>
      </div>
    </div>

    <!-- Content -->
    <div class="content">

      <!-- ── Greeting ── -->
      <div class="greeting">
        <div>
          <h2>Welcome to <span>ZenZele</span> Smart Market</h2>
          <p class="greeting-sub">Learn, give and create value on the Cardano blockchain</p>
          <div class="greeting-chips">
            <div class="chip chip-gold"><i class="ti ti-plus"></i> New training</div>
            <div class="chip chip-green"><i class="ti ti-heart"></i> Make a donation</div>
            <div class="chip chip-purple"><i class="ti ti-sparkles"></i> Create an NFT</div>
          </div>
        </div>
        <!-- Pattern kente décoratif -->
        <div class="kente-deco" aria-hidden="true">
          <div class="kente-col">
            <div class="kente-cell" style="background:#F5A623"></div>
            <div class="kente-cell" style="background:#1ECFBE"></div>
            <div class="kente-cell" style="background:#E8593C"></div>
            <div class="kente-cell" style="background:#9B59FF"></div>
          </div>
          <div class="kente-col">
            <div class="kente-cell" style="background:#9B59FF"></div>
            <div class="kente-cell" style="background:#F5A623"></div>
            <div class="kente-cell" style="background:#1ECFBE"></div>
            <div class="kente-cell" style="background:#E8593C"></div>
          </div>
          <div class="kente-col">
            <div class="kente-cell" style="background:#E8593C"></div>
            <div class="kente-cell" style="background:#9B59FF"></div>
            <div class="kente-cell" style="background:#F5A623"></div>
            <div class="kente-cell" style="background:#1ECFBE"></div>
          </div>
          <div class="kente-col">
            <div class="kente-cell" style="background:#1ECFBE"></div>
            <div class="kente-cell" style="background:#E8593C"></div>
            <div class="kente-cell" style="background:#9B59FF"></div>
            <div class="kente-cell" style="background:#F5A623"></div>
          </div>
        </div>
      </div>

      <!-- ── Stats row ── -->
      <div class="stats-row">

        <div class="stat-card gold" onclick="location.href='dons.html'">
          <div class="stat-icon gold"><i class="ti ti-coin"></i></div>
          <div class="stat-value">12 450</div>
          <div class="stat-label">ADA received in donations</div>
          <div class="stat-delta delta-up">
            <i class="ti ti-trending-up" style="font-size:12px"></i> +18% this month
          </div>
        </div>

        <div class="stat-card green" onclick="location.href='formations.html'">
          <div class="stat-icon green"><i class="ti ti-book"></i></div>
          <div class="stat-value">7</div>
          <div class="stat-label">Trainings taken</div>
          <div class="stat-delta delta-up">
            <i class="ti ti-trending-up" style="font-size:12px"></i> 3 en cours
          </div>
        </div>

        <div class="stat-card purple" onclick="location.href='nfts.html'">
          <div class="stat-icon purple"><i class="ti ti-photo"></i></div>
          <div class="stat-value">24</div>
          <div class="stat-label">NFTs created</div>
          <div class="stat-delta delta-up">
            <i class="ti ti-trending-up" style="font-size:12px"></i> 2 vendus hier
          </div>
        </div>

        <div class="stat-card teal">
          <div class="stat-icon teal"><i class="ti ti-users"></i></div>
          <div class="stat-value">1 836</div>
          <div class="stat-label">Active donors</div>
          <div class="stat-delta delta-up">
            <i class="ti ti-trending-up" style="font-size:12px"></i> +52 cette semaine
          </div>
        </div>

      </div>

      <!-- ── Bottom grid ── -->
      <div class="bottom-grid">

        <!-- Colonne gauche : formations + chart -->
        <div class="card">
          <div class="card-header">
            <span class="card-title">
              <i class="ti ti-book-open" style="color:var(--gold)"></i>
              Ongoing trainings
            </span>
            <a href="formations.html" class="card-action">
              View all <i class="ti ti-chevron-right" style="font-size:11px"></i>
            </a>
          </div>

          <!-- Formation 1 -->
          <div class="formation-item">
            <div class="f-thumb" style="background:rgba(245,166,35,.12)">🎵</div>
            <div class="f-info">
              <div class="f-title">Afrobeat music production</div>
              <div class="f-meta">12 modules · Dr. Segun Ade</div>
              <div class="f-bar-wrap">
                <div class="f-bar" style="width:72%;background:var(--gold)"></div>
              </div>
            </div>
            <div class="f-pct" style="color:var(--gold)">72%</div>
          </div>

          <!-- Formation 2 -->
          <div class="formation-item">
            <div class="f-thumb" style="background:rgba(46,204,143,.12)">💻</div>
            <div class="f-info">
              <div class="f-title">Cardano & Plutus development</div>
              <div class="f-meta">8 modules · Eng. Fatima Diallo</div>
              <div class="f-bar-wrap">
                <div class="f-bar" style="width:45%;background:var(--green)"></div>
              </div>
            </div>
            <div class="f-pct" style="color:var(--green)">45%</div>
          </div>

          <!-- Formation 3 -->
          <div class="formation-item">
            <div class="f-thumb" style="background:rgba(155,89,255,.12)">🎨</div>
            <div class="f-info">
              <div class="f-title">Digital art & African NFT</div>
              <div class="f-meta">6 modules · Artist Kemi B.</div>
              <div class="f-bar-wrap">
                <div class="f-bar" style="width:20%;background:var(--purple)"></div>
              </div>
            </div>
            <div class="f-pct" style="color:var(--purple)">20%</div>
          </div>

          <!-- Mini bar chart dons hebdo -->
          <div class="chart-section">
            <div class="chart-header">
              <span class="chart-label">Weekly donations (ADA)</span>
              <span class="chart-total">+2,340 ADA</span>
            </div>
            <div class="bars">
              <div class="bar" style="height:30%;background:rgba(245,166,35,.4)">
                <div class="bar-tip">Mon: 320 ADA</div>
              </div>
              <div class="bar" style="height:55%;background:rgba(245,166,35,.5)">
                <div class="bar-tip">Tue: 580 ADA</div>
              </div>
              <div class="bar" style="height:40%;background:rgba(245,166,35,.45)">
                <div class="bar-tip">Wed: 430 ADA</div>
              </div>
              <div class="bar" style="height:75%;background:rgba(245,166,35,.65)">
                <div class="bar-tip">Thu: 800 ADA</div>
              </div>
              <div class="bar" style="height:60%;background:rgba(245,166,35,.55)">
                <div class="bar-tip">Fri: 640 ADA</div>
              </div>
              <div class="bar" style="height:90%;background:linear-gradient(180deg,var(--gold),var(--orange))">
                <div class="bar-tip">Sat: 960 ADA</div>
              </div>
              <div class="bar" style="height:45%;background:rgba(245,166,35,.4)">
                <div class="bar-tip">Sun: 480 ADA</div>
              </div>
            </div>
            <div class="bar-labels">
              <span>Mon</span><span>Tue</span><span>Wed</span>
              <span>Thu</span><span>Fri</span><span>Sat</span><span>Sun</span>
            </div>
          </div>
        </div>

        <!-- Colonne droite -->
        <div class="right-col">

          <!-- Dons récents -->
          <div class="card">
            <div class="card-header">
              <span class="card-title">
                <i class="ti ti-heart" style="color:var(--orange)"></i>
                Recent donations
              </span>
              <a href="dons.html" class="card-action">
                View all <i class="ti ti-chevron-right" style="font-size:11px"></i>
              </a>
            </div>

            <div class="don-item">
              <div class="don-avatar" style="background:rgba(245,166,35,.15);color:var(--gold)">MK</div>
              <div class="don-info">
                <div class="don-name">Moussa Kouyaté</div>
                <div class="don-desc">Music production</div>
              </div>
              <div class="don-amount">+250 ₳</div>
            </div>

            <div class="don-item">
              <div class="don-avatar" style="background:rgba(46,204,143,.12);color:var(--green)">FO</div>
              <div class="don-info">
                <div class="don-name">Fatou Ouédraogo</div>
                <div class="don-desc">Cardano training</div>
              </div>
              <div class="don-amount">+500 ₳</div>
            </div>

            <div class="don-item">
              <div class="don-avatar" style="background:rgba(155,89,255,.12);color:var(--purple)">IB</div>
              <div class="don-info">
                <div class="don-name">Ibrahim Ba</div>
                <div class="don-desc">Digital NFT art</div>
              </div>
              <div class="don-amount">+120 ₳</div>
            </div>

          </div>

          <!-- NFTs récents -->
          <div class="card">
            <div class="card-header">
                <span class="card-title">
                <i class="ti ti-sparkles" style="color:var(--purple)"></i>
                Recent NFTs
              </span>
            </div>

            <!-- Cardano status -->
            <div class="chain-status">
              <div class="chain-dot"></div>
                <div class="chain-text">
                Cardano <span>· Mainnet · Slot 124,837,221</span>
              </div>
            </div>

            <div class="nft-mini">
              <div class="nft-img" style="background:rgba(245,166,35,.12)">🥁</div>
              <div class="nft-info">
                <div class="nft-name">Djembe Soul #07</div>
                <div class="nft-artist">by Aminata K.</div>
              </div>
              <div class="nft-price">
                340 <span class="ada-pill"><i class="ti ti-coin"></i> ADA</span>
              </div>
            </div>

            <div class="nft-mini">
              <div class="nft-img" style="background:rgba(30,207,190,.1)">🌍</div>
              <div class="nft-info">
                <div class="nft-name">Afrika Vision #12</div>
                <div class="nft-artist">by Kemi B.</div>
              </div>
              <div class="nft-price">
                880 <span class="ada-pill"><i class="ti ti-coin"></i> ADA</span>
              </div>
            </div>

          </div>

        </div><!-- /right-col -->
      </div><!-- /bottom-grid -->

    </div><!-- /content -->
  </main><!-- /main -->

  <script>
    /* ── Navigation active ── */
    document.querySelectorAll('.sidebar-item').forEach(item => {
      item.addEventListener('click', function () {
        document.querySelectorAll('.sidebar-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
      });
    });

    /* ── Quick action chips ── */
    document.querySelectorAll('.chip').forEach(chip => {
      chip.addEventListener('click', function () {
        const label = this.textContent.trim();
        if (label.toLowerCase().includes('training'))  location.href = 'formations.html';
        if (label.toLowerCase().includes('donation'))  location.href = 'dons.html';
        if (label.includes('NFT'))        location.href = 'nfts.html';
      });
    });

    /* ── Stat cards cliquables ── */
    document.querySelectorAll('.stat-card').forEach(card => {
      card.style.cursor = 'pointer';
    });

    /* ── Dynamic date in the topbar ── */
    (function () {
      const el = document.querySelector('.topbar-date');
      if (!el) return;
      const now = new Date();
      const opts = { weekday:'long', day:'numeric', month:'long', year:'numeric' };
      el.textContent = now.toLocaleDateString('en-GB', opts)
        .replace(/^\w/, c => c.toUpperCase());
    })();
  </script>

</body>
</html>
