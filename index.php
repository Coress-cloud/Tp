<?php
declare(strict_types=1);

session_start();

if (isset($_SESSION['utilisateur'])) {
    header('Location: verifier.php');
} else {
    header('Location: connexion.php');
}
exit;
