<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';
exigerConnexion();

$verification = $_SESSION['verification'] ?? null;
$message = $_SESSION['message'] ?? null;
unset($_SESSION['verification'], $_SESSION['message']);
$mot1 = $verification['mot1'] ?? '';
$mot2 = $verification['mot2'] ?? '';
$resultat = $verification['resultat'] ?? null;
$historique = obtenirHistorique();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérificateur d'anagrammes</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="conteneur">
    <header>
        <h1>Vérificateur d'anagrammes</h1>
        <nav>
            <span>Bonjour, <?= htmlspecialchars($_SESSION['utilisateur']['pseudo']) ?></span>
            <a href="historique.php">Historique</a>
            <a href="deconnexion.php">Se déconnecter</a>
        </nav>
    </header>
    <?php if ($message): ?><p class="message <?= htmlspecialchars($message['type']) ?>"><?= htmlspecialchars($message['texte']) ?></p><?php endif; ?>
    <section class="grille-verification">
    <div class="carte">
        <h2>Comparer deux chaînes</h2>
        <form method="post" action="traitement.php">
            <input type="hidden" name="action" value="verifier">
            <div class="champs-comparaison">
                <div>
                    <label for="mot1">String 1</label>
                    <input id="mot1" name="mot1" class="<?= $resultat === true ? 'champ-succes' : ($resultat === false ? 'champ-erreur' : '') ?>" value="<?= htmlspecialchars($mot1) ?>" required>
                </div>
                <div>
                    <label for="mot2">String 2</label>
                    <input id="mot2" name="mot2" class="<?= $resultat === true ? 'champ-succes' : ($resultat === false ? 'champ-erreur' : '') ?>" value="<?= htmlspecialchars($mot2) ?>" required>
                </div>
            </div>
            <button type="submit">Soumettre</button>
        </form>
        <?php if ($resultat !== null): ?>
            <p class="resultat <?= $resultat ? 'succes' : 'erreur' ?>">
                <?= $resultat ? 'Ces chaînes sont des anagrammes.' : 'Ces chaînes ne sont pas des anagrammes.' ?>
            </p>
        <?php endif; ?>
    </div>
    <div class="carte historique-compact">
        <h2>Historique</h2>
        <?php if (!$historique): ?>
            <p>Aucune vérification enregistrée.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr><th>String 1</th><th>String 2</th><th>Résultat</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($historique as $ligne): ?>
                        <?php $ligneEstAnagramme = (bool) (int) $ligne['resultat']; ?>
                        <tr class="<?= $ligneEstAnagramme ? 'ligne-anagramme' : 'ligne-non-anagramme' ?>">
                            <td><?= htmlspecialchars($ligne['chaine1']) ?></td>
                            <td><?= htmlspecialchars($ligne['chaine2']) ?></td>
                            <td><?= $ligneEstAnagramme ? 'Anagrammes' : 'Non anagrammes' ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
        <p><a href="historique.php">Voir l'historique complet</a></p>
    </div>
    </section>
</main>
</body>
</html>
