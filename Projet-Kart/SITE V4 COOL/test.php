<?php
$host = '127.0.0.1';
$db   = 'kart';
$user = 'poteau';
$pass = 'TON_MOT_DE_PASSE';

try {
    $mysqlClient = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $mysqlClient->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(['error' => $e->getMessage()]));
}