<?php
// ===== ZENZELE — PROFILES API =====
// GET    /php/profiles.php                   → list all (search/filter)
// GET    /php/profiles.php?id=123            → get one profile
// PUT    /php/profiles.php?action=update     → update own profile (auth)
// POST   /php/profiles.php?action=follow     → follow/unfollow (auth)
// POST   /php/profiles.php?action=like       → like/unlike (auth)
// POST   /php/profiles.php?action=audio      → save audio pitch URL (auth)
// GET    /php/profiles.php?action=stats&id=X → get profile stats

require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ---- LIST / SEARCH ----
if ($method === 'GET' && !$action && !isset($_GET['id'])) {
    $search   = '%' . sanitize($_GET['search']   ?? '') . '%';
    $category = sanitize($_GET['category'] ?? '');
    $country  = sanitize($_GET['country']  ?? '');
    $openWork = $_GET['open_for_work'] ?? '';
    $page     = max(1, (int)($_GET['page'] ?? 1));
    $limit    = min(50, max(1, (int)($_GET['limit'] ?? 12)));
    $offset   = ($page - 1) * $limit;

    $where = ['1=1'];
    $params = [];

    if ($_GET['search'] ?? '') {
        $where[] = '(u.name LIKE ? OR u.bio LIKE ? OR u.city LIKE ? OR u.skills LIKE ?)';
        $params = array_merge($params, [$search, $search, $search, $search]);
    }
    if ($category) { $where[] = 'u.category = ?'; $params[] = $category; }
    if ($country)  { $where[] = 'u.country = ?';  $params[] = $country; }
    if ($openWork) { $where[] = 'u.open_for_work = 1'; }

    $whereStr = implode(' AND ', $where);

    $total = $pdo->prepare("SELECT COUNT(*) FROM users u WHERE $whereStr");
    $total->execute($params);
    $totalCount = (int)$total->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT u.id, u.name, u.city, u.country, u.category, u.bio, u.avatar,
               u.wallet, u.open_for_work, u.training_wanted, u.skills,
               u.followers, u.likes, u.views, u.audio_url, u.created_at,
               (SELECT COUNT(*) FROM nfts n WHERE n.user_id = u.id) AS nft_count,
               (SELECT COUNT(*) FROM donations d WHERE d.to_user_id = u.id) AS donation_count
        FROM users u
        WHERE $whereStr
        ORDER BY u.followers DESC, u.created_at DESC
        LIMIT ? OFFSET ?
    ");
    $stmt->execute(array_merge($params, [$limit, $offset]));
    $profiles = $stmt->fetchAll();

    foreach ($profiles as &$p) {
        $p['skills'] = json_decode($p['skills'] ?? '[]', true);
    }

    jsonOk([
        'profiles'    => $profiles,
        'total'       => $totalCount,
        'page'        => $page,
        'limit'       => $limit,
        'total_pages' => ceil($totalCount / $limit)
    ]);
}

// ---- GET ONE PROFILE ----
if ($method === 'GET' && isset($_GET['id']) && !$action) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare('
        SELECT u.id, u.name, u.city, u.country, u.category, u.bio, u.avatar,
               u.wallet, u.open_for_work, u.training_wanted, u.skills,
               u.followers, u.likes, u.views, u.audio_url, u.created_at
        FROM users u WHERE u.id = ?
    ');
    $stmt->execute([$id]);
    $profile = $stmt->fetch();
    if (!$profile) jsonErr('Profile not found', 404);

    // Increment views
    $pdo->prepare('UPDATE users SET views = views + 1 WHERE id = ?')->execute([$id]);
    $profile['views']++;
    $profile['skills'] = json_decode($profile['skills'] ?? '[]', true);

    // Attach NFTs
    $nfts = $pdo->prepare('SELECT id, title, icon, category, policy_id, tx_hash, minted_at FROM nfts WHERE user_id = ? ORDER BY minted_at DESC');
    $nfts->execute([$id]);
    $profile['nfts'] = $nfts->fetchAll();

    // Attach recent donations
    $dons = $pdo->prepare('SELECT from_name, amount_ada, message, tx_ref, donated_at FROM donations WHERE to_user_id = ? ORDER BY donated_at DESC LIMIT 10');
    $dons->execute([$id]);
    $profile['recent_donations'] = $dons->fetchAll();

    // Training requests
    $tr = $pdo->prepare('SELECT area, details, status, created_at FROM training_requests WHERE user_id = ? ORDER BY created_at DESC');
    $tr->execute([$id]);
    $profile['training_requests'] = $tr->fetchAll();

    jsonOk($profile);
}

// ---- UPDATE PROFILE ----
if ($action === 'update') {
    requireMethod('PUT');
    $user = requireAuth($pdo);
    $b    = getBody();

    $fields = [];
    $params = [];

    $allowed = ['name','city','country','category','bio','wallet','training_wanted','open_for_work','avatar'];
    foreach ($allowed as $f) {
        if (array_key_exists($f, $b)) {
            $fields[] = "$f = ?";
            $params[] = $f === 'open_for_work' ? (int)$b[$f] : sanitize((string)$b[$f]);
        }
    }
    if (isset($b['skills'])) {
        $fields[] = 'skills = ?';
        $params[] = is_array($b['skills'])
            ? json_encode(array_map('trim', $b['skills']))
            : json_encode(array_map('trim', explode(',', $b['skills'])));
    }

    if (!$fields) jsonErr('No fields to update');
    $params[] = $user['id'];

    $pdo->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($params);

    $stmt = $pdo->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$user['id']]);
    $updated = $stmt->fetch();
    unset($updated['password']);
    $updated['skills'] = json_decode($updated['skills'] ?? '[]', true);

    jsonOk($updated);
}

// ---- SAVE AUDIO URL ----
if ($action === 'audio') {
    requireMethod('POST');
    $user = requireAuth($pdo);
    $b    = getBody();
    $url  = sanitize($b['audio_url'] ?? '');
    if (!$url) jsonErr('audio_url required');
    $pdo->prepare('UPDATE users SET audio_url = ? WHERE id = ?')->execute([$url, $user['id']]);
    jsonOk(['audio_url' => $url]);
}

// ---- FOLLOW / UNFOLLOW ----
if ($action === 'follow') {
    requireMethod('POST');
    $user   = requireAuth($pdo);
    $b      = getBody();
    $target = (int)($b['profile_id'] ?? 0);
    if (!$target || $target === $user['id']) jsonErr('Invalid profile_id');

    $check = $pdo->prepare('SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?');
    $check->execute([$user['id'], $target]);

    if ($check->fetch()) {
        // Unfollow
        $pdo->prepare('DELETE FROM follows WHERE follower_id = ? AND following_id = ?')->execute([$user['id'], $target]);
        $pdo->prepare('UPDATE users SET followers = GREATEST(0, followers - 1) WHERE id = ?')->execute([$target]);
        jsonOk(['following' => false, 'message' => 'Unfollowed']);
    } else {
        // Follow
        $pdo->prepare('INSERT INTO follows (follower_id, following_id) VALUES (?, ?)')->execute([$user['id'], $target]);
        $pdo->prepare('UPDATE users SET followers = followers + 1 WHERE id = ?')->execute([$target]);
        jsonOk(['following' => true, 'message' => 'Now following']);
    }
}

// ---- LIKE / UNLIKE ----
if ($action === 'like') {
    requireMethod('POST');
    $user   = requireAuth($pdo);
    $b      = getBody();
    $target = (int)($b['profile_id'] ?? 0);
    if (!$target) jsonErr('Invalid profile_id');

    $check = $pdo->prepare('SELECT 1 FROM likes WHERE user_id = ? AND profile_id = ?');
    $check->execute([$user['id'], $target]);

    if ($check->fetch()) {
        $pdo->prepare('DELETE FROM likes WHERE user_id = ? AND profile_id = ?')->execute([$user['id'], $target]);
        $pdo->prepare('UPDATE users SET likes = GREATEST(0, likes - 1) WHERE id = ?')->execute([$target]);
        jsonOk(['liked' => false]);
    } else {
        $pdo->prepare('INSERT INTO likes (user_id, profile_id) VALUES (?, ?)')->execute([$user['id'], $target]);
        $pdo->prepare('UPDATE users SET likes = likes + 1 WHERE id = ?')->execute([$target]);
        jsonOk(['liked' => true]);
    }
}

// ---- STATS ----
if ($action === 'stats') {
    requireMethod('GET');
    $id = (int)($_GET['id'] ?? 0);
    if (!$id) jsonErr('id required');
    $stmt = $pdo->prepare('SELECT followers, likes, views,
        (SELECT COUNT(*) FROM nfts WHERE user_id = ?) AS nfts,
        (SELECT COUNT(*) FROM donations WHERE to_user_id = ?) AS donations,
        (SELECT COALESCE(SUM(amount_ada),0) FROM donations WHERE to_user_id = ?) AS total_ada
        FROM users WHERE id = ?');
    $stmt->execute([$id, $id, $id, $id]);
    $stats = $stmt->fetch();
    if (!$stats) jsonErr('Profile not found', 404);
    jsonOk($stats);
}

jsonErr('Unknown action or method', 404);
