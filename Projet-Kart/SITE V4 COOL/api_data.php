<?php
include 'test.php';
header('Content-Type: application/json');

$out = [];

// ── 1. Dernière mesure capteur ──
$stmt = $mysqlClient->prepare('SELECT * FROM capteur ORDER BY date DESC LIMIT 1');
$stmt->execute();
$out['capteur'] = $stmt->fetch(PDO::FETCH_ASSOC);

// ── 2. 20 dernières mesures pour sparkline ──
$stmt = $mysqlClient->prepare('SELECT date, pourcentagebatterie, temperaturepiste FROM capteur ORDER BY date DESC LIMIT 20');
$stmt->execute();
$out['historique'] = array_reverse($stmt->fetchAll(PDO::FETCH_ASSOC));

// ── 3. Tours de la session table (temps en ms) ──
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

// ── 6. Stats globales ──
$stmt = $mysqlClient->prepare('
    SELECT
        MIN(temperaturepiste) AS temp_min,
        MAX(temperaturepiste) AS temp_max,
        ROUND(AVG(temperaturepiste),1) AS temp_moy,
        MIN(humiditepiste)    AS humid_min,
        MAX(humiditepiste)    AS humid_max,
        ROUND(AVG(humiditepiste),1) AS humid_moy,
        MIN(pourcentagebatterie) AS bat_min,
        MAX(pourcentagebatterie) AS bat_max,
        COUNT(*) AS total_mesures
    FROM capteur
');
$stmt->execute();
$out['stats'] = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($out);
