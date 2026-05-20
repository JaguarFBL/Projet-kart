<?php
// ── api_pilote_actif.php ─────────────────────────────────────────────────────
// Retourne les données du pilote actif en JSON — appelé toutes les 0.5s par le JS
header('Content-Type: application/json');
header('Cache-Control: no-store');

include 'test.php'; // fournit $mysqlClient

try {
    // Pilote actif
    $stmt = $mysqlClient->prepare('SELECT pilote FROM actif LIMIT 1');
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $piloteActif = $row['pilote'] ?? null;

    if (!$piloteActif) {
        echo json_encode(['pilote' => null]);
        exit;
    }

    // Classement complet (uniquement records valides > 100 ms)
    $stmtPilotes = $mysqlClient->prepare(
        "SELECT * FROM pilotes
         WHERE CAST(REPLACE(record, ',', '.') AS DECIMAL(10,2)) > 100
         ORDER BY CAST(REPLACE(record, ',', '.') AS DECIMAL(10,2)) ASC"
    );
    $stmtPilotes->execute();
    $pilotes = $stmtPilotes->fetchAll(PDO::FETCH_ASSOC);
    $totalPilotes = count($pilotes);

    $record = '—';
    $rang   = '—';
    foreach ($pilotes as $i => $p) {
        if ($p['nom'] === $piloteActif) {
            $record = $p['record'];
            $rang   = $i + 1;
            break;
        }
    }

    // Stats personnelles (temps_pilotes)
    $stmtStats = $mysqlClient->prepare("
        SELECT COUNT(DISTINCT DATE(tp.date)) AS nb_sessions,
               MIN(tp.temps_ms)              AS perso_record,
               ROUND(AVG(tp.temps_ms), 0)   AS moy_session
        FROM temps_pilotes tp
        JOIN pilotes p ON p.ID = tp.pilote_id
        WHERE p.nom = :nom
    ");
    $stmtStats->bindValue(':nom', $piloteActif);
    $stmtStats->execute();
    $pStats = $stmtStats->fetch(PDO::FETCH_ASSOC);

    $nbSessions = $pStats['nb_sessions'] ?? '—';
    $moySession = $pStats['moy_session'] ?? '—';
    $progression = '—';
    if (is_numeric($pStats['perso_record'] ?? null) && is_numeric($record)) {
        $ecart = round($moySession - floatval($record), 0);
        $progression = ($ecart > 0 ? '+' : '') . $ecart;
    }

    echo json_encode([
        'pilote'       => $piloteActif,
        'record'       => $record,
        'nb_sessions'  => $nbSessions,
        'moy_session'  => $moySession,
        'rang'         => $rang,
        'total_pilotes'=> $totalPilotes,
        'progression'  => $progression,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
