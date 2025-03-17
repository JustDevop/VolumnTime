<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion VolunTime</title>
    <link rel="stylesheet" href="/Voluntime/Volumntime/css/styleCo.css">
</head>
<body>
    <header>
        <h1>Connexion</h1>
    </header>
    <form action="identification.php" method="POST">
        <div class="form-group">
            <label for="ID">Identifiant</label>
            <input type="text" id="ID" name="ID" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit" class="btn">Se connecter</button>
    </form>
</body>

    <p><a href="inscription.php">Pas de compte ? Inscrivez vous</a></p>
</html>