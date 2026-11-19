<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';
exigerConnexion();

$pdo = connexionBDD();
$suppression = $pdo->prepare('DELETE FROM utilisateurs WHERE id = ?');
$suppression->execute([$_SESSION['utilisateur']['id']]);

$_SESSION = [];
session_destroy();
rediriger('connexion.php');
