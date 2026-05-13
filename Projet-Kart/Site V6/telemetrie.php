<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Télémétrie — Kart Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500&family=DM+Mono:wght@300;400&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.js"></script>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

:root {
  --bg: #f5f4f0;
  --white: #ffffff;
  --text: #1a1a1a;
  --muted: #9a9890;
  --border: #e8e6e0;
  --green: #2d9e6b;
  --yellow: #c98a1a;
  --red: #d94f3d;
  --blue: #2563eb;
  --accent: #1a1a1a;
}

html, body {
  width: 100%; height: 100%;
  background: var(--bg);
  font-family: 'DM Sans', sans-serif;
  color: var(--text);
  overflow: hidden;
}

body { display: flex; flex-direction: column; }

/* ── TOPBAR ── */
.topbar {
  height: 64px;
  background: var(--white);
  border-bottom: 1px solid var(--border);
  display: flex;
  align-items: center;
  padding: 0 36px;
  gap: 32px;
  flex-shrink: 0;
}

.page-title {
  font-size: 14px;
  font-weight: 500;
  color: var(--text);
}

.sep { width: 1px; height: 18px; background: var(--border); }

.topbar-right {
  margin-left: auto;
  display: flex;
  align-items: center;
  gap: 16px;
}

.live {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 500;
  color: var(--green);
}
.live::before {
  content: '';
  width: 6px; height: 6px;
  background: var(--green);
  border-radius: 50%;
  animation: pulse 2s ease infinite;
}
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.3; }
}

.clock {
  font-family: 'DM Mono', monospace;
  font-size: 12px;
  color: var(--muted);
}

.pilot-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 100px;
  padding: 5px 14px 5px 8px;
  font-size: 12px;
}

.pilot-dot {
  width: 24px; height: 24px;
  border-radius: 50%;
  background: var(--text);
  color: var(--white);
  font-size: 10px;
  font-weight: 500;
  display: flex; align-items: center; justify-content: center;
}

/* ── LAYOUT PRINCIPAL ── */
.telem-grid {
  flex: 1;
  display: grid;
  grid-template-columns: 320px 1fr 280px;
  grid-template-rows: 1fr 1fr;
  gap: 0;
  overflow: hidden;
}

/* ── PANEL GENERIQUE ── */
.panel {
  background: var(--white);
  border-right: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
  padding: 20px 22px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  overflow: hidden;
}

.panel:last-child { border-right: none; }
.panel.no-border-right { border-right: none; }
.panel.no-border-bottom { border-bottom: none; }
.panel.bg-alt { background: var(--bg); }

.panel-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.panel-label {
  font-size: 10px;
  font-weight: 500;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--muted);
}

.panel-tag {
  font-size: 10px;
  padding: 2px 8px;
  border-radius: 100px;
  background: var(--bg);
  color: var(--muted);
  border: 1px solid var(--border);
}

/* ── BLOC MÉTRIQUE HERO ── */
.metric-hero {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex-shrink: 0;
}

.metric-big {
  font-size: 52px;
  font-weight: 300;
  letter-spacing: -0.03em;
  line-height: 1;
  font-family: 'DM Mono', monospace;
}

.metric-unit {
  font-size: 18px;
  font-weight: 300;
  color: var(--muted);
}

.metric-sub {
  font-size: 11px;
  color: var(--muted);
  margin-top: 4px;
}

/* ── BARRE CHARGE ── */
.batt-bar-wrap {
  flex-shrink: 0;
}

.batt-bar-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: 6px;
}

.batt-bar-label { font-size: 11px; color: var(--muted); }
.batt-bar-val {
  font-family: 'DM Mono', monospace;
  font-size: 13px;
  font-weight: 400;
}

.bar-track {
  width: 100%;
  height: 6px;
  background: var(--border);
  border-radius: 100px;
  overflow: hidden;
}

.bar-fill {
  height: 100%;
  border-radius: 100px;
  transition: width 1.4s cubic-bezier(.4,0,.2,1);
}

.bar-fill.hi  { background: var(--green); }
.bar-fill.mid { background: var(--yellow); }
.bar-fill.lo  { background: var(--red); }

/* ── GRILLE STATS MINI ── */
.stats-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  flex-shrink: 0;
}

.stat-card {
  background: var(--bg);
  border-radius: 10px;
  padding: 10px 12px;
}

.stat-label {
  font-size: 9px;
  font-weight: 500;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
  margin-bottom: 4px;
}

.stat-value {
  font-size: 20px;
  font-weight: 300;
  letter-spacing: -0.02em;
  font-family: 'DM Mono', monospace;
  line-height: 1;
}

.stat-unit { font-size: 11px; color: var(--muted); }
.stat-sub { font-size: 9px; color: var(--muted); margin-top: 2px; }

/* ── DELTA BADGE ── */
.delta {
  font-size: 10px;
  font-weight: 500;
  padding: 2px 8px;
  border-radius: 100px;
}
.delta.ok   { background: #edf8f3; color: var(--green); }
.delta.warn { background: #fdf6e8; color: var(--yellow); }
.delta.bad  { background: #fdf0ee; color: var(--red); }

/* ── SPARKLINE / GRAPH ── */
.chart-wrap {
  flex: 1;
  position: relative;
  min-height: 0;
}

/* ── TABLE TOURS ── */
.tours-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 11px;
}

.tours-table th {
  text-align: left;
  font-size: 9px;
  font-weight: 500;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
  padding: 0 0 8px 0;
  border-bottom: 1px solid var(--border);
}

.tours-table th:not(:first-child) { text-align: right; }

.tours-table td {
  padding: 5px 0;
  border-bottom: 1px solid var(--border);
  font-family: 'DM Mono', monospace;
  font-size: 11px;
  color: var(--text);
}

.tours-table td:first-child {
  font-family: 'DM Sans', sans-serif;
  font-size: 10px;
  color: var(--muted);
}

.tours-table td:not(:first-child) { text-align: right; }
.tours-table tr:last-child td { border-bottom: none; }

.tours-table tr.best td { color: var(--green); }
.tours-table tr.best td:first-child { color: var(--green); }

.rank-badge {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 18px; height: 18px;
  border-radius: 50%;
  font-size: 9px;
  font-weight: 500;
  background: var(--border);
  color: var(--muted);
  font-family: 'DM Sans', sans-serif;
}

.rank-badge.gold   { background: #fef9e7; color: #c98a1a; }
.rank-badge.silver { background: #f5f5f5; color: #888; }
.rank-badge.bronze { background: #fdf3ee; color: #b5601a; }

/* ── TIMELINE SCROLL ── */
.tours-scroll {
  flex: 1;
  overflow-y: auto;
  min-height: 0;
}

.tours-scroll::-webkit-scrollbar { width: 3px; }
.tours-scroll::-webkit-scrollbar-track { background: transparent; }
.tours-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

/* ── RECORDS ── */
.records-table {
  width: 100%;
  border-collapse: collapse;
}

.records-table th {
  text-align: left;
  font-size: 9px;
  font-weight: 500;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
  padding: 0 0 8px 0;
  border-bottom: 1px solid var(--border);
}

.records-table th:not(:first-child) { text-align: right; }

.records-table td {
  padding: 5px 0;
  font-size: 11px;
  border-bottom: 1px solid var(--border);
  color: var(--text);
}

.records-table td:not(:first-child) {
  text-align: right;
  font-family: 'DM Mono', monospace;
}

.records-table tr:last-child td { border-bottom: none; }

/* ── CONDITIONS ── */
.cond-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
  flex-shrink: 0;
}

.cond-card {
  background: var(--bg);
  border-radius: 10px;
  padding: 12px 14px;
  display: flex;
  flex-direction: column;
  gap: 3px;
}

.cond-label {
  font-size: 9px;
  font-weight: 500;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
}

.cond-val {
  font-size: 26px;
  font-weight: 300;
  letter-spacing: -0.02em;
  line-height: 1;
  font-family: 'DM Mono', monospace;
}

.cond-unit { font-size: 12px; color: var(--muted); }
.cond-sub { font-size: 9px; color: var(--muted); margin-top: 2px; }

/* ── SECTION SPANS ── */
.span-2-rows {
  grid-row: 1 / 3;
}

.loading-msg {
  font-size: 12px;
  color: var(--muted);
  text-align: center;
  padding: 20px 0;
}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <span class="page-title">Télémétrie Kart</span>
  <div class="sep"></div>
  <div class="live">En direct</div>
  <div class="topbar-right">
    <div class="pilot-badge">
      <div class="pilot-dot" id="pilot-initial">—</div>
      <span id="pilot-name" style="font-size:12px;font-weight:500;">Aucun pilote</span>
    </div>
    <div class="clock" id="clock">--:--:--</div>
  </div>
</div>

<!-- GRILLE TÉLÉMÉTRIE -->
<div class="telem-grid">

  <!-- ① BATTERIE — col 1 ligne 1+2 (span 2 rows) -->
  <div class="panel span-2-rows" style="grid-column:1; grid-row:1/3; border-bottom:none;">
    <div class="panel-header">
      <span class="panel-label">Batterie</span>
      <span class="panel-tag" id="batt-delta-tag">—</span>
    </div>

    <!-- Pourcentage hero -->
    <div class="metric-hero">
      <div>
        <span class="metric-big" id="batt-pct">—</span>
        <span class="metric-unit">%</span>
      </div>
      <div class="metric-sub" id="batt-time">Dernière mesure : —</div>
    </div>

    <!-- Barre de charge -->
    <div class="batt-bar-wrap">
      <div class="batt-bar-row">
        <span class="batt-bar-label">Charge</span>
        <span class="batt-bar-val" id="batt-bar-val">—%</span>
      </div>
      <div class="bar-track">
        <div class="bar-fill hi" id="batt-bar" style="width:0%"></div>
      </div>
    </div>

    <!-- Stats tension / intensité / temp -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Tension</div>
        <div class="stat-value" id="batt-tension">—<span class="stat-unit"> V</span></div>
        <div class="stat-sub">Batterie principale</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Intensité</div>
        <div class="stat-value" id="batt-intensite">—<span class="stat-unit"> A</span></div>
        <div class="stat-sub">Courant tiré</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Temp. batt.</div>
        <div class="stat-value" id="batt-temp">—<span class="stat-unit"> °C</span></div>
        <div class="stat-sub" id="batt-temp-state">—</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Min session</div>
        <div class="stat-value" id="batt-min">—<span class="stat-unit"> %</span></div>
        <div class="stat-sub">Charge minimale</div>
      </div>
    </div>

    <!-- Sparkline historique batterie -->
    <div class="panel-header" style="margin-top:4px;">
      <span class="panel-label">Historique 20 mesures</span>
    </div>
    <div class="chart-wrap">
      <canvas id="sparkBatt" role="img" aria-label="Historique du pourcentage batterie sur les 20 dernières mesures">Historique batterie.</canvas>
    </div>
    <div class="chart-wrap" style="flex:0.8;">
      <canvas id="sparkTemp" role="img" aria-label="Historique de la température batterie">Historique température batterie.</canvas>
    </div>
  </div>

  <!-- ② TOURS — col 2 ligne 1 -->
  <div class="panel" style="grid-column:2; grid-row:1;">
    <div class="panel-header">
      <span class="panel-label">Tours de session</span>
      <span class="panel-tag" id="tours-count">0 tour</span>
    </div>
    <div class="tours-scroll">
      <table class="tours-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Pilote</th>
            <th>Temps</th>
            <th>Écart</th>
            <th>Horodatage</th>
          </tr>
        </thead>
        <tbody id="tours-body">
          <tr><td colspan="5" class="loading-msg">Chargement…</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ③ CONDITIONS PISTE — col 3 ligne 1 -->
  <div class="panel no-border-right" style="grid-column:3; grid-row:1;">
    <div class="panel-header">
      <span class="panel-label">Conditions piste</span>
    </div>
    <div class="cond-grid">
      <div class="cond-card">
        <div class="cond-label">Température</div>
        <div class="cond-val" id="cond-temp">—<span class="cond-unit"> °C</span></div>
        <div class="cond-sub" id="cond-temp-range">min — / max —</div>
      </div>
      <div class="cond-card">
        <div class="cond-label">Humidité</div>
        <div class="cond-val" id="cond-humid">—<span class="cond-unit"> %</span></div>
        <div class="cond-sub" id="cond-humid-range">min — / max —</div>
      </div>
    </div>
    <!-- Graphique température piste -->
    <div class="panel-label" style="margin-top:4px;">Évolution température</div>
    <div class="chart-wrap">
      <canvas id="sparkPiste" role="img" aria-label="Évolution de la température de piste">Température piste.</canvas>
    </div>
    <!-- Stats globales -->
    <div class="panel-label" style="margin-top:4px;">Statistiques session</div>
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-label">Mesures</div>
        <div class="stat-value" id="stats-total">—</div>
        <div class="stat-sub">Total capteurs</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Moy. temp.</div>
        <div class="stat-value" id="stats-temp-moy">—<span class="stat-unit"> °C</span></div>
        <div class="stat-sub">Sur session</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Moy. humid.</div>
        <div class="stat-value" id="stats-humid-moy">—<span class="stat-unit"> %</span></div>
        <div class="stat-sub">Sur session</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Batt. max</div>
        <div class="stat-value" id="stats-batt-max">—<span class="stat-unit"> %</span></div>
        <div class="stat-sub">Sur session</div>
      </div>
    </div>
  </div>

  <!-- ④ GRAPHIQUE TEMPS PAR TOUR — col 2 ligne 2 -->
  <div class="panel no-border-bottom" style="grid-column:2; grid-row:2;">
    <div class="panel-header">
      <span class="panel-label">Évolution des temps au tour</span>
      <span class="panel-tag" id="best-lap-tag">Meilleur : —</span>
    </div>
    <div class="chart-wrap">
      <canvas id="chartTours" role="img" aria-label="Graphique des temps par tour">Temps au tour.</canvas>
    </div>
  </div>

  <!-- ⑤ CLASSEMENT PILOTES — col 3 ligne 2 -->
  <div class="panel no-border-right no-border-bottom" style="grid-column:3; grid-row:2;">
    <div class="panel-header">
      <span class="panel-label">Classement records</span>
    </div>
    <div class="tours-scroll">
      <table class="records-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Pilote</th>
            <th>Record</th>
          </tr>
        </thead>
        <tbody id="records-body">
          <tr><td colspan="3" class="loading-msg">Chargement…</td></tr>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
/* ── UTILS ── */
function fmtMs(ms) {
  if (!ms || isNaN(ms) || ms <= 0) return '—';
  const m = Math.floor(ms / 60000);
  const s = Math.floor((ms % 60000) / 1000);
  const c = Math.floor((ms % 1000) / 10);
  return (m > 0 ? m + ':' : '') + String(s).padStart(2,'0') + '.' + String(c).padStart(2,'0');
}

function fmtTime(ts) {
  if (!ts) return '—';
  const d = new Date(ts);
  return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function fmtHorodatage(ts) {
  if (!ts) return '—';
  const d = new Date(ts * 1000);
  return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

/* ── HORLOGE ── */
function updateClock() {
  document.getElementById('clock').textContent =
    new Date().toLocaleTimeString('fr-FR');
}
setInterval(updateClock, 1000);
updateClock();

/* ── CHARTS ── */
let chartBatt = null, chartTemp = null, chartPiste = null, chartTours = null;

const CHART_DEFAULTS = {
  responsive: true,
  maintainAspectRatio: false,
  animation: { duration: 600 },
  plugins: { legend: { display: false }, tooltip: {
    backgroundColor: '#1a1a1a',
    titleColor: '#9a9890',
    bodyColor: '#ffffff',
    padding: 10,
    cornerRadius: 8,
    displayColors: false,
  }},
  scales: {
    x: { display: false },
    y: {
      grid: { color: '#e8e6e0', lineWidth: 0.5 },
      ticks: {
        font: { family: "'DM Mono', monospace", size: 10 },
        color: '#9a9890',
        maxTicksLimit: 4,
      },
      border: { display: false },
    }
  }
};

function initCharts() {
  // Sparkline batterie %
  chartBatt = new Chart(document.getElementById('sparkBatt'), {
    type: 'line',
    data: { labels: [], datasets: [{
      data: [],
      borderColor: '#2d9e6b',
      borderWidth: 1.5,
      pointRadius: 0,
      fill: true,
      backgroundColor: 'rgba(45,158,107,0.08)',
      tension: 0.35,
    }]},
    options: { ...CHART_DEFAULTS },
  });

  // Sparkline température batterie
  chartTemp = new Chart(document.getElementById('sparkTemp'), {
    type: 'line',
    data: { labels: [], datasets: [{
      data: [],
      borderColor: '#c98a1a',
      borderWidth: 1.5,
      pointRadius: 0,
      fill: true,
      backgroundColor: 'rgba(201,138,26,0.07)',
      tension: 0.35,
    }]},
    options: { ...CHART_DEFAULTS },
  });

  // Sparkline temp piste
  chartPiste = new Chart(document.getElementById('sparkPiste'), {
    type: 'line',
    data: { labels: [], datasets: [{
      data: [],
      borderColor: '#2563eb',
      borderWidth: 1.5,
      pointRadius: 0,
      fill: true,
      backgroundColor: 'rgba(37,99,235,0.07)',
      tension: 0.35,
    }]},
    options: { ...CHART_DEFAULTS },
  });

  // Graphique temps au tour (bar)
  chartTours = new Chart(document.getElementById('chartTours'), {
    type: 'bar',
    data: { labels: [], datasets: [{
      data: [],
      backgroundColor: '#e8e6e0',
      borderRadius: 4,
      borderSkipped: false,
    }]},
    options: {
      ...CHART_DEFAULTS,
      scales: {
        x: {
          display: true,
          grid: { display: false },
          ticks: {
            font: { family: "'DM Mono', monospace", size: 10 },
            color: '#9a9890',
            autoSkip: true,
            maxTicksLimit: 12,
          },
          border: { display: false },
        },
        y: {
          display: true,
          grid: { color: '#e8e6e0', lineWidth: 0.5 },
          ticks: {
            font: { family: "'DM Mono', monospace", size: 10 },
            color: '#9a9890',
            maxTicksLimit: 5,
            callback: v => fmtMs(v),
          },
          border: { display: false },
        }
      },
      plugins: {
        ...CHART_DEFAULTS.plugins,
        tooltip: {
          ...CHART_DEFAULTS.plugins.tooltip,
          callbacks: {
            label: ctx => fmtMs(ctx.parsed.y),
          }
        }
      }
    }
  });
}

/* ── MISE À JOUR UI ── */
function updateBatterie(capteur, historique, stats) {
  const pct = capteur?.pourcentagebatterie ?? null;
  const tension = capteur?.tensionbatterie ?? null;
  const intensite = capteur?.intensitebatterie ?? null;
  const tempBatt = capteur?.temperaturebatterie ?? null;
  const lastDate = capteur?.date ?? null;

  // Hero
  document.getElementById('batt-pct').textContent = pct !== null ? pct : '—';
  document.getElementById('batt-bar-val').textContent = pct !== null ? pct + '%' : '—%';
  document.getElementById('batt-time').textContent = 'Dernière mesure : ' + (lastDate ? fmtTime(lastDate) : '—');

  // Barre
  const bar = document.getElementById('batt-bar');
  bar.style.width = (pct !== null ? Math.min(pct, 100) : 0) + '%';
  bar.className = 'bar-fill ' + (pct > 50 ? 'hi' : pct > 20 ? 'mid' : 'lo');

  // Badge
  const tag = document.getElementById('batt-delta-tag');
  if (pct > 50) { tag.textContent = 'OK'; tag.className = 'panel-tag delta ok'; }
  else if (pct > 20) { tag.textContent = 'Attention'; tag.className = 'panel-tag delta warn'; }
  else if (pct !== null) { tag.textContent = 'Critique'; tag.className = 'panel-tag delta bad'; }

  // Stats
  document.getElementById('batt-tension').innerHTML = (tension !== null ? tension : '—') + '<span class="stat-unit"> V</span>';
  document.getElementById('batt-intensite').innerHTML = (intensite !== null ? intensite : '—') + '<span class="stat-unit"> A</span>';
  document.getElementById('batt-temp').innerHTML = (tempBatt !== null ? tempBatt : '—') + '<span class="stat-unit"> °C</span>';

  const tempState = tempBatt !== null
    ? (tempBatt < 25 ? 'Froide' : tempBatt < 40 ? 'Normale' : 'Chaude')
    : '—';
  document.getElementById('batt-temp-state').textContent = tempState;
  document.getElementById('batt-min').innerHTML = (stats?.bat_min ?? '—') + '<span class="stat-unit"> %</span>';

  // Sparklines
  if (historique && historique.length > 0) {
    const labels = historique.map(r => r.date ? r.date.slice(11,19) : '');
    const pctData = historique.map(r => r.pourcentagebatterie !== null ? parseInt(r.pourcentagebatterie) : null);
    const tempData = historique.map(r => r.temperaturebatterie !== null ? parseInt(r.temperaturebatterie) : null);

    chartBatt.data.labels = labels;
    chartBatt.data.datasets[0].data = pctData;
    chartBatt.update('none');

    chartTemp.data.labels = labels;
    chartTemp.data.datasets[0].data = tempData;
    chartTemp.update('none');
  }
}

function updateTours(tours) {
  const tbody = document.getElementById('tours-body');
  const count = document.getElementById('tours-count');

  if (!tours || tours.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="loading-msg">Aucun tour enregistré</td></tr>';
    count.textContent = '0 tour';
    document.getElementById('best-lap-tag').textContent = 'Meilleur : —';
    return;
  }

  const validTours = tours.filter(t => t.temps > 0);
  count.textContent = validTours.length + ' tour' + (validTours.length > 1 ? 's' : '');

  let bestTime = validTours.length > 0 ? Math.min(...validTours.map(t => t.temps)) : null;
  document.getElementById('best-lap-tag').textContent = 'Meilleur : ' + (bestTime ? fmtMs(bestTime) : '—');

  // Tableau
  const rows = validTours.map((t, i) => {
    const isBest = t.temps === bestTime;
    const diff = i === 0 ? '' : (t.temps - validTours[0].temps);
    const diffStr = diff === '' ? '—' : (diff > 0 ? '+' : '') + fmtMs(Math.abs(diff));
    return `<tr class="${isBest ? 'best' : ''}">
      <td style="color:var(--muted);font-size:10px;">${t.ID}</td>
      <td style="font-family:'DM Sans',sans-serif;font-size:10px;color:var(--text);">${t.pilote || '—'}</td>
      <td>${fmtMs(t.temps)}</td>
      <td style="color:${diff > 0 ? 'var(--red)' : diff < 0 ? 'var(--green)' : 'var(--muted)'}">${isBest ? '⚡ BEST' : diffStr}</td>
      <td style="color:var(--muted)">${fmtHorodatage(t.timestamp)}</td>
    </tr>`;
  }).join('');

  tbody.innerHTML = rows || '<tr><td colspan="5" class="loading-msg">Aucun tour valide</td></tr>';

  // Chart tours
  const labels = validTours.map((_, i) => 'T' + (i + 1));
  const data = validTours.map(t => t.temps);
  const colors = data.map(d => d === bestTime ? '#2d9e6b' : '#e8e6e0');

  chartTours.data.labels = labels;
  chartTours.data.datasets[0].data = data;
  chartTours.data.datasets[0].backgroundColor = colors;
  chartTours.update('none');
}

function updateConditions(capteur, historique, stats) {
  const tempPiste = capteur?.temperaturepiste ?? null;
  const humid = capteur?.humiditepiste ?? null;

  document.getElementById('cond-temp').innerHTML = (tempPiste !== null ? tempPiste : '—') + '<span class="cond-unit"> °C</span>';
  document.getElementById('cond-humid').innerHTML = (humid !== null ? humid : '—') + '<span class="cond-unit"> %</span>';

  document.getElementById('cond-temp-range').textContent =
    'min ' + (stats?.temp_min ?? '—') + ' / max ' + (stats?.temp_max ?? '—');
  document.getElementById('cond-humid-range').textContent =
    'min ' + (stats?.humid_min ?? '—') + ' / max ' + (stats?.humid_max ?? '—');

  document.getElementById('stats-total').textContent = stats?.total_mesures ?? '—';
  document.getElementById('stats-temp-moy').innerHTML = (stats?.temp_moy ?? '—') + '<span class="stat-unit"> °C</span>';
  document.getElementById('stats-humid-moy').innerHTML = (stats?.humid_moy ?? '—') + '<span class="stat-unit"> %</span>';
  document.getElementById('stats-batt-max').innerHTML = (stats?.bat_max ?? '—') + '<span class="stat-unit"> %</span>';

  // Sparkline piste
  if (historique && historique.length > 0) {
    const validPiste = historique.filter(r => r.temperaturepiste !== null);
    if (validPiste.length > 0) {
      chartPiste.data.labels = validPiste.map(r => r.date ? r.date.slice(11,19) : '');
      chartPiste.data.datasets[0].data = validPiste.map(r => parseInt(r.temperaturepiste));
      chartPiste.update('none');
    }
  }
}

function updatePilote(pilote_actif) {
  const el = document.getElementById('pilot-name');
  const dot = document.getElementById('pilot-initial');
  if (pilote_actif) {
    el.textContent = pilote_actif;
    dot.textContent = pilote_actif.slice(0, 2).toUpperCase();
  } else {
    el.textContent = 'Aucun pilote';
    dot.textContent = '—';
  }
}

function updatePilotes(pilotes) {
  const tbody = document.getElementById('records-body');
  if (!pilotes || pilotes.length === 0) {
    tbody.innerHTML = '<tr><td colspan="3" class="loading-msg">Aucun pilote</td></tr>';
    return;
  }

  const rankClass = (i) => i === 0 ? 'gold' : i === 1 ? 'silver' : i === 2 ? 'bronze' : '';

  // Trier par record numérique valide
  const sorted = [...pilotes].sort((a, b) => {
    const ra = parseFloat(String(a.record).replace(',', '.'));
    const rb = parseFloat(String(b.record).replace(',', '.'));
    if (isNaN(ra)) return 1;
    if (isNaN(rb)) return -1;
    return ra - rb;
  });

  tbody.innerHTML = sorted.map((p, i) => {
    const recMs = parseFloat(String(p.record).replace(',', '.'));
    const fmted = recMs > 100 ? fmtMs(recMs) : (isNaN(recMs) ? p.record : p.record + ' s');
    return `<tr>
      <td><span class="rank-badge ${rankClass(i)}">${i + 1}</span></td>
      <td style="font-family:'DM Sans',sans-serif;padding-left:4px;">${p.nom}</td>
      <td>${fmted}</td>
    </tr>`;
  }).join('');
}

/* ── FETCH DONNÉES ── */
async function fetchData() {
  try {
    const res = await fetch('api_data.php', { cache: 'no-store' });
    const data = await res.json();

    updatePilote(data.pilote_actif);
    updateBatterie(data.capteur, data.historique, data.stats);
    updateConditions(data.capteur, data.historique, data.stats);
    updateTours(data.tours);
    updatePilotes(data.pilotes);
  } catch (e) {
    console.error('Erreur fetch télémétrie :', e);
  }
}

/* ── INIT ── */
initCharts();
fetchData();
setInterval(fetchData, 5000); // rafraîchissement toutes les 5s
</script>
</body>
</html>
