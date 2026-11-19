<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';

$message = $_SESSION['message'] ?? null;
unset($_SESSION['message']);
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
        <?php if ($message): ?><p class="message <?= htmlspecialchars($message['type']) ?>"><?= htmlspecialchars($message['texte']) ?></p><?php endif; ?>
        <form method="post" action="traitement.php">
            <input type="hidden" name="action" value="inscription">
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
