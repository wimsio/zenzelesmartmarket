// ===== ZENZELE SMART MARKET — CARDANO INTEGRATION =====
// lucid-cardano@0.10.11 with full WASM patch for post-Chang Preprod

const CARDANO_CONFIG = {
  network:         'Preprod',
  blockfrostApiKey: localStorage.getItem('zenzele_blockfrost_key') || 'preprod_YOUR_KEY_HERE',
  blockfrostUrl:   'https://cardano-preprod.blockfrost.io/api/v0',
};

let lucidInstance      = null;
let connectedWallet    = null;
let connectedAddress   = null;
let LucidModule        = null;
let _isConnecting      = false;
let _autoReconnectDone = false;

const SUPPORTED_WALLETS = [
  { id: 'lace',   name: 'Lace',   icon: '🟣', installUrl: 'https://www.lace.io' },
  { id: 'eternl', name: 'Eternl', icon: '🔵', installUrl: 'https://eternl.io' },
  { id: 'nami',   name: 'Nami',   icon: '🟠', installUrl: 'https://namiwallet.io' },
  { id: 'flint',  name: 'Flint',  icon: '🔴', installUrl: 'https://flint-wallet.com' },
  { id: 'vespr',  name: 'Vespr',  icon: '🟢', installUrl: 'https://vespr.xyz' },
];

function getAvailableWallets() {
  return SUPPORTED_WALLETS.filter(w => !!window.cardano?.[w.id]);
}

async function loadLucidModule() {
  if (LucidModule) return LucidModule;
  if (window.__LucidMod) {
    LucidModule = window.__LucidMod;
    console.log('[Cardano] ✅ Lucid ready from window.__LucidMod');
    return LucidModule;
  }
  console.log('[Cardano] Waiting for Lucid module loader...');
  for (let i = 0; i < 100; i++) {
    await new Promise(r => setTimeout(r, 100));
    if (window.__LucidMod) {
      LucidModule = window.__LucidMod;
      console.log('[Cardano] ✅ Lucid loaded after', (i + 1) * 100, 'ms');
      return LucidModule;
    }
    if (window.__LucidError) throw new Error('Lucid failed: ' + window.__LucidError);
  }
  throw new Error('Lucid did not load within 10 seconds.');
}

function patchWasm(C) {
  try {
    if (C && C.UnitInterval) {
      C.UnitInterval.from_float = function(f) {
        const n = Math.round(Math.abs(f) * 10000000);
        return C.UnitInterval.new(
          C.BigNum.from_str(String(n)),
          C.BigNum.from_str('10000000')
        );
      };
      console.log('[Cardano] ✅ UnitInterval.from_float patched');
    }
    if (C && C.ExUnitPrices) {
      C.ExUnitPrices.from_float = function() {
        const memR  = C.UnitInterval.new(C.BigNum.from_str('577'),  C.BigNum.from_str('10000'));
        const stepR = C.UnitInterval.new(C.BigNum.from_str('721'),  C.BigNum.from_str('10000000'));
        return C.ExUnitPrices.new(memR, stepR);
      };
      console.log('[Cardano] ✅ ExUnitPrices.from_float patched');
    }
  } catch(e) {
    console.warn('[Cardano] WASM patch warning:', e.message);
  }
}

// All values are integers — no decimals anywhere to avoid ParseIntError in WASM BigNum
const SAFE_PROTOCOL_PARAMS = {
  minFeeA:              44,
  minFeeB:              155381,
  maxTxSize:            16384,
  maxValSize:           5000,
  keyDeposit:           BigInt(2000000),
  poolDeposit:          BigInt(500000000),
  maxTxExMem:           BigInt(14000000),
  maxTxExSteps:         BigInt(10000000000),
  coinsPerUtxoByte:     BigInt(4310),
  collateralPercentage: 150,
  maxCollateralInputs:  3,
  costModels: {
    PlutusV1: Object.fromEntries(Array.from({length: 166}, (_, i) => [String(i), 1])),
    PlutusV2: Object.fromEntries(Array.from({length: 175}, (_, i) => [String(i), 1])),
  }
};

async function initLucid() {
  if (lucidInstance) return lucidInstance;
  if (!CARDANO_CONFIG.blockfrostApiKey || CARDANO_CONFIG.blockfrostApiKey.includes('YOUR_KEY')) {
    throw new Error('Blockfrost API key not set. Enter it in Step 1 and click Save.');
  }
  const mod = await loadLucidModule();

  // Patch WASM before anything else
  patchWasm(mod.C);

  const provider = new mod.Blockfrost(
    CARDANO_CONFIG.blockfrostUrl,
    CARDANO_CONFIG.blockfrostApiKey
  );

  // Override getProtocolParameters to return our safe integer-only params
  provider.getProtocolParameters = async () => {
    console.log('[Cardano] Returning safe protocol params');
    return SAFE_PROTOCOL_PARAMS;
  };

  console.log('[Cardano] ✅ Provider patched BEFORE Lucid.new()');
  lucidInstance = await mod.Lucid.new(provider, CARDANO_CONFIG.network);
  console.log('[Cardano] ✅ Lucid initialised');
  return lucidInstance;
}

function stripCborPrefix(hex) {
  if (typeof hex !== 'string') return hex;
  if (hex.startsWith('59')) return hex.slice(6);
  if (hex.startsWith('58')) return hex.slice(4);
  return hex;
}

async function anyHexToAddress(hex) {
  if (!hex || hex === 'undefined') throw new Error('Address value is empty.');
  const mod = await loadLucidModule();
  if (typeof hex === 'string' && hex.startsWith('addr')) return hex;
  for (const candidate of [stripCborPrefix(hex), hex]) {
    try {
      const bytes = mod.fromHex(candidate);
      const addr  = mod.C.Address.from_bytes(bytes);
      const bech  = addr.to_bech32(undefined);
      if (bech && bech.startsWith('addr')) return bech;
    } catch(e) { /* try next */ }
  }
  throw new Error('Could not decode address from: ' + String(hex).substring(0, 20) + '...');
}

async function connectWallet(walletId) {
  if (_isConnecting) { console.warn('[Cardano] Already connecting.'); return null; }
  _isConnecting = true;
  try {
    const info = SUPPORTED_WALLETS.find(w => w.id === walletId);
    if (!info) throw new Error('Unknown wallet: ' + walletId);
    if (!window.cardano?.[walletId]) throw new Error(info.name + ' is not installed.');

    const walletApi = await window.cardano[walletId].enable();
    if (!walletApi) throw new Error('Wallet enable() returned nothing.');

    const lucid = await initLucid();
    lucid.selectWallet(walletApi);

    let address = null;

    if (!address) {
      try {
        const raw = await lucid.wallet.address();
        console.log('[Cardano] Method 1 raw:', String(raw).substring(0, 30));
        if (raw && String(raw) !== 'undefined') {
          address = String(raw).startsWith('addr') ? String(raw) : await anyHexToAddress(String(raw));
        }
      } catch(e) { console.warn('[Cardano] Method 1 failed:', e.message); }
    }

    if (!address) {
      try {
        const raw = await walletApi.getChangeAddress();
        console.log('[Cardano] Method 2 raw:', String(raw).substring(0, 30));
        if (raw && String(raw) !== 'undefined') address = await anyHexToAddress(String(raw));
      } catch(e) { console.warn('[Cardano] Method 2 failed:', e.message); }
    }

    if (!address) {
      try {
        const list = await walletApi.getUsedAddresses();
        if (list?.length > 0) address = await anyHexToAddress(String(list[0]));
      } catch(e) { console.warn('[Cardano] Method 3 failed:', e.message); }
    }

    if (!address) {
      try {
        const list = await walletApi.getUnusedAddresses();
        if (list?.length > 0) address = await anyHexToAddress(String(list[0]));
      } catch(e) { console.warn('[Cardano] Method 4 failed:', e.message); }
    }

    if (!address || !address.startsWith('addr')) {
      throw new Error('Address decode failed. Make sure Lace is set to Preprod.');
    }

    connectedAddress = address;
    connectedWallet  = walletId;

    const user = typeof getUser === 'function' ? getUser() : null;
    if (user) {
      user.wallet = connectedAddress;
      if (typeof setUser === 'function') setUser(user);
      if (typeof BACKEND_LIVE !== 'undefined' && BACKEND_LIVE && typeof Profiles !== 'undefined') {
        try { await Profiles.update({ wallet: connectedAddress }); } catch(e) {}
      }
    }

    localStorage.setItem('zenzele_last_wallet', walletId);
    console.log('[Cardano] ✅ Connected:', walletId, connectedAddress);
    return { success: true, address: connectedAddress, wallet: walletId, network: CARDANO_CONFIG.network };

  } finally {
    _isConnecting = false;
  }
}

async function tryAutoReconnect() {
  if (_autoReconnectDone) return false;
  _autoReconnectDone = true;
  const last = localStorage.getItem('zenzele_last_wallet');
  if (!last || !window.cardano?.[last]) return false;
  try {
    const ok = await window.cardano[last].isEnabled();
    if (ok) { await connectWallet(last); return true; }
  } catch(e) { localStorage.removeItem('zenzele_last_wallet'); }
  return false;
}

async function getWalletBalance() {
  if (!lucidInstance || !connectedWallet) throw new Error('No wallet connected.');
  const utxos = await lucidInstance.wallet.getUtxos();
  let lovelace = BigInt(0);
  for (const utxo of (utxos || [])) lovelace += BigInt(utxo.assets?.lovelace ?? 0);
  return { lovelace: lovelace.toString(), ada: (Number(lovelace) / 1_000_000).toFixed(6) };
}

function disconnectWallet(containerId) {
  lucidInstance = null; connectedWallet = null; connectedAddress = null;
  _autoReconnectDone = false;
  localStorage.removeItem('zenzele_last_wallet');
  const user = typeof getUser === 'function' ? getUser() : null;
  if (user) { user.wallet = ''; if (typeof setUser === 'function') setUser(user); }
  if (containerId) renderWalletButton(containerId);
  if (typeof showToast === 'function') showToast('Wallet disconnected.');
}

async function sendRealDonation(recipientAddress, adaAmount, message) {
  if (!lucidInstance || !connectedWallet) throw new Error('Connect your wallet first.');
  if (!recipientAddress?.startsWith('addr')) throw new Error('Invalid recipient address.');
  const lovelace = BigInt(Math.round(adaAmount * 1_000_000));
  if (lovelace < BigInt(1_000_000)) throw new Error('Minimum donation is 1 ADA.');
  const tx = await lucidInstance.newTx()
    .payToAddress(recipientAddress, { lovelace })
    .attachMetadata(674, { msg: ['Zenzele Smart Market Donation', (message || 'Community support').substring(0, 60)] })
    .complete();
  const signed = await tx.sign().complete();
  const txHash = await signed.submit();
  console.log('[Cardano] ✅ Donation tx:', txHash);
  return {
    success: true, tx_hash: txHash, tx_ref: txHash, amount_ada: adaAmount,
    network: CARDANO_CONFIG.network,
    explorer: `https://preprod.cardanoscan.io/transaction/${txHash}`
  };
}

async function mintRealNFT(nftData) {
  if (!lucidInstance || !connectedWallet) throw new Error('Connect your wallet first.');
  const { paymentCredential } = lucidInstance.utils.getAddressDetails(await lucidInstance.wallet.address());
  const mintingPolicy = lucidInstance.utils.nativeScriptFromJson({
    type: 'all', scripts: [{ type: 'sig', keyHash: paymentCredential.hash }]
  });
  const policyId  = lucidInstance.utils.mintingPolicyToId(mintingPolicy);
  const rawName   = nftData.title.replace(/\s+/g, '').substring(0, 32);
  const assetName = toHex(new TextEncoder().encode(rawName));
  const assetId   = policyId + assetName;
  const tx = await lucidInstance.newTx()
    .mintAssets({ [assetId]: BigInt(1) }, mintingPolicy)
    .attachMetadata(721, { [policyId]: { [rawName]: {
      name: nftData.title, description: nftData.description,
      image: nftData.image || 'ipfs://QmZenzelePlaceholder', mediaType: 'image/png',
      category: nftData.category || 'Business Achievement',
      platform: 'Zenzele Smart Market', owner: connectedAddress,
      supportGoal: nftData.supportGoal || '',
    }}})
    .complete();
  const signed = await tx.sign().complete();
  const txHash = await signed.submit();
  console.log('[Cardano] ✅ NFT minted:', txHash);
  return {
    success: true, policy_id: policyId, asset_name: assetName, asset_id: assetId,
    tx_hash: txHash, network: CARDANO_CONFIG.network,
    explorer: `https://preprod.cardanoscan.io/transaction/${txHash}`
  };
}

function toHex(bytes) {
  return Array.from(bytes).map(b => b.toString(16).padStart(2, '0')).join('');
}

function renderWalletButton(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return;
  if (connectedWallet && connectedAddress) {
    const short = connectedAddress.substring(0, 20) + '...' + connectedAddress.slice(-6);
    const info  = SUPPORTED_WALLETS.find(w => w.id === connectedWallet);
    container.innerHTML = `
      <div style="background:#d1fae5;border:1px solid #6ee7b7;border-radius:10px;padding:14px 16px">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
          <div>
            <div style="font-size:0.82rem;font-weight:700;color:#065f46">${info?.icon||'🔗'} ${info?.name||connectedWallet} Connected</div>
            <code style="font-size:0.72rem;color:#065f46;word-break:break-all">${short}</code>
          </div>
          <button onclick="disconnectWallet('${containerId}')"
            style="font-size:0.75rem;color:#991b1b;background:none;border:1px solid #fca5a5;border-radius:50px;padding:4px 12px;cursor:pointer">
            Disconnect
          </button>
        </div>
      </div>`;
    return;
  }
  const available = getAvailableWallets();
  if (!available.length) {
    container.innerHTML = `
      <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:10px;padding:14px">
        <div style="font-size:0.85rem;font-weight:600;color:#92400e;margin-bottom:8px">⚠️ No Cardano wallet detected</div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">
          ${SUPPORTED_WALLETS.map(w=>`<a href="${w.installUrl}" target="_blank"
            style="background:var(--purple);color:white;padding:7px 14px;border-radius:50px;font-size:0.78rem;font-weight:600;text-decoration:none">
            ${w.icon} Install ${w.name}</a>`).join('')}
        </div>
      </div>`;
    return;
  }
  container.innerHTML = `
    <div style="border:1px solid var(--border);border-radius:10px;padding:14px">
      <div style="font-size:0.85rem;font-weight:600;color:var(--purple);margin-bottom:12px">🔗 Connect Your Cardano Wallet</div>
      <div style="display:flex;gap:8px;flex-wrap:wrap" id="_wbtns_${containerId}">
        ${available.map(w=>`
          <button onclick="_doConnect('${w.id}','${containerId}')"
            style="background:var(--purple);color:white;border:none;padding:9px 18px;border-radius:50px;font-size:0.85rem;font-weight:600;cursor:pointer">
            ${w.icon} ${w.name}
          </button>`).join('')}
      </div>
      <div id="_wstatus_${containerId}" style="margin-top:10px;font-size:0.78rem;color:var(--text-muted)">
        Network: <strong>${CARDANO_CONFIG.network}</strong> · Testnet is free and safe
      </div>
    </div>`;
}

async function _doConnect(walletId, containerId) {
  const statusEl = document.getElementById(`_wstatus_${containerId}`);
  const btnsEl   = document.getElementById(`_wbtns_${containerId}`);
  if (statusEl) statusEl.innerHTML = '⏳ Connecting — approve in your wallet...';
  if (btnsEl)   btnsEl.style.opacity = '0.5';
  try {
    await connectWallet(walletId);
    renderWalletButton(containerId);
    if (typeof onWalletConnected === 'function') onWalletConnected(connectedAddress, walletId);
    if (typeof showToast === 'function') showToast('✅ ' + walletId + ' connected!');
  } catch(err) {
    if (statusEl) statusEl.innerHTML = `<span style="color:#991b1b">❌ ${err.message}</span>`;
    if (btnsEl)   btnsEl.style.opacity = '1';
    console.error('[Cardano] Connect error:', err);
  }
}

async function donateWithCardano(recipientAddress, adaAmount, message, fromName, toUserId) {
  if (connectedWallet && lucidInstance && recipientAddress?.startsWith('addr')) {
    const result = await sendRealDonation(recipientAddress, adaAmount, message);
    if (typeof BACKEND_LIVE !== 'undefined' && BACKEND_LIVE && typeof Donations !== 'undefined') {
      try { await Donations.send(toUserId, adaAmount, message, fromName); } catch(e) {}
    }
    return { ...result, real: true };
  }
  const sim = typeof sendDonation === 'function'
    ? await sendDonation(toUserId, adaAmount, message, fromName)
    : { success: true, tx_ref: 'sim_' + Date.now(), network: 'Simulated' };
  return { ...sim, real: false };
}

async function mintNFTWithCardano(nftData) {
  if (connectedWallet && lucidInstance) {
    const result = await mintRealNFT(nftData);
    if (typeof BACKEND_LIVE !== 'undefined' && BACKEND_LIVE && typeof NFTs !== 'undefined') {
      try { await NFTs.mint(nftData); } catch(e) {}
    }
    return { ...result, real: true };
  }
  const sim = typeof doMintNFTReal === 'function'
    ? await doMintNFTReal(nftData)
    : { policy_id: 'sim_' + Date.now(), tx_hash: 'sim_' + Date.now(), network: 'Simulated' };
  return { ...sim, real: false };
}

console.log('[Cardano] cardano.js loaded — lucid-cardano + WASM patch v2');