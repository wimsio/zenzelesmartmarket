// ===== ZENZELE SMART MARKET — MAIN.JS =====
// Core helpers used by all pages.
// api-client.js (loaded first) overrides registerUser() and loginUser()
// to use real PHP when the backend is live. These are the localStorage fallbacks.

// ── Auth helpers ──────────────────────────────────────────────────────────
function getUser() {
  try { return JSON.parse(localStorage.getItem('zenzele_user')); } catch { return null; }
}
function setUser(u) { localStorage.setItem('zenzele_user', JSON.stringify(u)); }
function logout()   { localStorage.removeItem('zenzele_user'); window.location.href = '/index.html'; }

// ── Profile DB (localStorage) ─────────────────────────────────────────────
function getProfiles() {
  try { return JSON.parse(localStorage.getItem('zenzele_profiles')) || getDemoProfiles(); }
  catch { return getDemoProfiles(); }
}
function saveProfile(p) {
  const all = getProfiles();
  const idx = all.findIndex(x => x.id === p.id);
  if (idx >= 0) all[idx] = p; else all.push(p);
  localStorage.setItem('zenzele_profiles', JSON.stringify(all));
}

// ── Demo seed data ────────────────────────────────────────────────────────
function getDemoProfiles() {
  return [
    { id:1,  name:"Thabo Mokoena",   city:"Soweto",    country:"ZA", category:"Carpentry",       bio:"I build custom furniture from reclaimed wood. 10 years turning scrap into art.", avatar:"🧑🏾‍💼", wallet:"addr1q8xyz_demo", followers:124, likes:89,  views:432,  donations:3,  skills:["Woodwork","Furniture","Reclaimed Wood"], nfts:[{title:"Master Carpenter NFT",icon:"🔨"}], trainingWanted:"Business Management", openForWork:true,  hasMentor:false },
    { id:2,  name:"Aisha Ndlovu",    city:"Nairobi",   country:"KE", category:"Digital Marketing",bio:"Helping small businesses grow online with social media & SEO.",                  avatar:"👩🏿‍💻", wallet:"addr1q9abc_demo", followers:256, likes:198, views:870,  donations:7,  skills:["SEO","Social Media","Content"],          nfts:[{title:"Digital Marketer NFT",icon:"📱"}], trainingWanted:"AI & Automation",    openForWork:true,  hasMentor:true  },
    { id:3,  name:"Ravi Sharma",     city:"Mumbai",    country:"IN", category:"Coding",           bio:"Full-stack developer building fintech apps for rural communities on Cardano.",   avatar:"👨🏽‍💻", wallet:"addr1q7def_demo", followers:88,  likes:71,  views:310,  donations:2,  skills:["PHP","JavaScript","MySQL"],              nfts:[],                                         trainingWanted:"Cardano/Blockchain", openForWork:false, hasMentor:false },
    { id:4,  name:"Nomvula Dlamini", city:"Durban",    country:"ZA", category:"Fashion",          bio:"Creating Afrocentric fashion that celebrates our roots. Each piece tells a story.",avatar:"👗",    wallet:"addr1q5ghi_demo", followers:340, likes:290, views:1200, donations:12, skills:["Sewing","Design","Afrocentric Style"],   nfts:[{title:"Fashion NFT",icon:"✂️"},{title:"Collection 2026",icon:"👗"}], trainingWanted:"E-Commerce", openForWork:true, hasMentor:false },
    { id:5,  name:"Carlos Mendes",   city:"São Paulo", country:"BR", category:"Music Production", bio:"Producing Afrobeats and Brazilian funk beats. Looking for global collaborations.",avatar:"🎵",    wallet:"addr1q3jkl_demo", followers:178, likes:155, views:620,  donations:5,  skills:["Beat Making","Mixing","Afrobeats"],       nfts:[{title:"Producer NFT",icon:"🎹"}],          trainingWanted:"Music Business",     openForWork:true,  hasMentor:false },
    { id:6,  name:"Fatima Al-Hassan",city:"Lagos",     country:"NG", category:"Farming",          bio:"Organic vegetable farmer using smart irrigation. Feeding 3 communities.",        avatar:"🌱",    wallet:"addr1q1mno_demo", followers:92,  likes:67,  views:380,  donations:4,  skills:["Organic Farming","Irrigation","Agri-Tech"],nfts:[],                                        trainingWanted:"Funding & Grants",   openForWork:false, hasMentor:true  },
  ];
}

// ── localStorage registration (fallback when backend is offline) ──────────
function registerLocalStorage(formData) {
  const profiles = getProfiles();

  // Check for duplicate email
  if (profiles.find(p => p.email && p.email.toLowerCase() === (formData.email||'').toLowerCase())) {
    return { success: false, error: 'An account with this email already exists.' };
  }

  const newUser = {
    id:              Date.now(),
    name:            formData.name || '',
    email:           (formData.email || '').toLowerCase(),
    // NOTE: password stored in plain text — localStorage demo only.
    // Real backend (auth.php) uses PHP password_hash(). Never do this in production.
    password:        formData.password || '',
    country:         formData.country || '',
    city:            formData.city || '',
    category:        formData.category || '',
    bio:             formData.bio || '',
    skills:          typeof formData.skills === 'string'
                       ? formData.skills.split(',').map(s=>s.trim()).filter(Boolean)
                       : (formData.skills || []),
    wallet:          formData.wallet || '',
    trainingWanted:  formData.training_wanted || formData.trainingWanted || '',
    training_wanted: formData.training_wanted || formData.trainingWanted || '',
    openForWork:     !!(formData.open_for_work || formData.openForWork),
    open_for_work:   !!(formData.open_for_work || formData.openForWork),
    avatar:          '🧑🏾‍💼',
    followers: 0, likes: 0, views: 0, donations: 0,
    nfts: [],
    createdAt: new Date().toISOString(),
  };

  saveProfile(newUser);
  setUser(newUser);
  return { success: true, user: newUser };
}

// ── localStorage login (fallback when backend is offline) ─────────────────
function loginLocalStorage(email, password) {
  const profiles = getProfiles();
  const user = profiles.find(
    p => p.email && p.email.toLowerCase() === email.toLowerCase() && p.password === password
  );
  if (!user) return { success: false, error: 'Invalid email or password. Try the Demo Login button.' };
  setUser(user);
  return { success: true, user };
}

// ── Donation simulator ────────────────────────────────────────────────────
function simulateDonation(profileId, amount, message) {
  return {
    success:   true,
    txRef:     'tx_' + Math.random().toString(36).substr(2,12).toUpperCase(),
    policy_id: null,
    amount,
    message,
    timestamp: new Date().toISOString(),
    network:   'Cardano Testnet (simulated)'
  };
}

// ── NFT mint simulator ────────────────────────────────────────────────────
function mintNFT(data) {
  const policyId = 'policy_' + Math.random().toString(36).substr(2,16);
  return {
    success:    true,
    policy_id:  policyId,
    asset_name: (data.title||'NFT').replace(/\s+/g,'').toUpperCase() + '_NFT',
    tx_hash:    'txhash_' + Math.random().toString(36).substr(2,20),
    network:    'Cardano Testnet (simulated)',
    metadata: {
      name:        data.title,
      description: data.description,
      image:       data.image || 'ipfs://QmPlaceholder',
      category:    data.category,
      owner:       data.wallet,
      timestamp:   new Date().toISOString(),
      supportGoal: data.supportGoal,
      platform:    'Zenzele Smart Market',
      blockchain:  'Cardano',
      icon:        data.icon || '🏆',
    }
  };
}

// ── Like / Follow (localStorage) ──────────────────────────────────────────
function toggleLike(profileId) {
  const liked = JSON.parse(localStorage.getItem('zenzele_liked') || '[]');
  const idx = liked.indexOf(profileId);
  if (idx >= 0) liked.splice(idx, 1); else liked.push(profileId);
  localStorage.setItem('zenzele_liked', JSON.stringify(liked));
  return idx < 0;
}
function toggleFollow(profileId) {
  const followed = JSON.parse(localStorage.getItem('zenzele_followed') || '[]');
  const idx = followed.indexOf(profileId);
  if (idx >= 0) followed.splice(idx, 1); else followed.push(profileId);
  localStorage.setItem('zenzele_followed', JSON.stringify(followed));
  return idx < 0;
}

// ── Social sharing ────────────────────────────────────────────────────────
function getShareUrl(profileId) {
  return `${window.location.origin}/pages/profile.html?id=${profileId}`;
}
function shareToWhatsApp(id, name) {
  const u = encodeURIComponent(getShareUrl(id));
  const t = encodeURIComponent(`Check out ${name} on Zenzele Smart Market! 🚀`);
  window.open(`https://wa.me/?text=${t}%20${u}`, '_blank');
}
function shareToFacebook(id) {
  window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(getShareUrl(id))}`, '_blank');
}
function shareToX(id, name) {
  const u = encodeURIComponent(getShareUrl(id));
  const t = encodeURIComponent(`Check out ${name} on @ZenzeleMarket 🚀 #BeSelFReliant #Cardano`);
  window.open(`https://twitter.com/intent/tweet?text=${t}&url=${u}`, '_blank');
}
function shareToTelegram(id, name) {
  const u = encodeURIComponent(getShareUrl(id));
  const t = encodeURIComponent(`Check out ${name} on Zenzele Smart Market!`);
  window.open(`https://t.me/share/url?url=${u}&text=${t}`, '_blank');
}

// ── Toast notification ────────────────────────────────────────────────────
function showToast(msg, type = 'success') {
  // Remove any existing toast
  document.querySelectorAll('.zenzele-toast').forEach(t => t.remove());
  const t = document.createElement('div');
  t.className = `alert alert-${type} zenzele-toast`;
  t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;min-width:260px;max-width:380px;box-shadow:0 8px 32px rgba(0,0,0,0.15)';
  t.textContent = msg;
  document.body.appendChild(t);
  setTimeout(() => t.remove(), 3500);
}

// ── Audio recorder ────────────────────────────────────────────────────────
let mediaRecorder, audioChunks = [], isRecording = false;

async function startRecording(statusEl, btnEl) {
  try {
    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
    mediaRecorder  = new MediaRecorder(stream);
    audioChunks    = [];
    mediaRecorder.ondataavailable = e => audioChunks.push(e.data);
    mediaRecorder.start();
    isRecording    = true;
    if (statusEl) statusEl.textContent = '🔴 Recording... Click again to stop';
    if (btnEl)    btnEl.classList.add('recording');
  } catch (e) {
    showToast('Microphone access denied. Please allow microphone access in your browser.', 'error');
  }
}

function stopRecording(statusEl, btnEl, audioEl) {
  return new Promise(resolve => {
    mediaRecorder.onstop = () => {
      const blob = new Blob(audioChunks, { type: 'audio/webm' });
      const url  = URL.createObjectURL(blob);
      if (audioEl) { audioEl.src = url; audioEl.style.display = 'block'; }
      if (statusEl) statusEl.textContent = '✅ Recording complete. Preview below, then click Save.';
      if (btnEl)    btnEl.classList.remove('recording');
      isRecording = false;
      resolve(blob);
    };
    mediaRecorder.stop();
    mediaRecorder.stream.getTracks().forEach(t => t.stop());
  });
}
