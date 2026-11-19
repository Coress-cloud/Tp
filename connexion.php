<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pseudo = trim((string) ($_POST['pseudo'] ?? ''));
    $motDePasse = (string) ($_POST['mot_de_passe'] ?? '');
    $requete = connexionBDD()->prepare(
        'SELECT id, pseudo, mot_de_passe FROM utilisateurs WHERE pseudo = ?'
    );
    $requete->execute([$pseudo]);
    $utilisateur = $requete->fetch();

    if ($utilisateur && hash_equals((string) $utilisateur['mot_de_passe'], $motDePasse)) {
        session_regenerate_id(true);
        $_SESSION['utilisateur'] = [
            'id' => (int) $utilisateur['id'],
            'pseudo' => $utilisateur['pseudo'],
        ];
        rediriger('verifier.php');
    }
    $message = 'Pseudonyme ou mot de passe incorrect.';
}
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
        <?php if ($message): ?><p class="message erreur"><?= htmlspecialchars($message) ?></p><?php endif; ?>
        <form method="post">
            <label for="pseudo">Pseudonyme</label><input id="pseudo" name="pseudo" required>
            <label for="mot_de_passe">Mot de passe</label><input id="mot_de_passe" type="password" name="mot_de_passe" required>
            <button type="submit">Se connecter</button>
        </form>
        <p><a href="inscription.php">Créer un compte</a></p>
    </section>
</main>
</body>
</html>
