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

// ── GRAPHIQUE TOURS PAR PILOTE ──
const lapsData = [
  { name: 'Léa M.',     laps: 143 },
  { name: 'Marc L.',    laps: 287 },
  { name: 'Julien P.',  laps: 98  },
  { name: 'Sophie R.',  laps: 201 },
  { name: 'Thomas B.',  laps: 165 },
  { name: 'Antoine D.', laps: 234 },
];
const maxLaps = Math.max(...lapsData.map(d => d.laps));

function drawSessionsChart() {
  const canvas = document.getElementById('sessionsChart');
  if (!canvas) return;
  const parent = canvas.parentElement;
  const W = parent.clientWidth - 32;
  const H = Math.max(parent.clientHeight - 44, 120);
  canvas.width = W;
  canvas.height = H;
  const ctx = canvas.getContext('2d');
  ctx.clearRect(0, 0, W, H);

  const n = lapsData.length;
  const padL = 8, padR = 8, padT = 10, padB = 36;
  const chartW = W - padL - padR;
  const chartH = H - padT - padB;
  const barW = Math.floor(chartW / n * 0.55);
  const gap  = chartW / n;

  ctx.strokeStyle = '#e8e6e0';
  ctx.lineWidth = 1;
  [0.25, 0.5, 0.75, 1].forEach(r => {
    const y = padT + chartH - r * chartH;
    ctx.beginPath(); ctx.moveTo(padL, y); ctx.lineTo(W - padR, y); ctx.stroke();
    const val = Math.round(r * maxLaps);
    ctx.fillStyle = '#9a9890';
    ctx.font = '9px DM Mono, monospace';
    ctx.textAlign = 'right';
    ctx.fillText(val, padL - 2, y + 3);
  });

  lapsData.forEach((d, i) => {
    const x = padL + gap * i + (gap - barW) / 2;
    const barH = (d.laps / maxLaps) * chartH;
    const y = padT + chartH - barH;
    const isTop = d.laps === maxLaps;
    const r = 4;

    ctx.fillStyle = isTop ? '#2d9e6b' : '#e8e6e0';
    ctx.beginPath();
    ctx.moveTo(x + r, y);
    ctx.lineTo(x + barW - r, y);
    ctx.arcTo(x + barW, y, x + barW, y + r, r);
    ctx.lineTo(x + barW, y + barH);
    ctx.lineTo(x, y + barH);
    ctx.lineTo(x, y + r);
    ctx.arcTo(x, y, x + r, y, r);
    ctx.closePath();
    ctx.fill();

    ctx.fillStyle = isTop ? '#2d9e6b' : '#9a9890';
    ctx.font = `${isTop ? '500' : '400'} 11px DM Mono, monospace`;
    ctx.textAlign = 'center';
    ctx.fillText(d.laps, x + barW / 2, y - 4);

    ctx.fillStyle = '#1a1a1a';
    ctx.font = `${isTop ? '500' : '400'} 10px DM Sans, sans-serif`;
    ctx.textAlign = 'center';
    ctx.fillText(d.name.split(' ')[0], x + barW / 2, H - padB + 14);
    ctx.fillStyle = '#9a9890';
    ctx.font = '9px DM Sans, sans-serif';
    ctx.fillText(d.name.split(' ')[1] || '', x + barW / 2, H - padB + 25);
  });
}
drawSessionsChart();
window.addEventListener('resize', drawSessionsChart);

// ── TABLEAU RECORDS TOUS LES TEMPS ──
const allTimeRecords = [
  { rank:1,  name:'Marc L.',     time:51.3, date:'22 jan. 2025', cond:'☀️ Sec' },
  { rank:2,  name:'Antoine D.',  time:52.1, date:'14 mar. 2025', cond:'☀️ Sec' },
  { rank:3,  name:'Sophie R.',   time:52.8, date:'02 avr. 2025', cond:'☀️ Sec' },
  { rank:4,  name:'Thomas B.',   time:53.0, date:'08 jan. 2025', cond:'🌤️ Nuageux' },
  { rank:5,  name:'Marc L.',     time:53.2, date:'31 mar. 2025', cond:'☀️ Sec' },
  { rank:6,  name:'Léa M.',      time:53.5, date:'17 fév. 2025', cond:'☀️ Sec' },
  { rank:7,  name:'Antoine D.',  time:53.7, date:'22 fév. 2025', cond:'🌤️ Nuageux' },
  { rank:8,  name:'Julien P.',   time:53.9, date:'05 mar. 2025', cond:'☀️ Sec' },
  { rank:9,  name:'Sophie R.',   time:54.1, date:'19 avr. 2025', cond:'⛅ Couvert' },
  { rank:10, name:'Thomas B.',   time:54.3, date:'28 jan. 2025', cond:'☀️ Sec' },
];
const bestRecord  = allTimeRecords[0].time;
const recordsBody = document.getElementById('recordsBody');
allTimeRecords.forEach(r => {
  const gap     = (r.time - bestRecord).toFixed(1);
  const isFirst = r.rank === 1;
  const medal   = r.rank === 1 ? '🥇' : r.rank === 2 ? '🥈' : r.rank === 3 ? '🥉' : '';
  const bg        = isFirst ? '#edf8f3' : 'transparent';
  const timeColor = isFirst ? 'var(--green)' : 'var(--text)';
  recordsBody.innerHTML += `
    <tr style="background:${bg};">
      <td><span class="record-rank" style="color:${isFirst?'var(--green)':'var(--muted)'};">${medal || r.rank}</span></td>
      <td style="font-weight:${isFirst?'500':'400'}; color:${timeColor};">${r.name}</td>
      <td style="color:${timeColor}; font-weight:${isFirst?'500':'400'};">${r.time.toFixed(1)} s</td>
      <td style="color:${gap==='0.0'?'var(--green)':gap<=2?'var(--yellow)':'var(--muted)'};">${isFirst ? '—' : '+'+gap+' s'}</td>
      <td style="color:var(--muted); font-size:11px;">${r.date}</td>
      <td style="font-size:11px;">${r.cond}</td>
    </tr>`;
});

// ── PRÉVISIONS ACCUEIL ──
const forecastData = [
  { offset: 0,   icon: '☀️',  temp: '7.4°', rain: '0%'  },
  { offset: 30,  icon: '☀️',  temp: '8.1°', rain: '0%'  },
  { offset: 60,  icon: '🌤️', temp: '9.0°', rain: '5%'  },
  { offset: 90,  icon: '🌤️', temp: '9.8°', rain: '5%'  },
  { offset: 120, icon: '⛅',  temp: '10.5°', rain: '10%' },
];

function buildForecastRow(containerId, smallStyle) {
  const container = document.getElementById(containerId);
  if (!container) return;
  const now = new Date();
  forecastData.forEach(f => {
    const t  = new Date(now.getTime() + f.offset * 60000);
    const hh = String(t.getHours()).padStart(2, '0');
    const mm = String(t.getMinutes()).padStart(2, '0');
    const label = f.offset === 0 ? 'Maintenant' : `${hh}:${mm}`;
    if (smallStyle) {
      container.innerHTML += `
        <div style="flex:1; background:var(--bg); border-radius:8px; padding:7px 4px; text-align:center; display:flex; flex-direction:column; gap:3px;">
          <div style="font-size:9px; font-weight:500; letter-spacing:0.06em; color:var(--muted);">${label}</div>
          <div style="font-size:16px;">${f.icon}</div>
          <div style="font-family:'DM Mono',monospace; font-size:11px;">${f.temp}</div>
          <div style="font-size:9px; color:var(--muted);">${f.rain}</div>
        </div>`;
    } else {
      container.innerHTML += `
        <div style="flex:1; background:var(--bg); border-radius:10px; padding:10px 6px; text-align:center; display:flex; flex-direction:column; gap:4px;">
          <div style="font-size:10px; font-weight:500; letter-spacing:0.06em; color:var(--muted);">${label}</div>
          <div style="font-size:20px;">${f.icon}</div>
          <div style="font-family:'DM Mono',monospace; font-size:13px;">${f.temp}</div>
          <div style="font-size:10px; color:var(--muted);">${f.rain}</div>
        </div>`;
    }
  });
}
buildForecastRow('accueil-forecastRow', true);
buildForecastRow('forecastRow', false);

// ── SPARKLINE (page Conditions) ──
function drawSparkline() {
  const canvas = document.getElementById('sparkline');
  if (!canvas) return;
  const W = canvas.parentElement.clientWidth - 44;
  const H = canvas.parentElement.clientHeight - 44 || 70;
  canvas.width = W;
  canvas.height = H;
  const ctx = canvas.getContext('2d');

  const chargeData = [82, 81, 81, 80, 80, 79, 79, 78, 78, 78];
  const tempData   = [24, 25, 25, 26, 27, 27, 28, 28, 28, 28];
  const pad = 4, pts = chargeData.length;
  const xStep = (W - pad * 2) / (pts - 1);

  const toX       = i => pad + i * xStep;
  const toYcharge = v => H - pad - ((v - 70) / 20) * (H - pad * 2);
  const toYtemp   = v => H - pad - ((v - 20) / 15) * (H - pad * 2);

  ctx.clearRect(0, 0, W, H);

  ctx.strokeStyle = '#e8e6e0';
  ctx.lineWidth = 1;
  [0.25, 0.5, 0.75].forEach(r => {
    const y = pad + r * (H - pad * 2);
    ctx.beginPath(); ctx.moveTo(pad, y); ctx.lineTo(W - pad, y); ctx.stroke();
  });

  // Charge fill
  ctx.beginPath();
  ctx.moveTo(toX(0), toYcharge(chargeData[0]));
  chargeData.forEach((v, i) => ctx.lineTo(toX(i), toYcharge(v)));
  ctx.lineTo(toX(pts - 1), H); ctx.lineTo(toX(0), H); ctx.closePath();
  ctx.fillStyle = 'rgba(45,158,107,0.08)'; ctx.fill();

  // Charge line
  ctx.beginPath();
  chargeData.forEach((v, i) => i === 0 ? ctx.moveTo(toX(i), toYcharge(v)) : ctx.lineTo(toX(i), toYcharge(v)));
  ctx.strokeStyle = '#2d9e6b'; ctx.lineWidth = 2; ctx.lineJoin = 'round'; ctx.stroke();

  // Temp line
  ctx.beginPath();
  tempData.forEach((v, i) => i === 0 ? ctx.moveTo(toX(i), toYtemp(v)) : ctx.lineTo(toX(i), toYtemp(v)));
  ctx.strokeStyle = '#c98a1a'; ctx.lineWidth = 2; ctx.setLineDash([4, 3]); ctx.stroke();
  ctx.setLineDash([]);

  // Dots
  [[toX(pts-1), toYcharge(chargeData[pts-1]), '#2d9e6b'],
   [toX(pts-1), toYtemp(tempData[pts-1]),     '#c98a1a']].forEach(([x, y, c]) => {
    ctx.beginPath(); ctx.arc(x, y, 4, 0, Math.PI * 2);
    ctx.fillStyle = c; ctx.fill();
    ctx.strokeStyle = '#fff'; ctx.lineWidth = 1.5; ctx.stroke();
  });
}
drawSparkline();
window.addEventListener('resize', drawSparkline);

// ── CHRONOMÈTRE SESSION ──
let sessionInterval = null;
let sessionMs = 0;

function updateTimer() {
  sessionMs += 10;
  const m  = String(Math.floor(sessionMs / 60000)).padStart(2, '0');
  const s  = String(Math.floor((sessionMs % 60000) / 1000)).padStart(2, '0');
  const ms = String(Math.floor((sessionMs % 1000) / 10)).padStart(2, '0');
  document.getElementById('sessionTimer').textContent = `${m}:${s}:${ms}`;
}

function startSession() {
  document.getElementById('btnStart').style.display  = 'none';
  document.getElementById('btnStop').style.display   = 'block';
  document.getElementById('btnReset').style.display  = 'none';
  document.getElementById('sessionBadge').style.display = 'flex';
  sessionMs = 0;
  document.getElementById('sessionTimer').textContent = '00:00:00';
  document.getElementById('sessionTimer').style.color = 'var(--green)';
  sessionInterval = setInterval(updateTimer, 10);
}

function resumeSession() {
  document.getElementById('btnStart').style.display  = 'none';
  document.getElementById('btnStop').style.display   = 'block';
  document.getElementById('btnReset').style.display  = 'none';
  document.getElementById('sessionBadge').style.display = 'flex';
  document.getElementById('sessionTimer').style.color = 'var(--green)';
  sessionInterval = setInterval(updateTimer, 10);
}

function stopSession() {
  document.getElementById('btnStop').style.display   = 'none';
  document.getElementById('btnStart').style.display  = 'block';
  document.getElementById('btnStart').textContent    = '▶ Reprendre';
  document.getElementById('btnStart').style.background = '#3a8fd4';
  document.getElementById('btnStart').onclick        = resumeSession;
  document.getElementById('btnReset').style.display  = 'block';
  document.getElementById('sessionBadge').style.display = 'none';
  clearInterval(sessionInterval);
  sessionInterval = null;
  document.getElementById('sessionTimer').style.color = 'var(--muted)';
}

function resetSession() {
  sessionMs = 0;
  document.getElementById('sessionTimer').textContent  = '00:00:00';
  document.getElementById('btnReset').style.display    = 'none';
  document.getElementById('btnStart').textContent      = '▶ Démarrer';
  document.getElementById('btnStart').style.background = 'var(--green)';
  document.getElementById('btnStart').onclick          = startSession;
}

// ── TABLEAU DES TOURS (page Session) ──
const laps = [
  { n:1,  t:56.8, conso:3.4 },
  { n:2,  t:58.1, conso:3.6 },
  { n:3,  t:55.2, conso:3.3 },
  { n:4,  t:54.7, conso:3.2 },
  { n:5,  t:54.1, conso:3.1 },
  { n:6,  t:53.8, conso:3.2 },
  { n:7,  t:53.0, conso:3.1 },
  { n:8,  t:52.4, conso:3.0 },
  { n:9,  t:53.1, conso:3.1 },
  { n:10, t:53.6, conso:3.2 },
  { n:11, t:54.0, conso:3.2 },
  { n:12, t:53.3, conso:3.1 },
  { n:13, t:52.9, conso:3.0 },
  { n:14, t:53.4, conso:3.1 },
  { n:15, t:53.7, conso:3.2 },
];
const best  = Math.min(...laps.map(l => l.t));
const worst = Math.max(...laps.map(l => l.t));
const avg   = (laps.reduce((s, l) => s + l.t, 0) / laps.length).toFixed(1);
document.getElementById('avgLap').textContent = avg + ' s';

const tbody = document.getElementById('lapBody');
laps.forEach(l => {
  const gap      = (l.t - best).toFixed(1);
  const isBest   = l.t === best;
  const isWorst  = l.t === worst;
  const color    = isBest ? 'var(--green)' : isWorst ? 'var(--red)' : 'var(--text)';
  const gapColor = isBest ? 'var(--green)' : gap > 3 ? 'var(--red)' : 'var(--yellow)';
  const bg       = isBest ? '#edf8f3' : isWorst ? '#fdf0ee' : 'transparent';
  tbody.innerHTML += `
    <tr style="background:${bg}; border-radius:6px;">
      <td style="padding:6px 0; font-size:12px; color:${color}; font-weight:${isBest||isWorst?'500':'400'};">
        Tour ${l.n} ${isBest ? '🏆' : isWorst ? '⚠️' : ''}
      </td>
      <td style="text-align:right; padding:6px 0; font-family:'DM Mono',monospace; font-size:12px; color:${color};">${l.t.toFixed(1)} s</td>
      <td style="text-align:right; padding:6px 0; font-family:'DM Mono',monospace; font-size:11px; color:${gapColor};">${isBest ? '—' : '+'+gap+' s'}</td>
      <td style="text-align:right; padding:6px 0; font-size:11px; color:var(--muted);">${l.conso} Wh</td>
    </tr>`;
});

function fetchLiveData() {
  fetch('api_data.php')
    .then(res => res.json())
    .then(data => {
      const pct    = data.pourcentagebatterie;
      const temp   = data.temperaturebatterie;
      const volt   = data.tensionbatterie;
      const ampere = data.intensitebatterie;
      const humid  = data.humiditepiste;
      const tempP  = data.temperaturepiste;

      // ── PAGE SESSION : barre de charge ──
      const sessionChargePct  = document.getElementById('session-charge-pct');
      const sessionChargeFill = document.getElementById('session-charge-fill');
      if (sessionChargePct)  sessionChargePct.innerHTML = `Batterie : ${pct}%<br>`;
      if (sessionChargeFill) sessionChargeFill.style.width = `${pct}%`;

      // ── PAGE CONDITIONS : charge temps réel ──
      const battVal        = document.getElementById('batt-charge-val');
      const condChargePct  = document.getElementById('cond-charge-pct');
      const condChargeFill = document.getElementById('cond-charge-fill');
      if (battVal)        battVal.textContent = pct;
      if (condChargePct)  condChargePct.innerHTML = `${pct}<span>%</span>`;
      if (condChargeFill) condChargeFill.style.width = `${pct}%`;

      // ── Température batterie ──
      const battTempEl = document.getElementById('batt-temp-val');
      if (battTempEl) battTempEl.textContent = temp;

      // ── Tension & courant ──
      const voltEl = document.getElementById('batt-volt-val');
      const ampEl  = document.getElementById('batt-amp-val');
      if (voltEl) voltEl.textContent = volt;
      if (ampEl)  ampEl.textContent  = ampere;

      // ── Température & humidité piste ──
      const tempPisteEl = document.getElementById('temp-piste-val');
      const humidEl     = document.getElementById('humid-val');
      if (tempPisteEl) tempPisteEl.textContent = tempP;
      if (humidEl)     humidEl.textContent     = humid;
    })
    .catch(err => console.warn('Erreur fetch live data:', err));
}

// Appel immédiat + toutes les 4 secondes
fetchLiveData();
setInterval(fetchLiveData, 4000);