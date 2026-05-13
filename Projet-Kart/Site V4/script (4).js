// ── HORLOGE TOPBAR ──
function tick() {
  const pad = n => String(n).padStart(2, '0');
  const now = new Date();
  document.getElementById('clock').textContent =
    `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
}
setInterval(tick, 1000); tick();

// ── NAVIGATION TABS ──
function showTab(name) {
  const names = ['accueil', 'session', 'conditions', 'appexterne'];
  document.querySelectorAll('.tab').forEach((t, i) => {
    t.classList.toggle('active', names[i] === name);
  });
  document.querySelectorAll('.content').forEach(el => el.classList.remove('active'));
  document.getElementById('page-' + name).classList.add('active');
  if (name === 'conditions') setTimeout(() => { drawSparkline(); drawSessionsChart(); }, 0);
  if (name === 'accueil')    setTimeout(() => drawSessionsChart(), 0);
}

// ── HORLOGE ACCUEIL ──
function tickAccueil() {
  const pad = n => String(n).padStart(2, '0');
  const now = new Date();
  document.getElementById('accueil-clock').textContent =
    `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
  const days   = ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'];
  const months = ['janvier','février','mars','avril','mai','juin','juillet','août','septembre','octobre','novembre','décembre'];
  document.getElementById('accueil-date').textContent =
    `${days[now.getDay()]} ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()}`;
}
setInterval(tickAccueil, 1000); tickAccueil();

// ── DONNÉES GLOBALES ──
let pilotesData    = [];
let historiqueData = [];

// ── GRAPHIQUE RECORDS PAR PILOTE ──
function drawSessionsChart() {
  const canvas = document.getElementById('sessionsChart');
  if (!canvas || pilotesData.length === 0) return;
  const parent = canvas.parentElement;
  const W = parent.clientWidth - 32;
  const H = Math.max(parent.clientHeight - 44, 120);
  canvas.width = W; canvas.height = H;
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, W, H);

  const data   = pilotesData.map(p => ({ name: p.nom, val: parseInt(p.record) }));
  const minVal = Math.min(...data.map(d => d.val));
  const maxVal = Math.max(...data.map(d => d.val));
  const n = data.length;
  const padL = 8, padR = 8, padT = 10, padB = 36;
  const chartW = W - padL - padR;
  const chartH = H - padT - padB;
  const barW = Math.floor(chartW / n * 0.55);
  const gap  = chartW / n;

  ctx.strokeStyle = '#e8e6e0'; ctx.lineWidth = 1;
  [0.25, 0.5, 0.75, 1].forEach(r => {
    const y = padT + chartH - r * chartH;
    ctx.beginPath(); ctx.moveTo(padL, y); ctx.lineTo(W - padR, y); ctx.stroke();
  });

  data.forEach((d, i) => {
    const x      = padL + gap * i + (gap - barW) / 2;
    const ratio  = maxVal === minVal ? 0.8 : 1 - (d.val - minVal) / (maxVal - minVal) * 0.8;
    const normH  = ratio * chartH;
    const y      = padT + (chartH - normH);
    const isBest = d.val === minVal;
    const r2 = 4;
    ctx.fillStyle = isBest ? '#2d9e6b' : '#e8e6e0';
    ctx.beginPath();
    ctx.moveTo(x + r2, y); ctx.lineTo(x + barW - r2, y);
    ctx.arcTo(x + barW, y, x + barW, y + r2, r2);
    ctx.lineTo(x + barW, padT + chartH); ctx.lineTo(x, padT + chartH);
    ctx.lineTo(x, y + r2); ctx.arcTo(x, y, x + r2, y, r2);
    ctx.closePath(); ctx.fill();

    ctx.fillStyle = isBest ? '#2d9e6b' : '#9a9890';
    ctx.font = `${isBest ? '500' : '400'} 10px DM Mono, monospace`;
    ctx.textAlign = 'center';
    ctx.fillText((d.val / 1000).toFixed(1) + 's', x + barW / 2, y - 4);
    ctx.fillStyle = '#1a1a1a';
    ctx.font = `${isBest ? '500' : '400'} 10px DM Sans, sans-serif`;
    ctx.fillText(d.name, x + barW / 2, H - padB + 14);
  });
}
window.addEventListener('resize', drawSessionsChart);

// ── PRÉVISIONS (statique) ──
const forecastData = [
  { offset: 0,   icon: '☀️',  temp: '7.4°', rain: '0%'  },
  { offset: 30,  icon: '☀️',  temp: '8.1°', rain: '0%'  },
  { offset: 60,  icon: '🌤️', temp: '9.0°', rain: '5%'  },
  { offset: 90,  icon: '🌤️', temp: '9.8°', rain: '5%'  },
  { offset: 120, icon: '⛅',  temp: '10.5°', rain: '10%' },
];
function buildForecastRow(containerId, small) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const now = new Date();
  forecastData.forEach(f => {
    const t  = new Date(now.getTime() + f.offset * 60000);
    const hh = String(t.getHours()).padStart(2, '0');
    const mm = String(t.getMinutes()).padStart(2, '0');
    const label = f.offset === 0 ? 'Maintenant' : `${hh}:${mm}`;
    const fs = small ? '9' : '10'; const ico = small ? '16' : '20'; const val = small ? '11' : '13';
    container.innerHTML += `
      <div style="flex:1; background:var(--bg); border-radius:${small?8:10}px; padding:${small?'7px 4px':'10px 6px'}; text-align:center; display:flex; flex-direction:column; gap:${small?3:4}px;">
        <div style="font-size:${fs}px; font-weight:500; letter-spacing:0.06em; color:var(--muted);">${label}</div>
        <div style="font-size:${ico}px;">${f.icon}</div>
        <div style="font-family:'DM Mono',monospace; font-size:${val}px;">${f.temp}</div>
        <div style="font-size:${fs}px; color:var(--muted);">${f.rain}</div>
      </div>`;
  });
}
buildForecastRow('accueil-forecastRow', true);
buildForecastRow('forecastRow', false);

// ── SPARKLINE ──
function drawSparkline() {
  const canvas = document.getElementById('sparkline');
  if (!canvas) return;
  const W = canvas.parentElement.clientWidth - 44;
  const H = canvas.parentElement.clientHeight - 44 || 70;
  canvas.width = W; canvas.height = H;
  const ctx = canvas.getContext('2d');

  const chargeData = historiqueData.length > 1 ? historiqueData.map(h => parseInt(h.pourcentagebatterie)) : [65,65];
  const tempData   = historiqueData.length > 1 ? historiqueData.map(h => parseInt(h.temperaturepiste))    : [25,25];

  const pad = 4, pts = chargeData.length;
  const xStep    = (W - pad * 2) / (pts - 1);
  const chargeMin = Math.min(...chargeData) - 5;
  const chargeMax = Math.max(...chargeData) + 5;
  const tempMin   = Math.min(...tempData) - 2;
  const tempMax   = Math.max(...tempData) + 2;
  const toX  = i => pad + i * xStep;
  const toYc = v => H - pad - ((v - chargeMin) / (chargeMax - chargeMin || 1)) * (H - pad * 2);
  const toYt = v => H - pad - ((v - tempMin)   / (tempMax   - tempMin   || 1)) * (H - pad * 2);

  ctx.clearRect(0, 0, W, H);
  ctx.strokeStyle = '#e8e6e0'; ctx.lineWidth = 1;
  [0.25, 0.5, 0.75].forEach(r => {
    const y = pad + r * (H - pad * 2);
    ctx.beginPath(); ctx.moveTo(pad, y); ctx.lineTo(W - pad, y); ctx.stroke();
  });

  ctx.beginPath();
  ctx.moveTo(toX(0), toYc(chargeData[0]));
  chargeData.forEach((v, i) => ctx.lineTo(toX(i), toYc(v)));
  ctx.lineTo(toX(pts - 1), H); ctx.lineTo(toX(0), H); ctx.closePath();
  ctx.fillStyle = 'rgba(45,158,107,0.08)'; ctx.fill();

  ctx.beginPath();
  chargeData.forEach((v, i) => i === 0 ? ctx.moveTo(toX(i), toYc(v)) : ctx.lineTo(toX(i), toYc(v)));
  ctx.strokeStyle = '#2d9e6b'; ctx.lineWidth = 2; ctx.lineJoin = 'round'; ctx.setLineDash([]); ctx.stroke();

  ctx.beginPath();
  tempData.forEach((v, i) => i === 0 ? ctx.moveTo(toX(i), toYt(v)) : ctx.lineTo(toX(i), toYt(v)));
  ctx.strokeStyle = '#c98a1a'; ctx.lineWidth = 2; ctx.setLineDash([4, 3]); ctx.stroke();
  ctx.setLineDash([]);

  [[toX(pts-1), toYc(chargeData[pts-1]), '#2d9e6b'], [toX(pts-1), toYt(tempData[pts-1]), '#c98a1a']].forEach(([x, y, col]) => {
    ctx.beginPath(); ctx.arc(x, y, 4, 0, Math.PI * 2);
    ctx.fillStyle = col; ctx.fill();
    ctx.strokeStyle = '#fff'; ctx.lineWidth = 1.5; ctx.stroke();
  });
}
drawSparkline();
window.addEventListener('resize', drawSparkline);

// ── CHRONOMÈTRE PERSISTANT ──
// Le temps est calculé à partir d'un epoch stocké dans localStorage,
// ce qui permet de survivre aux rechargements et changements d'onglet.

let sessionInterval = null;

const LS_START  = 'kart_session_start';   // epoch ms du démarrage
const LS_PAUSED = 'kart_session_paused';  // ms écoulées au moment de la pause
const LS_STATE  = 'kart_session_state';   // 'running' | 'paused' | 'idle'
const LS_PILOTE = 'kart_session_pilote';  // nom du pilote actif

function msToDisplay(ms) {
  const m  = String(Math.floor(ms / 60000)).padStart(2, '0');
  const s  = String(Math.floor((ms % 60000) / 1000)).padStart(2, '0');
  const cs = String(Math.floor((ms % 1000) / 10)).padStart(2, '0');
  return `${m}:${s}:${cs}`;
}

function getElapsedMs() {
  const state = localStorage.getItem(LS_STATE);
  if (state === 'running') {
    const start = parseInt(localStorage.getItem(LS_START) || '0');
    return Date.now() - start;
  } else if (state === 'paused') {
    return parseInt(localStorage.getItem(LS_PAUSED) || '0');
  }
  return 0;
}

function updateTimer() {
  const el = document.getElementById('sessionTimer');
  if (el) el.textContent = msToDisplay(getElapsedMs());
}

function _setRunningUI(nomPilote) {
  const nom = nomPilote || localStorage.getItem(LS_PILOTE) || '—';
  document.getElementById('btnStart').style.display    = 'none';
  document.getElementById('btnStop').style.display     = 'block';
  document.getElementById('btnReset').style.display    = 'none';
  document.getElementById('sessionBadge').style.display = 'flex';
  document.getElementById('sessionTimer').style.color  = 'var(--green)';
  document.getElementById('btnStart').textContent      = '▶ Démarrer';
  document.getElementById('btnStart').style.background = 'var(--green)';
  document.getElementById('btnStart').onclick          = ouvrirModal;
  const badgeNom = document.getElementById('badgePiloteNom');
  if (badgeNom) badgeNom.textContent = nom;
}

function _setPausedUI() {
  document.getElementById('btnStop').style.display     = 'none';
  document.getElementById('btnStart').style.display    = 'block';
  document.getElementById('btnStart').textContent      = '▶ Reprendre';
  document.getElementById('btnStart').style.background = '#3a8fd4';
  document.getElementById('btnStart').onclick          = resumeSession;
  document.getElementById('btnReset').style.display    = 'block';
  document.getElementById('sessionBadge').style.display = 'none';
  document.getElementById('sessionTimer').style.color  = 'var(--muted)';
}

// ── MODALE SAISIE PILOTE ──
function ouvrirModal() {
  const input = document.getElementById('inputPilote');
  input.value = '';
  document.getElementById('modalPilote').style.display = 'flex';
  setTimeout(() => input.focus(), 50);
}

function fermerModal() {
  document.getElementById('modalPilote').style.display = 'none';
}

function confirmerPilote() {
  const nom = document.getElementById('inputPilote').value.trim();
  if (!nom) {
    document.getElementById('inputPilote').style.border = '1px solid var(--red)';
    return;
  }
  fermerModal();
  startSession(nom);
}

// Fermer la modale en cliquant en dehors
document.getElementById('modalPilote').addEventListener('click', function(e) {
  if (e.target === this) fermerModal();
});

function startSession(nomPilote) {
  localStorage.setItem(LS_START, Date.now());
  localStorage.removeItem(LS_PAUSED);
  localStorage.setItem(LS_STATE, 'running');
  localStorage.setItem(LS_PILOTE, nomPilote);

  _setRunningUI(nomPilote);
  updateTimer();
  clearInterval(sessionInterval);
  sessionInterval = setInterval(updateTimer, 10);

  fetch('session_pilote.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=demarrer&nom=' + encodeURIComponent(nomPilote)
  }).catch(() => {});
}

function resumeSession() {
  const pausedMs = parseInt(localStorage.getItem(LS_PAUSED) || '0');
  localStorage.setItem(LS_START, Date.now() - pausedMs);
  localStorage.removeItem(LS_PAUSED);
  localStorage.setItem(LS_STATE, 'running');

  _setRunningUI();
  clearInterval(sessionInterval);
  sessionInterval = setInterval(updateTimer, 10);
}

function stopSession() {
  const elapsed = getElapsedMs();
  localStorage.setItem(LS_PAUSED, elapsed);
  localStorage.setItem(LS_STATE, 'paused');

  clearInterval(sessionInterval);
  sessionInterval = null;
  _setPausedUI();
  updateTimer();
}

function resetSession() {
  localStorage.removeItem(LS_START);
  localStorage.removeItem(LS_PAUSED);
  localStorage.removeItem(LS_PILOTE);
  localStorage.setItem(LS_STATE, 'idle');

  clearInterval(sessionInterval);
  sessionInterval = null;

  document.getElementById('sessionTimer').textContent  = '00:00:00';
  document.getElementById('sessionTimer').style.color  = 'var(--text)';
  document.getElementById('btnReset').style.display    = 'none';
  document.getElementById('btnStart').textContent      = '▶ Démarrer';
  document.getElementById('btnStart').style.background = 'var(--green)';
  document.getElementById('btnStart').onclick          = ouvrirModal;

  fetch('session_pilote.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: 'action=arreter'
  }).catch(() => {});
}

// ── RESTAURATION AU CHARGEMENT ──
(function restoreSession() {
  const state = localStorage.getItem(LS_STATE);
  if (state === 'running') {
    _setRunningUI();
    updateTimer();
    clearInterval(sessionInterval);
    sessionInterval = setInterval(updateTimer, 10);
  } else if (state === 'paused') {
    _setPausedUI();
    updateTimer();
  }
})();

// ── LIVE UPDATE toutes les 4s ──
function fetchLive() {
  fetch('api_data.php')
    .then(res => res.json())
    .then(data => {
      const c      = data.capteur || {};
      const pct    = c.pourcentagebatterie ?? '—';
      const tempB  = c.temperaturebatterie ?? '—';
      const volt   = c.tensionbatterie ?? '—';
      const ampere = c.intensitebatterie ?? '—';
      const humid  = c.humiditepiste ?? '—';
      const tempP  = c.temperaturepiste ?? '—';

      const tempBadge  = t => t < 15 ? ['Piste froide','bad'] : t < 25 ? ['Piste correcte','warn'] : ['Piste chaude','ok'];
      const humidBadge = h => h < 50 ? ['Idéale','ok'] : h < 70 ? ['Modérée','warn'] : ['Humide','bad'];
      const batBadge   = p => p >= 60 ? ['Bonne charge','ok'] : p >= 30 ? ['Charge moyenne','warn'] : ['Charge faible','bad'];
      const batClass   = p => p >= 60 ? 'high' : p >= 30 ? 'medium' : 'low';
      const setText    = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
      const setBadge   = (id, text, cls) => { const el = document.getElementById(id); if (el) { el.textContent = text; el.className = 'delta ' + cls; }};
      const setWidth   = (id, p, cls) => { const el = document.getElementById(id); if (el) { el.style.width = p + '%'; el.className = 'charge-bar-fill ' + cls; }};

      setText('temp-piste-val', tempP); setText('ambiant-temp', tempP);
      if (tempP !== '—') { const [l,c2] = tempBadge(parseInt(tempP)); setBadge('temp-piste-badge', l, c2); setBadge('ambiant-badge', l, c2); }

      setText('humid-val', humid); setText('accueil-humid', humid);
      if (humid !== '—') { const [l,c2] = humidBadge(parseInt(humid)); setBadge('humid-badge', l, c2); setBadge('accueil-humid-badge', l, c2); }

      setText('batt-charge-val', pct);
      setText('session-charge-pct', pct + '%');
      const condPctEl = document.getElementById('cond-charge-pct');
      if (condPctEl) condPctEl.innerHTML = pct + '<span>%</span>';
      if (pct !== '—') {
        const p = parseInt(pct);
        setWidth('session-charge-fill', p, batClass(p));
        setWidth('cond-charge-fill', p, batClass(p));
        const [l,c2] = batBadge(p); setBadge('batt-charge-badge', l, c2);
      }

      setText('batt-temp-val', tempB); setText('sess-temp-val', tempB);
      if (tempB !== '—') {
        const t = parseInt(tempB);
        const [l,c2] = t > 35 ? ['Surchauffe','bad'] : t > 28 ? ['Chaude','warn'] : ['Normale','ok'];
        setBadge('batt-temp-badge', l, c2);
      }

      setText('batt-volt-val', volt);  setText('sess-volt-val', volt);
      setText('batt-amp-val', ampere); setText('sess-amp-val', ampere);

      if (data.historique && data.historique.length > 1) {
        historiqueData = data.historique;
        drawSparkline();
      }
      if (data.pilotes && data.pilotes.length > 0) {
        pilotesData = data.pilotes;
        drawSessionsChart();
      }
    })
    .catch(err => console.warn('Erreur fetch live:', err));
}

fetchLive();
setInterval(fetchLive, 4000);