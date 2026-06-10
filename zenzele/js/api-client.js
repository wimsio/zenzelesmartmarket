// ===== ZENZELE SMART MARKET — API CLIENT =====
// Bridges the frontend to the PHP backend.
// Automatically detects if backend is running.
// Falls back to localStorage if PHP is not reachable.

// ── API base URL ──────────────────────────────────────────────────────────
// This points to your XAMPP server.
// For local XAMPP:  http://localhost/zenzele-smart-market/php/api.php
// For live hosting: https://yourdomain.com/php/api.php
const API_BASE = 'http://localhost/zenzele-smart-market/php/api.php';

// ── Token helpers ─────────────────────────────────────────────────────────
function getToken()  { return localStorage.getItem('zenzele_token'); }
function setToken(t) { localStorage.setItem('zenzele_token', t); }
function clearToken(){ localStorage.removeItem('zenzele_token'); localStorage.removeItem('zenzele_user'); }

// ── Core fetch wrapper ────────────────────────────────────────────────────
async function apiCall(route, action, method = 'GET', body = null, isMultipart = false) {
    let url = `${API_BASE}?route=${route}`;
    if (action) url += `&action=${action}`;

    const headers = {};
    const token = getToken();
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const options = { method, headers };

    if (body) {
        if (isMultipart) {
            options.body = body; // FormData — browser sets Content-Type
        } else {
            headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }
    }

    const res  = await fetch(url, options);
    const json = await res.json();

    if (!res.ok || !json.success) {
        throw new Error(json.error || `Server error ${res.status}`);
    }
    return json.data;
}

// ── GET with query params ─────────────────────────────────────────────────
async function apiGet(route, params = {}) {
    let url = `${API_BASE}?route=${route}`;
    for (const [k, v] of Object.entries(params)) {
        if (v !== undefined && v !== '') url += `&${encodeURIComponent(k)}=${encodeURIComponent(v)}`;
    }
    const headers = {};
    const token = getToken();
    if (token) headers['Authorization'] = `Bearer ${token}`;

    const res  = await fetch(url, { headers });
    const json = await res.json();
    if (!res.ok || !json.success) throw new Error(json.error || `Server error ${res.status}`);
    return json.data;
}

// ══════════════════════════════════════════════════════════════════════════
// AUTH
// ══════════════════════════════════════════════════════════════════════════
const Auth = {
    async register(fields) {
        const data = await apiCall('auth', 'register', 'POST', fields);
        setToken(data.token);
        return data;
    },
    async login(email, password) {
        const data = await apiCall('auth', 'login', 'POST', { email, password });
        setToken(data.token);
        setUser(data.user);
        return data;
    },
    async me() {
        return await apiCall('auth', 'me', 'GET');
    },
    logout() {
        clearToken();
        window.location.href = '/zenzele-smart-market/index.html';
    }
};

// ══════════════════════════════════════════════════════════════════════════
// PROFILES
// ══════════════════════════════════════════════════════════════════════════
const Profiles = {
    async list(params = {}) {
        return await apiGet('profiles', params);
    },
    async get(id) {
        return await apiGet('profiles', { id });
    },
    async update(fields) {
        return await apiCall('profiles', 'update', 'PUT', fields);
    },
    async follow(profileId) {
        return await apiCall('profiles', 'follow', 'POST', { profile_id: profileId });
    },
    async like(profileId) {
        return await apiCall('profiles', 'like', 'POST', { profile_id: profileId });
    }
};

// ══════════════════════════════════════════════════════════════════════════
// NFTs
// ══════════════════════════════════════════════════════════════════════════
const NFTs = {
    async mint(data) {
        return await apiCall('nfts', 'mint', 'POST', {
            title:        data.title,
            description:  data.description,
            category:     data.category,
            support_goal: data.supportGoal,
            icon:         data.icon,
            image:        data.image || ''
        });
    },
    async getByUser(userId) {
        return await apiGet('nfts', { user_id: userId });
    }
};

// ══════════════════════════════════════════════════════════════════════════
// DONATIONS
// ══════════════════════════════════════════════════════════════════════════
const Donations = {
    async send(toUserId, amountAda, message, fromName) {
        return await apiCall('donations', 'donate', 'POST', {
            to_user_id: toUserId,
            amount_ada: amountAda,
            message,
            from_name: fromName
        });
    },
    async received(userId) {
        return await apiGet('donations', { user_id: userId });
    },
    async summary(userId) {
        return await apiGet('donations', { action: 'summary', user_id: userId });
    }
};

// ══════════════════════════════════════════════════════════════════════════
// TRAINING
// ══════════════════════════════════════════════════════════════════════════
const Training = {
    async request(area, details) {
        return await apiCall('training', 'request', 'POST', { area, details });
    },
    async mentors() {
        return await apiGet('training', { action: 'mentors' });
    },
    async accept(requestId) {
        return await apiCall('training', 'accept', 'POST', { request_id: requestId });
    }
};

// ══════════════════════════════════════════════════════════════════════════
// AUDIO UPLOAD
// ══════════════════════════════════════════════════════════════════════════
const AudioUpload = {
    async upload(audioBlob) {
        const form = new FormData();
        form.append('audio', audioBlob, 'pitch.webm');
        return await apiCall('audio', null, 'POST', form, true);
    }
};

// ══════════════════════════════════════════════════════════════════════════
// BACKEND CONNECTION CHECK
// ══════════════════════════════════════════════════════════════════════════
let BACKEND_LIVE = false;

async function checkBackendConnection() {
    try {
        const data = await apiGet('health');
        console.log('%c[Zenzele] ✅ Backend connected: ' + data.app, 'color:green;font-weight:bold');
        BACKEND_LIVE = true;
        return true;
    } catch (err) {
        console.warn('%c[Zenzele] ⚠️ Backend offline — using localStorage demo mode. Error: ' + err.message, 'color:orange');
        BACKEND_LIVE = false;
        return false;
    }
}

// Run check on every page load
checkBackendConnection();

// ══════════════════════════════════════════════════════════════════════════
// UNIFIED REGISTER — tries PHP first, falls back to localStorage
// ══════════════════════════════════════════════════════════════════════════
async function registerUser(formData) {
    // Wait briefly for the backend check to complete
    await new Promise(r => setTimeout(r, 600));

    if (BACKEND_LIVE) {
        try {
            const result = await Auth.register(formData);
            // Cache basic info locally for quick reads
            const userObj = {
                id:       result.user_id,
                name:     formData.name,
                email:    formData.email,
                category: formData.category,
                city:     formData.city,
                country:  formData.country,
                bio:      formData.bio,
                skills:   typeof formData.skills === 'string'
                            ? formData.skills.split(',').map(s => s.trim()).filter(Boolean)
                            : (formData.skills || []),
                wallet:        formData.wallet || '',
                trainingWanted:formData.training_wanted || '',
                openForWork:   !!(formData.open_for_work),
                avatar:        '🧑🏾‍💼',
                followers: 0, likes: 0, views: 0, donations: 0, nfts: [],
            };
            setUser(userObj);
            console.log('[Zenzele] ✅ Registered via PHP/MySQL — user_id:', result.user_id);
            return { success: true, user: userObj, source: 'mysql' };
        } catch (err) {
            console.error('[Zenzele] PHP registration failed:', err.message);
            return { success: false, error: err.message };
        }
    } else {
        // Backend not available — use localStorage
        console.warn('[Zenzele] Using localStorage registration (demo mode)');
        return registerLocalStorage(formData);
    }
}

// ══════════════════════════════════════════════════════════════════════════
// UNIFIED LOGIN — tries PHP first, falls back to localStorage
// ══════════════════════════════════════════════════════════════════════════
async function loginUser(email, password) {
    await new Promise(r => setTimeout(r, 600));

    if (BACKEND_LIVE) {
        try {
            const result = await Auth.login(email, password);
            console.log('[Zenzele] ✅ Logged in via PHP/MySQL');
            return { success: true, user: result.user, source: 'mysql' };
        } catch (err) {
            return { success: false, error: err.message };
        }
    } else {
        return loginLocalStorage(email, password);
    }
}

// ══════════════════════════════════════════════════════════════════════════
// UNIFIED PROFILE LOAD — tries PHP first, falls back to localStorage
// ══════════════════════════════════════════════════════════════════════════
async function loadProfiles(filters = {}) {
    if (BACKEND_LIVE) {
        try {
            const result = await Profiles.list(filters);
            return result;
        } catch {
            return { profiles: getProfiles(), total: getProfiles().length };
        }
    }
    return { profiles: getProfiles(), total: getProfiles().length };
}

// ══════════════════════════════════════════════════════════════════════════
// UNIFIED DONATION
// ══════════════════════════════════════════════════════════════════════════
async function sendDonation(toUserId, amountAda, message, fromName) {
    if (BACKEND_LIVE) {
        try { return await Donations.send(toUserId, amountAda, message, fromName); }
        catch { return simulateDonation(toUserId, amountAda, message); }
    }
    return simulateDonation(toUserId, amountAda, message);
}

// ══════════════════════════════════════════════════════════════════════════
// UNIFIED NFT MINT
// ══════════════════════════════════════════════════════════════════════════
async function doMintNFTReal(data) {
    if (BACKEND_LIVE) {
        try { return await NFTs.mint(data); }
        catch { return mintNFT(data); }
    }
    return mintNFT(data);
}

// ══════════════════════════════════════════════════════════════════════════
// UNIFIED AUDIO SAVE
// ══════════════════════════════════════════════════════════════════════════
async function saveAudioPitch(blob) {
    if (BACKEND_LIVE) {
        try { return await AudioUpload.upload(blob); }
        catch (e) {
            const url = URL.createObjectURL(blob);
            return { audio_url: url };
        }
    }
    const url = URL.createObjectURL(blob);
    const user = getUser();
    if (user) { user.audioUrl = url; setUser(user); }
    return { audio_url: url };
}
