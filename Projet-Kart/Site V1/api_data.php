<?php
include 'test.php';
header('Content-Type: application/json');

$stmt = $mysqlClient->prepare('SELECT * FROM capteur ORDER BY date DESC LIMIT 1');
$stmt->execute();
$derniere = $stmt->fetch(PDO::FETCH_ASSOC);

echo json_encode($derniere);