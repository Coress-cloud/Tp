<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function connexionBDD(): PDO
{
    return $GLOBALS['pdo'];
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

function obtenirHistorique(): array
{
    $requete = connexionBDD()->prepare(
        'SELECT chaine1, chaine2, resultat, date_verification
         FROM verifications
         WHERE utilisateur_id = ?
         ORDER BY date_verification DESC'
    );
    $requete->execute([$_SESSION['utilisateur']['id']]);
    return $requete->fetchAll();
}
