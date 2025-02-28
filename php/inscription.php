<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription VolunTime</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>Inscription à VolunTime</h1>
    </header>
    <main>
        <div class="signup-container">
            <form action="creation_benevole.php" method="POST">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" id="identifiant" name="identifiant" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Sexe</label>
                    <div>
                        <input type="radio" id="homme" name="sexe" value="homme" required>
                        <label for="homme">Homme</label>
                    </div>
                    <div>
                        <input type="radio" id="femme" name="sexe" value="femme" required>
                        <label for="femme">Femme</label>
                    </div>
                    <div>
                        <input type="radio" id="neutre" name="sexe" value="neutre" required>
                        <label for="neutre">Neutre</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="interets">Centres d'intérêt</label>
                    <select id="interets" name="interets[]" multiple required>
                        <option value="sport">Sport</option>
                        <option value="musique">Musique</option>
                        <option value="lecture">Lecture</option>
                        <option value="voyage">Voyage</option>
                        <option value="technologie">Technologie</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="localisation">Localisation</label>
                    <input type="text" id="localisation" name="localisation" required>
                </div>
                <div class="form-group">
                    <label for="disponibilite">Disponibilité</label>
                    <select id="disponibilite" name="disponibilite" required>
                        <option value="semaine">Semaine</option>
                        <option value="weekend">Week-end</option>
                    </select>
                </div>
                <button type="submit" class="btn">S'inscrire</button>
            </form>
        </div>
        <p>Déjà un compte ? <a href="connexion.php"> Connectez vous</a></p>
    </main>
</body>
</html>