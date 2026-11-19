<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function connexionBDD(): PDO
{
    static $pdo;
    if (!$pdo instanceof PDO) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER,
            DB_PASSWORD,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
    }
    return $pdo;
}

function rediriger(string $page): never
{
    header('Location: ' . $page);
    exit;
}

function exigerConnexion(): void
{
    if (!isset($_SESSION['utilisateur'])) {
        rediriger('connexion.php');
    }
}
