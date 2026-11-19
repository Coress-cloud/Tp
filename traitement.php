<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/fonctions.php';

function anagramme(string $mot1, string $mot2): bool
{
    $mot1 = strtolower(trim($mot1));
    $mot2 = strtolower(trim($mot2));

    if ($mot1 === '' || $mot2 === '' || strlen($mot1) !== strlen($mot2)) {
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

function enregistrerMessage(string $texte, string $type = 'erreur'): void
{
    $_SESSION['message'] = ['texte' => $texte, 'type' => $type];
}

$action = (string) ($_POST['action'] ?? '');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    rediriger('index.php');
}

try {
    if ($action === 'inscription') {
        $pseudo = trim((string) ($_POST['pseudo'] ?? ''));
        $motDePasse = (string) ($_POST['mot_de_passe'] ?? '');
        $confirmation = (string) ($_POST['confirmation'] ?? '');

        if (!preg_match('/^[A-Za-z0-9_]{3,30}$/', $pseudo)) {
            enregistrerMessage('Le pseudonyme doit contenir 3 à 30 caractères alphanumériques ou _.');
        } elseif ($motDePasse === '' || $motDePasse !== $confirmation) {
            enregistrerMessage('Les mots de passe sont obligatoires et doivent correspondre.');
        } else {
            $pdo = connexionBDD();
            $pseudoInverse = strrev($pseudo);
            $requete = $pdo->prepare('SELECT id FROM utilisateurs WHERE pseudo IN (?, ?)');
            $requete->execute([$pseudo, $pseudoInverse]);

            if ($requete->fetch()) {
                enregistrerMessage('Ce pseudonyme ou son inverse est déjà utilisé.');
            } else {
                $pdo->beginTransaction();
                $insertion = $pdo->prepare('INSERT INTO utilisateurs (pseudo, mot_de_passe) VALUES (?, ?)');
                $motDePasseHache = password_hash($motDePasse, PASSWORD_DEFAULT);
                $insertion->execute([$pseudo, $motDePasseHache]);
                $insertion->execute([$pseudoInverse, $motDePasseHache]);
                $pdo->commit();
                enregistrerMessage('Compte créé. Vous pouvez maintenant vous connecter.', 'succes');
            }
        }
        rediriger('inscription.php');
    }

    if ($action === 'connexion') {
        $pseudo = trim((string) ($_POST['pseudo'] ?? ''));
        $motDePasse = (string) ($_POST['mot_de_passe'] ?? '');
        $requete = connexionBDD()->prepare(
            'SELECT id, pseudo, mot_de_passe FROM utilisateurs WHERE pseudo = ?'
        );
        $requete->execute([$pseudo]);
        $utilisateur = $requete->fetch();

        $motDePasseValide = false;
        if ($utilisateur) {
            $motStocke = (string) $utilisateur['mot_de_passe'];
            if (password_get_info($motStocke)['algo'] !== 0) {
                $motDePasseValide = password_verify($motDePasse, $motStocke);
            } elseif (hash_equals($motStocke, $motDePasse)) {
                $motDePasseValide = true;
                $miseAJour = connexionBDD()->prepare(
                    'UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?'
                );
                $miseAJour->execute([
                    password_hash($motDePasse, PASSWORD_DEFAULT),
                    (int) $utilisateur['id'],
                ]);
            }
        }

        if ($motDePasseValide) {
            session_regenerate_id(true);
            $_SESSION['utilisateur'] = [
                'id' => (int) $utilisateur['id'],
                'pseudo' => $utilisateur['pseudo'],
            ];
            unset($_SESSION['message']);
            rediriger('verifier.php');
        }

        enregistrerMessage('Pseudonyme ou mot de passe incorrect.');
        rediriger('connexion.php');
    }

    if ($action === 'deconnexion') {
        exigerConnexion();
        $pseudo = (string) $_SESSION['utilisateur']['pseudo'];
        $pseudoInverse = strrev($pseudo);
        $suppression = connexionBDD()->prepare('DELETE FROM utilisateurs WHERE pseudo IN (?, ?)');
        $suppression->execute([$pseudo, $pseudoInverse]);
        $_SESSION = [];
        session_destroy();
        rediriger('connexion.php');
    }

    if ($action === 'verifier') {
        exigerConnexion();
        $mot1 = trim((string) ($_POST['mot1'] ?? ''));
        $mot2 = trim((string) ($_POST['mot2'] ?? ''));

        if ($mot1 === '' || $mot2 === '') {
            enregistrerMessage('Les deux chaînes sont obligatoires.');
            rediriger('verifier.php');
        }

        if (!preg_match('/^[\p{L}\s]+$/u', $mot1) || !preg_match('/^[\p{L}\s]+$/u', $mot2)) {
            enregistrerMessage('Utilisez uniquement des lettres et des espaces.');
            rediriger('verifier.php');
        }

        $resultat = anagramme($mot1, $mot2);
        $requete = connexionBDD()->prepare(
            'INSERT INTO verifications (utilisateur_id, chaine1, chaine2, resultat)
             VALUES (?, ?, ?, ?)'
        );
        $requete->execute([
            $_SESSION['utilisateur']['id'],
            $mot1,
            $mot2,
            $resultat ? 1 : 0,
        ]);

        $_SESSION['verification'] = [
            'mot1' => $mot1,
            'mot2' => $mot2,
            'resultat' => $resultat,
        ];
        rediriger('verifier.php');
    }
} catch (PDOException $exception) {
    if (connexionBDD()->inTransaction()) {
        connexionBDD()->rollBack();
    }
    enregistrerMessage('Une erreur est survenue lors du traitement.');
    rediriger(isset($_SERVER['HTTP_REFERER']) ? basename((string) parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH)) : 'index.php');
}

enregistrerMessage('Action invalide.');
rediriger('index.php');
