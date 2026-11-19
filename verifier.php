<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';
exigerConnexion();

function anagramme(string $mot1, string $mot2): bool
{
    $mot1 = mb_strtolower(trim($mot1), 'UTF-8');
    $mot2 = mb_strtolower(trim($mot2), 'UTF-8');

    if ($mot1 === '' || $mot2 === '' || mb_strlen($mot1, 'UTF-8') !== mb_strlen($mot2, 'UTF-8')) {
        return false;
    }

    $lettres1 = preg_split('//u', $mot1, -1, PREG_SPLIT_NO_EMPTY);
    $lettres2 = preg_split('//u', $mot2, -1, PREG_SPLIT_NO_EMPTY);
    sort($lettres1);
    sort($lettres2);
    return $lettres1 === $lettres2;
}

$mot1 = trim((string) ($_POST['mot1'] ?? ''));
$mot2 = trim((string) ($_POST['mot2'] ?? ''));
$verification = null;
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mot1 === '' || $mot2 === '') {
        $erreur = 'Les deux chaînes sont obligatoires.';
    } elseif (!preg_match('/^[\p{L}\s]+$/u', $mot1) || !preg_match('/^[\p{L}\s]+$/u', $mot2)) {
        $erreur = 'Utilisez uniquement des lettres et des espaces.';
    } else {
        $verification = anagramme($mot1, $mot2);
        $requete = connexionBDD()->prepare(
            'INSERT INTO verifications (utilisateur_id, chaine1, chaine2, resultat)
             VALUES (?, ?, ?, ?)'
        );
        $requete->execute([
            $_SESSION['utilisateur']['id'],
            $mot1,
            $mot2,
            $verification,
        ]);
    }
}
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
    <?php if ($erreur): ?><p class="message erreur"><?= htmlspecialchars($erreur) ?></p><?php endif; ?>
    <section class="carte">
        <form method="post">
            <label for="mot1">String 1</label>
            <input id="mot1" name="mot1" class="<?= $verification === true ? 'champ-succes' : ($verification === false ? 'champ-erreur' : '') ?>" value="<?= htmlspecialchars($mot1) ?>" required>
            <label for="mot2">String 2</label>
            <input id="mot2" name="mot2" class="<?= $verification === true ? 'champ-succes' : ($verification === false ? 'champ-erreur' : '') ?>" value="<?= htmlspecialchars($mot2) ?>" required>
            <button type="submit">Soumettre</button>
        </form>
        <?php if ($verification !== null): ?>
            <p class="resultat <?= $verification ? 'succes' : 'erreur' ?>">
                <?= $verification ? 'Ces chaînes sont des anagrammes.' : 'Ces chaînes ne sont pas des anagrammes.' ?>
            </p>
        <?php endif; ?>
    </section>
</main>
</body>
</html>
