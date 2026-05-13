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

.session-badge {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--bg);
  border: 1px solid var(--border);
  border-radius: 100px;
  padding: 5px 14px;
  font-size: 12px;
  font-family: 'DM Mono', monospace;
  color: var(--muted);
}

.session-badge strong {
  color: var(--text);
  font-weight: 500;
}

/* ── LAYOUT PRINCIPAL ── */
.telem-grid {
  flex: 1;
  display: grid;
  grid-template-columns: 340px 1fr 300px;
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

/* ── HORODATAGE BADGE ── */
.date-stamp {
  display: flex;
  align-items: center;
  gap: 6px;
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  color: var(--muted);
  flex-shrink: 0;
}

.date-stamp .date-icon {
  width: 14px; height: 14px;
  opacity: 0.4;
}

.date-stamp .date-part {
  color: var(--text);
  font-weight: 400;
}

.date-stamp .time-part {
  color: var(--blue);
}

.freshness-dot {
  width: 5px; height: 5px;
  border-radius: 50%;
  background: var(--green);
  animation: pulse 2s ease infinite;
}

/* ── BLOC MÉTRIQUE HERO ── */
.metric-hero {
  display: flex;
  flex-direction: column;
  gap: 2px;
  flex-shrink: 0;
}

.metric-big {
  font-size: 64px;
  font-weight: 300;
  letter-spacing: -0.03em;
  line-height: 1;
  font-family: 'DM Mono', monospace;
}

.metric-unit {
  font-size: 22px;
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
  height: 8px;
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

/* ── LOG MESURES DATÉ ── */
.log-scroll {
  flex: 1;
  overflow-y: auto;
  min-height: 0;
}

.log-scroll::-webkit-scrollbar { width: 3px; }
.log-scroll::-webkit-scrollbar-track { background: transparent; }
.log-scroll::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

.log-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 11px;
}

.log-table th {
  text-align: left;
  font-size: 9px;
  font-weight: 500;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--muted);
  padding: 0 0 8px 0;
  border-bottom: 1px solid var(--border);
  position: sticky;
  top: 0;
  background: var(--white);
}

.log-table th:not(:first-child) { text-align: right; }

.log-table td {
  padding: 5px 0;
  border-bottom: 1px solid var(--border);
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  color: var(--text);
}

.log-table td:not(:first-child) { text-align: right; }
.log-table tr:last-child td { border-bottom: none; }

.log-table tr.log-latest td {
  color: var(--blue);
}

.log-table .log-time {
  color: var(--muted);
  font-size: 9px;
}

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

/* ── TIMELINE DATÉE ── */
.timeline-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  flex-shrink: 0;
}

.timeline-count {
  font-family: 'DM Mono', monospace;
  font-size: 10px;
  color: var(--muted);
}

/* ── STAT HIGHLIGHT ── */
.stat-card.highlight {
  border: 1px solid rgba(37, 99, 235, 0.2);
  background: rgba(37, 99, 235, 0.04);
}
.stat-card.highlight .stat-label { color: var(--blue); }
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <span class="page-title">Télémétrie Batterie — Kart</span>
  <div class="sep"></div>
  <div class="live">En direct</div>
  <div class="topbar-right">
    <div class="session-badge">
      Session démarrée : <strong id="session-start">—</strong>
    </div>
    <div class="session-badge">
      Durée : <strong id="session-duration">—</strong>
    </div>
    <div class="clock" id="clock">--:--:--</div>
  </div>
</div>

<!-- GRILLE TÉLÉMÉTRIE -->
<div class="telem-grid">

  <!-- ① BATTERIE — col 1, span 2 rows -->
  <div class="panel span-2-rows" style="grid-column:1; grid-row:1/3; border-bottom:none;">
    <div class="panel-header">
      <span class="panel-label">Batterie</span>
      <span class="panel-tag" id="batt-delta-tag">—</span>
    </div>

    <!-- Horodatage de la dernière mesure -->
    <div class="date-stamp">
      <div class="freshness-dot" id="freshness-dot"></div>
      <span>Dernière mesure :</span>
      <span class="date-part" id="batt-date">—</span>
      <span class="time-part" id="batt-time-hms">—</span>
    </div>

    <!-- Pourcentage hero -->
    <div class="metric-hero">
      <div>
        <span class="metric-big" id="batt-pct">—</span>
        <span class="metric-unit">%</span>
      </div>
      <div class="metric-sub" id="batt-age">—</div>
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

    <!-- Stats tension / intensité / temp / min -->
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

    <!-- Journal des mesures compact -->
    <div class="panel-header" style="margin-top:4px;">
      <span class="panel-label">Journal des mesures</span>
      <span class="timeline-count" id="sparkBatt-count">—</span>
    </div>
    <div class="log-scroll" style="flex:1;">
      <table class="log-table">
        <thead>
          <tr>
            <th>Heure</th>
            <th>Charge</th>
            <th>Tension</th>
            <th>Intensité</th>
            <th>Temp.</th>
          </tr>
        </thead>
        <tbody id="log-body-left">
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

  <!-- ④ GRAPHIQUE ÉVOLUTION BATTERIE — col 2, span 2 rows -->
  <div class="panel no-border-bottom" style="grid-column:2; grid-row:1/3;">
    <div class="panel-header">
      <span class="panel-label">Évolution charge — session complète</span>
      <span class="panel-tag" id="batt-trend-tag">—</span>
    </div>
    <div class="chart-wrap">
      <canvas id="chartBattFull" role="img" aria-label="Évolution complète de la charge batterie">Évolution batterie.</canvas>
    </div>
  </div>

  <!-- ⑤ STATISTIQUES BATTERIE AVANCÉES — col 3 ligne 2 -->
  <div class="panel no-border-right no-border-bottom" style="grid-column:3; grid-row:2;">
    <div class="panel-header">
      <span class="panel-label">Analyse batterie</span>
      <span class="panel-tag" id="batt-analysis-date">—</span>
    </div>
    <div class="stats-grid" style="flex:1; align-content:start;">
      <div class="stat-card highlight">
        <div class="stat-label">Décharge/h</div>
        <div class="stat-value" id="batt-discharge-rate">—<span class="stat-unit"> %/h</span></div>
        <div class="stat-sub">Taux estimé</div>
      </div>
      <div class="stat-card highlight">
        <div class="stat-label">Autonomie est.</div>
        <div class="stat-value" id="batt-eta">—<span class="stat-unit"> min</span></div>
        <div class="stat-sub">À ce taux</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Batt. max</div>
        <div class="stat-value" id="batt-max-val">—<span class="stat-unit"> %</span></div>
        <div class="stat-sub" id="batt-max-at">—</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Batt. min</div>
        <div class="stat-value" id="batt-min-val">—<span class="stat-unit"> %</span></div>
        <div class="stat-sub" id="batt-min-at">—</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Moy. tension</div>
        <div class="stat-value" id="batt-tension-moy">—<span class="stat-unit"> V</span></div>
        <div class="stat-sub">Sur session</div>
      </div>
      <div class="stat-card">
        <div class="stat-label">Moy. intensité</div>
        <div class="stat-value" id="batt-intensite-moy">—<span class="stat-unit"> A</span></div>
        <div class="stat-sub">Sur session</div>
      </div>
    </div>
  </div>

</div>

<script>
/* ── UTILS ── */
function fmtTime(ts) {
  if (!ts) return '—';
  const d = new Date(ts);
  return d.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function fmtDate(ts) {
  if (!ts) return '—';
  const d = new Date(ts);
  return d.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
}

function fmtDateTime(ts) {
  if (!ts) return '—';
  const d = new Date(ts);
  return d.toLocaleString('fr-FR', {
    day: '2-digit', month: '2-digit', year: 'numeric',
    hour: '2-digit', minute: '2-digit', second: '2-digit'
  });
}

function fmtAge(ts) {
  if (!ts) return '—';
  const now = Date.now();
  const d = new Date(ts);
  const diff = Math.floor((now - d.getTime()) / 1000);
  if (diff < 5)  return 'À l\'instant';
  if (diff < 60) return `Il y a ${diff}s`;
  if (diff < 3600) return `Il y a ${Math.floor(diff/60)}min`;
  return `Il y a ${Math.floor(diff/3600)}h`;
}

function fmtDuration(ms) {
  if (!ms || ms < 0) return '—';
  const h = Math.floor(ms / 3600000);
  const m = Math.floor((ms % 3600000) / 60000);
  const s = Math.floor((ms % 60000) / 1000);
  if (h > 0) return `${h}h ${String(m).padStart(2,'0')}min`;
  if (m > 0) return `${m}min ${String(s).padStart(2,'0')}s`;
  return `${s}s`;
}

/* ── HORLOGE ── */
function updateClock() {
  document.getElementById('clock').textContent =
    new Date().toLocaleTimeString('fr-FR');
}
setInterval(updateClock, 1000);
updateClock();

/* ── MISE À JOUR ÂGE EN TEMPS RÉEL ── */
let _lastMeasureTs = null;
function refreshAge() {
  if (_lastMeasureTs) {
    document.getElementById('batt-age').textContent = 'Mesure : ' + fmtAge(_lastMeasureTs);
  }
}
setInterval(refreshAge, 1000);

/* ── DURÉE SESSION ── */
let _sessionStart = null;
function refreshSessionDuration() {
  if (_sessionStart) {
    document.getElementById('session-duration').textContent = fmtDuration(Date.now() - _sessionStart);
  }
}
setInterval(refreshSessionDuration, 1000);

/* ── CHARTS ── */
let chartBatt = null, chartTemp = null, chartPiste = null, chartBattFull = null;
// Note: chartBatt and chartTemp sparklines removed; data shown in log table

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

  // Graphique batterie session complète (avec horodatage sur l'axe X)
  chartBattFull = new Chart(document.getElementById('chartBattFull'), {
    type: 'line',
    data: { labels: [], datasets: [
      {
        label: 'Charge %',
        data: [],
        borderColor: '#2d9e6b',
        borderWidth: 2,
        pointRadius: 2,
        pointBackgroundColor: '#2d9e6b',
        fill: true,
        backgroundColor: 'rgba(45,158,107,0.07)',
        tension: 0.3,
        yAxisID: 'y',
      },
      {
        label: 'Temp. °C',
        data: [],
        borderColor: '#c98a1a',
        borderWidth: 1.5,
        pointRadius: 0,
        fill: false,
        tension: 0.3,
        yAxisID: 'y2',
        borderDash: [4, 3],
      }
    ]},
    options: {
      responsive: true,
      maintainAspectRatio: false,
      animation: { duration: 600 },
      interaction: { mode: 'index', intersect: false },
      plugins: {
        legend: {
          display: true,
          position: 'top',
          align: 'end',
          labels: {
            font: { family: "'DM Mono', monospace", size: 10 },
            color: '#9a9890',
            boxWidth: 12,
            padding: 10,
          }
        },
        tooltip: {
          backgroundColor: '#1a1a1a',
          titleColor: '#9a9890',
          bodyColor: '#ffffff',
          padding: 10,
          cornerRadius: 8,
          displayColors: true,
        }
      },
      scales: {
        x: {
          display: true,
          grid: { display: false },
          ticks: {
            font: { family: "'DM Mono', monospace", size: 9 },
            color: '#9a9890',
            maxTicksLimit: 8,
            maxRotation: 0,
          },
          border: { display: false },
        },
        y: {
          display: true,
          position: 'left',
          grid: { color: '#e8e6e0', lineWidth: 0.5 },
          ticks: {
            font: { family: "'DM Mono', monospace", size: 10 },
            color: '#2d9e6b',
            maxTicksLimit: 5,
            callback: v => v + '%',
          },
          border: { display: false },
          min: 0, max: 100,
        },
        y2: {
          display: true,
          position: 'right',
          grid: { display: false },
          ticks: {
            font: { family: "'DM Mono', monospace", size: 10 },
            color: '#c98a1a',
            maxTicksLimit: 5,
            callback: v => v + '°',
          },
          border: { display: false },
        }
      }
    }
  });
}

/* ── MISE À JOUR UI ── */
function updateBatterie(capteur, historique, stats) {
  const pct      = capteur?.pourcentagebatterie ?? null;
  const tension  = capteur?.tensionbatterie ?? null;
  const intensite= capteur?.intensitebatterie ?? null;
  const tempBatt = capteur?.temperaturebatterie ?? null;
  const lastDate = capteur?.date ?? null;

  // Horodatage
  _lastMeasureTs = lastDate ? new Date(lastDate).getTime() : null;
  document.getElementById('batt-date').textContent   = lastDate ? fmtDate(lastDate) : '—';
  document.getElementById('batt-time-hms').textContent = lastDate ? fmtTime(lastDate) : '—';
  document.getElementById('batt-age').textContent    = lastDate ? 'Mesure : ' + fmtAge(lastDate) : '—';

  // Hero
  document.getElementById('batt-pct').textContent     = pct !== null ? pct : '—';
  document.getElementById('batt-bar-val').textContent  = pct !== null ? pct + '%' : '—%';

  // Barre
  const bar = document.getElementById('batt-bar');
  bar.style.width = (pct !== null ? Math.min(pct, 100) : 0) + '%';
  bar.className = 'bar-fill ' + (pct > 50 ? 'hi' : pct > 20 ? 'mid' : 'lo');

  // Badge état
  const tag = document.getElementById('batt-delta-tag');
  if (pct > 50)       { tag.textContent = 'OK';       tag.className = 'panel-tag delta ok'; }
  else if (pct > 20)  { tag.textContent = 'Attention'; tag.className = 'panel-tag delta warn'; }
  else if (pct !== null){ tag.textContent = 'Critique'; tag.className = 'panel-tag delta bad'; }

  // Stats
  document.getElementById('batt-tension').innerHTML   = (tension  !== null ? tension  : '—') + '<span class="stat-unit"> V</span>';
  document.getElementById('batt-intensite').innerHTML = (intensite !== null ? intensite: '—') + '<span class="stat-unit"> A</span>';
  document.getElementById('batt-temp').innerHTML      = (tempBatt !== null ? tempBatt : '—') + '<span class="stat-unit"> °C</span>';

  const tempState = tempBatt !== null
    ? (tempBatt < 25 ? 'Froide' : tempBatt < 40 ? 'Normale' : 'Chaude')
    : '—';
  document.getElementById('batt-temp-state').textContent = tempState;
  document.getElementById('batt-min').innerHTML = (stats?.bat_min ?? '—') + '<span class="stat-unit"> %</span>';

  // Sparklines 20 dernières mesures
  if (historique && historique.length > 0) {
    const labels   = historique.map(r => r.date ? r.date.slice(11,19) : '');
    const pctData  = historique.map(r => r.pourcentagebatterie  !== null ? parseInt(r.pourcentagebatterie)  : null);
    const tempData = historique.map(r => r.temperaturebatterie  !== null ? parseInt(r.temperaturebatterie)  : null);

    // Graphique session complète
    chartBattFull.data.labels = labels;
    chartBattFull.data.datasets[0].data = pctData;
    chartBattFull.data.datasets[1].data = tempData;
    chartBattFull.update('none');

    // Analyse batterie
    updateAnalyseBatterie(historique, stats);
  }
}

function updateLogMesures(historique) {
  const tbody = document.getElementById('log-body-left');
  const count = document.getElementById('sparkBatt-count');

  if (!historique || historique.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5" class="loading-msg">Aucune mesure</td></tr>';
    count.textContent = '0 mesure';
    return;
  }

  count.textContent = historique.length + ' mesure' + (historique.length > 1 ? 's' : '');

  // Afficher du plus récent au plus ancien
  const sorted = [...historique].reverse();

  tbody.innerHTML = sorted.map((r, i) => {
    const ts   = r.date ?? null;
    const pct  = r.pourcentagebatterie  !== null ? parseInt(r.pourcentagebatterie)  + '%' : '—';
    const v    = r.tensionbatterie      !== null ? r.tensionbatterie + ' V'     : '—';
    const a    = r.intensitebatterie    !== null ? r.intensitebatterie + ' A'   : '—';
    const t    = r.temperaturebatterie  !== null ? r.temperaturebatterie + ' °C': '—';
    const isLatest = i === 0;
    return `<tr class="${isLatest ? 'log-latest' : ''}">
      <td class="log-time">${ts ? ts.slice(11,19) : '—'}</td>
      <td>${pct}</td>
      <td>${v}</td>
      <td>${a}</td>
      <td>${t}</td>
    </tr>`;
  }).join('');
}

function updateAnalyseBatterie(historique, stats) {
  // Filtrer mesures valides
  const valid = historique.filter(r => r.pourcentagebatterie !== null && r.date);
  if (valid.length < 2) return;

  const first = valid[0];
  const last  = valid[valid.length - 1];
  const pctFirst = parseInt(first.pourcentagebatterie);
  const pctLast  = parseInt(last.pourcentagebatterie);
  const tFirst   = new Date(first.date).getTime();
  const tLast    = new Date(last.date).getTime();
  const elapsedH = (tLast - tFirst) / 3600000;

  // Taux de décharge %/h
  let dischargeRate = null;
  if (elapsedH > 0 && pctFirst > pctLast) {
    dischargeRate = ((pctFirst - pctLast) / elapsedH).toFixed(1);
  }

  // Autonomie estimée (en minutes)
  let eta = null;
  if (dischargeRate && dischargeRate > 0) {
    eta = Math.round((pctLast / dischargeRate) * 60);
  }

  document.getElementById('batt-discharge-rate').innerHTML =
    (dischargeRate !== null ? dischargeRate : '—') + '<span class="stat-unit"> %/h</span>';
  document.getElementById('batt-eta').innerHTML =
    (eta !== null ? eta : '—') + '<span class="stat-unit"> min</span>';

  // Max / min avec horodatage
  const maxR = valid.reduce((best, r) => parseInt(r.pourcentagebatterie) > parseInt(best.pourcentagebatterie) ? r : best, valid[0]);
  const minR = valid.reduce((best, r) => parseInt(r.pourcentagebatterie) < parseInt(best.pourcentagebatterie) ? r : best, valid[0]);

  document.getElementById('batt-max-val').innerHTML = parseInt(maxR.pourcentagebatterie) + '<span class="stat-unit"> %</span>';
  document.getElementById('batt-max-at').textContent = maxR.date ? 'à ' + maxR.date.slice(11,19) : '—';
  document.getElementById('batt-min-val').innerHTML = parseInt(minR.pourcentagebatterie) + '<span class="stat-unit"> %</span>';
  document.getElementById('batt-min-at').textContent = minR.date ? 'à ' + minR.date.slice(11,19) : '—';

  // Moyennes tension / intensité
  const vValid = valid.filter(r => r.tensionbatterie !== null);
  const aValid = valid.filter(r => r.intensitebatterie !== null);
  const vMoy = vValid.length > 0
    ? (vValid.reduce((s, r) => s + parseFloat(r.tensionbatterie), 0) / vValid.length).toFixed(1)
    : null;
  const aMoy = aValid.length > 0
    ? (aValid.reduce((s, r) => s + parseFloat(r.intensitebatterie), 0) / aValid.length).toFixed(1)
    : null;

  document.getElementById('batt-tension-moy').innerHTML  = (vMoy ?? '—') + '<span class="stat-unit"> V</span>';
  document.getElementById('batt-intensite-moy').innerHTML= (aMoy ?? '—') + '<span class="stat-unit"> A</span>';

  // Badge tendance
  const trendTag = document.getElementById('batt-trend-tag');
  if (dischargeRate !== null) {
    trendTag.textContent = '−' + dischargeRate + ' %/h';
    trendTag.className = 'panel-tag delta ' + (dischargeRate < 10 ? 'ok' : dischargeRate < 25 ? 'warn' : 'bad');
  }

  // Badge date analyse
  document.getElementById('batt-analysis-date').textContent = last.date ? 'au ' + fmtTime(last.date) : '—';
}

function updateConditions(capteur, historique, stats) {
  const tempPiste = capteur?.temperaturepiste ?? null;
  const humid     = capteur?.humiditepiste ?? null;

  document.getElementById('cond-temp').innerHTML  = (tempPiste !== null ? tempPiste : '—') + '<span class="cond-unit"> °C</span>';
  document.getElementById('cond-humid').innerHTML = (humid     !== null ? humid     : '—') + '<span class="cond-unit"> %</span>';

  document.getElementById('cond-temp-range').textContent  = 'min ' + (stats?.temp_min  ?? '—') + ' / max ' + (stats?.temp_max  ?? '—');
  document.getElementById('cond-humid-range').textContent = 'min ' + (stats?.humid_min ?? '—') + ' / max ' + (stats?.humid_max ?? '—');

  document.getElementById('stats-total').textContent = stats?.total_mesures ?? '—';
  document.getElementById('stats-temp-moy').innerHTML   = (stats?.temp_moy  ?? '—') + '<span class="stat-unit"> °C</span>';
  document.getElementById('stats-humid-moy').innerHTML  = (stats?.humid_moy ?? '—') + '<span class="stat-unit"> %</span>';
  document.getElementById('stats-batt-max').innerHTML   = (stats?.bat_max   ?? '—') + '<span class="stat-unit"> %</span>';

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

function updateSession(historique) {
  if (!historique || historique.length === 0) return;
  const dates = historique.map(r => r.date ? new Date(r.date).getTime() : null).filter(Boolean);
  if (dates.length === 0) return;
  const firstTs = Math.min(...dates);
  _sessionStart = firstTs;
  document.getElementById('session-start').textContent = fmtTime(new Date(firstTs));
  refreshSessionDuration();
}

/* ── FETCH DONNÉES ── */
async function fetchData() {
  try {
    const res  = await fetch('api_data.php', { cache: 'no-store' });
    const data = await res.json();

    updateBatterie(data.capteur, data.historique, data.stats);
    updateLogMesures(data.historique);
    updateConditions(data.capteur, data.historique, data.stats);
    updateSession(data.historique);
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
