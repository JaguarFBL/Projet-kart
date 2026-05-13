<?php
/**
 * session_pilote.php
 * Gère le démarrage et l'arrêt de session pilote.
 * - Enregistre le pilote en session PHP ET dans la table `actif`
 * - À l'arrêt : vide la session PHP ET vide la table `actif`
 * Appelé en AJAX (fetch) depuis le JS du site.
 */
session_start();
header('Content-Type: application/json');

// Connexion BDD (réutilise le même include que index.php)
include 'test.php'; // fournit $mysqlClient (PDO)

$action = $_POST['action'] ?? '';

// ─────────────────────────────────────────────
// DÉMARRER
// ─────────────────────────────────────────────
if ($action === 'demarrer') {
    $nom = trim($_POST['nom'] ?? '');

    if ($nom === '') {
        echo json_encode(['success' => false, 'message' => 'Le nom du pilote est requis.']);
        exit;
    }

    // 1. Session PHP
    $_SESSION['pilote_nom']     = $nom;
    $_SESSION['session_debut']  = date('Y-m-d H:i:s');
    $_SESSION['session_active'] = true;

    // 2. Table `actif` : vider puis insérer la ligne unique (ID = 1)
    try {
        $stmt = $mysqlClient->prepare('DELETE FROM actif');
        $stmt->execute();

        $stmt = $mysqlClient->prepare('INSERT INTO actif (ID, pilote) VALUES (1, :pilote)');
        $stmt->execute([':pilote' => $nom]);
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur BDD : ' . $e->getMessage(),
        ]);
        exit;
    }

    echo json_encode([
        'success' => true,
        'pilote'  => $nom,
        'debut'   => $_SESSION['session_debut'],
    ]);
    exit;
}

// ─────────────────────────────────────────────
// ARRÊTER
// ─────────────────────────────────────────────
if ($action === 'arreter') {
    $pilote = trim($_POST['nom'] ?? $_SESSION['pilote_nom'] ?? '');

    // 1. Meilleur tour de la session → mise à jour du record pilote si meilleur
    if ($pilote !== '') {
        try {
            // Meilleur temps de la session en cours (temps > 0)
            $stmtBest = $mysqlClient->prepare(
                'SELECT MIN(temps) AS best FROM session WHERE temps > 0'
            );
            $stmtBest->execute();
            $best = $stmtBest->fetchColumn();

            if ($best !== false && $best > 0) {
                // Cherche si le pilote existe déjà
                $stmtPilote = $mysqlClient->prepare(
                    'SELECT ID, record FROM pilotes WHERE nom = :nom LIMIT 1'
                );
                $stmtPilote->execute([':nom' => $pilote]);
                $row = $stmtPilote->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    // Met à jour seulement si le nouveau temps est meilleur
                    if (!is_numeric($row['record']) || floatval($best) < floatval($row['record'])) {
                        $stmtUpdate = $mysqlClient->prepare(
                            'UPDATE pilotes SET record = :record WHERE ID = :id'
                        );
                        $stmtUpdate->execute([':record' => $best, ':id' => $row['ID']]);
                    }
                } else {
                    // Pilote inconnu : on le crée
                    $stmtInsert = $mysqlClient->prepare(
                        'INSERT INTO pilotes (nom, record) VALUES (:nom, :record)'
                    );
                    $stmtInsert->execute([':nom' => $pilote, ':record' => $best]);
                }
            }
        } catch (PDOException $e) {
            // On continue même si la mise à jour échoue
        }
    }

    // 2. Vider la table `actif`
    try {
        $stmt = $mysqlClient->prepare('DELETE FROM actif');
        $stmt->execute();
    } catch (PDOException $e) {}

    // 3. Vider la session PHP
    $_SESSION = [];
    session_destroy();

    echo json_encode([
        'success' => true,
        'message' => 'Session terminée.',
        'pilote'  => $pilote,
    ]);
    exit;
}

// ─────────────────────────────────────────────
// STATUT (restauration au rechargement de page)
// ─────────────────────────────────────────────
if ($action === 'statut') {
    if (!empty($_SESSION['session_active'])) {
        echo json_encode([
            'active' => true,
            'pilote' => $_SESSION['pilote_nom'] ?? '',
            'debut'  => $_SESSION['session_debut'] ?? '',
        ]);
    } else {
        // Fallback : vérifie la BDD si la session PHP a expiré
        try {
            $stmt = $mysqlClient->prepare('SELECT pilote FROM actif LIMIT 1');
            $stmt->execute();
            $row = $stmt->fetch();
            if ($row) {
                echo json_encode(['active' => true, 'pilote' => $row['pilote'], 'debut' => '']);
            } else {
                echo json_encode(['active' => false]);
            }
        } catch (PDOException $e) {
            echo json_encode(['active' => false]);
        }
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Action inconnue.']);
