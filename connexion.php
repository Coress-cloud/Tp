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
    <title>Connexion</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="conteneur">
    <section class="carte auth">
        <h1>Connexion</h1>
        <?php if ($message): ?><p class="message <?= htmlspecialchars($message['type']) ?>"><?= htmlspecialchars($message['texte']) ?></p><?php endif; ?>
        <form method="post" action="traitement.php">
            <input type="hidden" name="action" value="connexion">
            <label for="pseudo">Pseudonyme</label><input id="pseudo" name="pseudo" required>
            <label for="mot_de_passe">Mot de passe</label><input id="mot_de_passe" type="password" name="mot_de_passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <p><a href="inscription.php">Créer un compte</a></p>
    </section>
</main>
</body>
</html>
