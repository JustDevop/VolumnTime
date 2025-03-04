<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription VolunTime</title>
    <link rel="stylesheet" href="/VolunTime/VolumnTime/css/styleCo.css">
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
                    <input type="text" id="nom" name="nom" placeholder="Sylvain" required>
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" placeholder="Jeanker" required>
                </div>
                <div class="form-group">
                    <label for="telephone">Numéro de téléphone</label>
                    <input type="tel" id="telephone" name="telephone" pattern="\d{10}" maxlength="10" placeholder="Ex:0612345678" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="sylvain.jeanker@gmail.com" required>
                </div>
                <div class="form-group">
                    <label for="identifiant">Identifiant</label>
                    <input type="text" id="identifiant" name="identifiant" placeholder="SylvainJeanker" required>
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
                    <label for="ville">Ville</label>
                    <input type="text" id="ville" name="ville" placeholder="Lille" required>
                </div>
                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse" placeholder="Pl Augustin Laurent" required>
                </div>
                <div class="form-group">
                    <label for="postal">Code Postal</label>
                    <input type="number" id="postal" name="postal" placeholder="59000" required>
                </div>
                <div class="form-group">
                    <label for="disponibilite">Disponibilité</label>
                    <select id="disponibilite" name="disponibilite" required>
                    <option value="" disabled selected>Choisissez votre disponibilité</option>
                        <option value="semaine">Semaine</option>
                        <option value="weekend">Week-end</option>
                        <option value="weekend">Semaine et Week-end</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="handicap_checkbox">Êtes-vous handicapé ?</label>
                    <input type="checkbox" id="handicap_checkbox" name="handicap_checkbox" onclick="toggleHandicapField()">
                </div>
                <div class="form-group" id="handicap_field" style="display: none;">
                    <label for="description_handicap">Veuillez préciser votre handicap</label>
                    <textarea id="description_handicap" name="description_handicap" placeholder="Sensoriels, Moteurs, Mentaux, Psychiques et Cognitifs" rows="4" cols="35" required></textarea>
                </div>
        
                <button type="submit" class="btn">S'inscrire</button>
            </form>
        </div>
        <p>Déjà un compte ? <a href="connexion.php"> Connectez vous</a></p>
    </main>
    <script src="/VolunTime/VolumnTime/Js/inscription.js"></script>
</body>
</html>