<?php
// ===== ZENZELE — NFT API =====
// POST /php/nfts.php?action=mint   → mint NFT (auth)
// GET  /php/nfts.php?user_id=X     → get NFTs for a user
// GET  /php/nfts.php?id=X          → get single NFT
// DELETE /php/nfts.php?id=X        → delete own NFT (auth)

require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

// ---- MINT NFT ----
if ($action === 'mint') {
    requireMethod('POST');
    $user = requireAuth($pdo);
    $b    = getBody();

    $title       = sanitize($b['title']       ?? '');
    $description = sanitize($b['description'] ?? '');
    $category    = sanitize($b['category']    ?? 'Business Achievement');
    $supportGoal = sanitize($b['support_goal'] ?? '');
    $icon        = sanitize($b['icon']        ?? '🏆');
    $image       = sanitize($b['image']       ?? '');

    if (!$title || !$description) jsonErr('title and description are required');

    // Simulate Cardano NFT minting
    // In production: call Haskell/Plutus smart contract via CLI or Lucid
    $policyId  = 'policy_' . bin2hex(random_bytes(8));
    $assetName = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', $title)) . '_NFT';
    $txHash    = 'txhash_' . bin2hex(random_bytes(16));

    $metadata = json_encode([
        'name'         => $title,
        'description'  => $description,
        'image'        => $image ?: 'ipfs://QmPlaceholder',
        'category'     => $category,
        'support_goal' => $supportGoal,
        'owner'        => $user['wallet'] ?? 'addr1q_not_set',
        'owner_id'     => $user['id'],
        'platform'     => 'Zenzele Smart Market',
        'blockchain'   => 'Cardano',
        'network'      => CARDANO_NETWORK,
        'timestamp'    => date('c'),
        '721'          => [
            $policyId => [
                $assetName => [
                    'name'        => $title,
                    'description' => $description,
                    'image'       => $image ?: 'ipfs://QmPlaceholder',
                    'mediaType'   => 'image/png',
                ]
            ]
        ]
    ]);

    $stmt = $pdo->prepare('
        INSERT INTO nfts (user_id, title, description, icon, category, support_goal, policy_id, asset_name, tx_hash, metadata)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmt->execute([$user['id'], $title, $description, $icon, $category, $supportGoal, $policyId, $assetName, $txHash, $metadata]);
    $nftId = (int)$pdo->lastInsertId();

    jsonOk([
        'id'         => $nftId,
        'title'      => $title,
        'icon'       => $icon,
        'policy_id'  => $policyId,
        'asset_name' => $assetName,
        'tx_hash'    => $txHash,
        'network'    => CARDANO_NETWORK,
        'metadata'   => json_decode($metadata, true),
        'message'    => 'NFT minted successfully on ' . CARDANO_NETWORK
    ], 201);
}

// ---- GET NFTs BY USER ----
if ($method === 'GET' && isset($_GET['user_id']) && !$action) {
    $userId = (int)$_GET['user_id'];
    $stmt   = $pdo->prepare('SELECT id, title, description, icon, category, support_goal, policy_id, asset_name, tx_hash, minted_at FROM nfts WHERE user_id = ? ORDER BY minted_at DESC');
    $stmt->execute([$userId]);
    jsonOk($stmt->fetchAll());
}

// ---- GET SINGLE NFT ----
if ($method === 'GET' && isset($_GET['id']) && !$action) {
    $id   = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT n.*, u.name AS owner_name, u.wallet AS owner_wallet FROM nfts n JOIN users u ON u.id = n.user_id WHERE n.id = ?');
    $stmt->execute([$id]);
    $nft = $stmt->fetch();
    if (!$nft) jsonErr('NFT not found', 404);
    $nft['metadata'] = json_decode($nft['metadata'], true);
    jsonOk($nft);
}

// ---- DELETE NFT ----
if ($method === 'DELETE' && isset($_GET['id'])) {
    $user = requireAuth($pdo);
    $id   = (int)$_GET['id'];
    $stmt = $pdo->prepare('SELECT user_id FROM nfts WHERE id = ?');
    $stmt->execute([$id]);
    $nft = $stmt->fetch();
    if (!$nft) jsonErr('NFT not found', 404);
    if ($nft['user_id'] !== $user['id']) jsonErr('Forbidden', 403);
    $pdo->prepare('DELETE FROM nfts WHERE id = ?')->execute([$id]);
    jsonOk(['deleted' => true]);
}

jsonErr('Unknown action or method', 404);
