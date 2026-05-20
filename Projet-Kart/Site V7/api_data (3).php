<?php
include 'test.php';
header('Content-Type: application/json');

$out = [];

// ── Filtre date ──
$date = isset($_GET['date']) ? $_GET['date'] : null;
// Valider format YYYY-MM-DD
if ($date && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) $date = null;

$dateWhere      = $date ? "WHERE DATE(date) = :date"                       : "";
$dateWhereLast  = $date ? "WHERE DATE(date) = :date ORDER BY date DESC LIMIT 1" : "ORDER BY date DESC LIMIT 1";
$dateWhereHist  = $date ? "WHERE DATE(date) = :date ORDER BY date ASC"     : "ORDER BY date DESC LIMIT 50";

// ── 1. Dernière mesure capteur (du jour sélectionné ou la toute dernière) ──
$stmt = $mysqlClient->prepare("SELECT * FROM capteur $dateWhereLast");
if ($date) $stmt->bindValue(':date', $date);
$stmt->execute();
$out['capteur'] = $stmt->fetch(PDO::FETCH_ASSOC);

// ── 2. Mesures pour graphique/journal (jour sélectionné ou 50 dernières) ──
$stmt = $mysqlClient->prepare("SELECT date, pourcentagebatterie, temperaturebatterie, tensionbatterie, intensitebatterie FROM capteur $dateWhereHist");
if ($date) $stmt->bindValue(':date', $date);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$out['historique'] = $date ? $rows : array_reverse($rows);

// ── 3. Tours de la session ──
$stmt = $mysqlClient->prepare('SELECT * FROM session ORDER BY ID ASC');
$stmt->execute();
$out['tours'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── 4. Pilotes + leurs records ──
$stmt = $mysqlClient->prepare('SELECT * FROM pilotes ORDER BY record ASC');
$stmt->execute();
$out['pilotes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ── 5. Pilote actif ──
$stmt = $mysqlClient->prepare('SELECT pilote FROM actif LIMIT 1');
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$out['pilote_actif'] = $row ? $row['pilote'] : null;

// ── 6. Stats globales (du jour ou globales) ──
$statsWhere = $date ? "WHERE DATE(date) = :date" : "";
$stmt = $mysqlClient->prepare("
    SELECT
        MIN(temperaturepiste)    AS temp_min,
        MAX(temperaturepiste)    AS temp_max,
        ROUND(AVG(temperaturepiste),1) AS temp_moy,
        MIN(humiditepiste)       AS humid_min,
        MAX(humiditepiste)       AS humid_max,
        ROUND(AVG(humiditepiste),1)   AS humid_moy,
        MIN(pourcentagebatterie) AS bat_min,
        MAX(pourcentagebatterie) AS bat_max,
        COUNT(*)                 AS total_mesures
    FROM capteur $statsWhere
");
if ($date) $stmt->bindValue(':date', $date);
$stmt->execute();
$out['stats'] = $stmt->fetch(PDO::FETCH_ASSOC);

// ── 7. Jours disponibles (pour le calendrier) ──
$stmtDays = $mysqlClient->prepare("
    SELECT DATE(date) AS jour, COUNT(*) AS nb
    FROM capteur
    GROUP BY DATE(date)
    ORDER BY jour DESC
    LIMIT 90
");
$stmtDays->execute();
$out['jours_disponibles'] = $stmtDays->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($out);
