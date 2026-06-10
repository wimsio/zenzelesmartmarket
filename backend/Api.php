<?php
// ===== ZENZELE — API ROUTER =====
// Single entry point: /php/api.php?route=auth|profiles|nfts|donations|training|audio
// Or use individual files directly.

require_once("../php/config.php");

$route = $_GET['route'] ?? '';

switch ($route) {
    case 'auth':      require __DIR__ . '/auth.php';      break;
    case 'profiles':  require __DIR__ . '/profiles.php';  break;
    case 'nfts':      require __DIR__ . '/nfts.php';      break;
    case 'donations': require __DIR__ . '/donations.php'; break;
    case 'training':  require __DIR__ . '/training.php';  break;
    case 'audio':     require __DIR__ . '/audio.php';     break;
    case 'health':
        jsonOk([
            'status'   => 'ok',
            'app'      => 'Zenzele Smart Market API',
            'version'  => '1.0.0',
            'network'  => CARDANO_NETWORK,
            'time'     => date('c'),
        ]);
        break;
    default:
        jsonOk([
            'app'      => 'Zenzele Smart Market API',
            'version'  => '1.0.0',
            'routes'   => [
                'GET  /php/api.php?route=health'                         => 'Health check',
                'POST /php/api.php?route=auth&action=register'           => 'Register',
                'POST /php/api.php?route=auth&action=login'              => 'Login',
                'GET  /php/api.php?route=auth&action=me'                 => 'Get current user (auth)',
                'GET  /php/api.php?route=profiles'                       => 'List/search entrepreneurs',
                'GET  /php/api.php?route=profiles&id=X'                  => 'Get profile',
                'PUT  /php/api.php?route=profiles&action=update'         => 'Update own profile (auth)',
                'POST /php/api.php?route=profiles&action=follow'         => 'Follow/unfollow (auth)',
                'POST /php/api.php?route=profiles&action=like'           => 'Like/unlike (auth)',
                'POST /php/api.php?route=profiles&action=audio'          => 'Save audio URL (auth)',
                'POST /php/api.php?route=nfts&action=mint'               => 'Mint NFT (auth)',
                'GET  /php/api.php?route=nfts&user_id=X'                 => 'Get user NFTs',
                'GET  /php/api.php?route=nfts&id=X'                      => 'Get single NFT',
                'POST /php/api.php?route=donations&action=donate'        => 'Send donation (auth)',
                'GET  /php/api.php?route=donations&user_id=X'            => 'Donations received',
                'GET  /php/api.php?route=donations&action=summary&user_id=X' => 'Donation summary',
                'POST /php/api.php?route=training&action=request'        => 'Request training (auth)',
                'GET  /php/api.php?route=training'                       => 'List training requests',
                'GET  /php/api.php?route=training&action=mentors'        => 'List mentors',
                'POST /php/api.php?route=training&action=mentor_on'      => 'Become a mentor (auth)',
                'POST /php/api.php?route=training&action=accept'         => 'Accept training request (auth)',
                'POST /php/api.php?route=audio'                          => 'Upload audio file (auth, multipart)',
                'DELETE /php/api.php?route=audio'                        => 'Delete audio (auth)',
            ]
        ]);
}