<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';

$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim((string) ($_POST['pseudo'] ?? ''));
    $motDePasse = (string) ($_POST['mot_de_passe'] ?? '');
    $confirmation = (string) ($_POST['confirmation'] ?? '');

    if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $pseudo)) {
        $message = 'Le pseudonyme doit contenir 3 à 30 caractères alphanumériques ou _.';
        $type = 'erreur';
    } elseif ($motDePasse === '' || $motDePasse !== $confirmation) {
        $message = 'Les mots de passe sont obligatoires et doivent correspondre.';
        $type = 'erreur';
    } else {
        $pseudoInverse = strrev($pseudo);
        $pdo = connexionBDD();
        $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE pseudo IN (?, ?)');
        $requete->execute([$pseudo, $pseudoInverse]);

        if ($requete->fetch()) {
            $message = 'Ce pseudonyme ou son inverse est déjà utilisé.';
            $type = 'erreur';
        } else {
            $pdo->beginTransaction();
            try {
                $insertion = $pdo->prepare('INSERT INTO utilisateurs (pseudo, mot_de_passe) VALUES (?, ?)');
                $insertion->execute([$pseudo, $motDePasse]);
                $insertion->execute([$pseudoInverse, $motDePasse]);
                $pdo->commit();
                $message = 'Compte créé. Vous pouvez maintenant vous connecter.';
                $type = 'succes';
            } catch (Throwable $exception) {
                $pdo->rollBack();
                $message = 'Impossible de créer les comptes.';
                $type = 'erreur';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="conteneur">
    <section class="carte auth">
        <h1>Inscription</h1>
        <?php if ($message): ?><p class="message <?= $type ?>"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <form method="post">
            <label for="pseudo">Pseudonyme</label><input id="pseudo" name="pseudo" required>
            <label for="mot_de_passe">Mot de passe</label><input id="mot_de_passe" type="password" name="mot_de_passe" required>
            <label for="confirmation">Confirmation du mot de passe</label><input id="confirmation" type="password" name="confirmation" required>
            <button type="submit">Créer le compte</button>
        </form>
        <p><a href="connexion.php">Déjà inscrit ? Se connecter</a></p>
    </section>
</main>
</body>
</html>
