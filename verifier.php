<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';
exigerConnexion();

function anagramme(string $mot1, string $mot2): bool
{
    $mot1 = strtolower(trim($mot1));
    $mot2 = strtolower(trim($mot2));

    if ($mot1 === '' || $mot2 === '' || strlen($mot1) != strlen($mot2)) {
        return false;
    }

    $compteur1 = array_count_values(str_split($mot1));
    $compteur2 = array_count_values(str_split($mot2));

    foreach ($compteur1 as $lettre => $nombre) {
        if (!isset($compteur2[$lettre]) || $nombre !== $compteur2[$lettre]) {
            return false;
        }
    }

    return true;
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
            <div class="champs-comparaison">
                <div>
                    <label for="mot1">String 1</label>
                    <input id="mot1" name="mot1" class="<?= $verification === true ? 'champ-succes' : ($verification === false ? 'champ-erreur' : '') ?>" value="<?= htmlspecialchars($mot1) ?>" required>
                </div>
                <div>
                    <label for="mot2">String 2</label>
                    <input id="mot2" name="mot2" class="<?= $verification === true ? 'champ-succes' : ($verification === false ? 'champ-erreur' : '') ?>" value="<?= htmlspecialchars($mot2) ?>" required>
                </div>
            </div>
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
