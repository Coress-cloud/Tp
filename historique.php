<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';
exigerConnexion();

$requete = connexionBDD()->prepare(
    'SELECT chaine1, chaine2, resultat, date_verification
     FROM verifications
     WHERE utilisateur_id = ?
     ORDER BY date_verification DESC'
);
$requete->execute([$_SESSION['utilisateur']['id']]);
$historique = $requete->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historique</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="conteneur">
    <header>
        <h1>Historique des vérifications</h1>
        <nav>
            <a href="verifier.php">Nouvelle vérification</a>
            <a href="deconnexion.php">Se déconnecter</a>
        </nav>
    </header>
    <section class="carte">
        <?php if (!$historique): ?>
            <p>Aucune vérification enregistrée.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>String 1</th><th>String 2</th><th>Résultat</th><th>Date</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $ligne): ?>
                        <tr>
                            <td><?= htmlspecialchars($ligne['chaine1']) ?></td>
                            <td><?= htmlspecialchars($ligne['chaine2']) ?></td>
                            <td><?= $ligne['resultat'] ? 'Anagrammes' : 'Non anagrammes' ?></td>
                            <td><?= htmlspecialchars($ligne['date_verification']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
