<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déconnexion</title>
</head>
<body>
<form id="deconnexion" method="post" action="traitement.php">
    <input type="hidden" name="action" value="deconnexion">
</form>
<script>document.getElementById('deconnexion').submit();</script>
</body>
</html>
