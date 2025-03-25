<?php
session_start();
include("../include/connect_bdd.php");

// Vérifiez que tous les champs requis sont présents
if (!isset($_POST["nom"]) || !isset($_POST["prenom"]) || !isset($_POST["email"]) || 
    !isset($_POST["identifiant"])) {
    $_SESSION['message'] = 'Tous les champs obligatoires doivent être remplis.';
    header('Location: inscription.php');
    exit();
}

// Récupération des données du formulaire
$nom = strip_tags($_POST["nom"]);
$prenom = strip_tags($_POST["prenom"]);
$mail = strip_tags($_POST["email"]);
$identifiant = strip_tags($_POST["identifiant"]);

// Le mot de passe ne vient pas du récapitulatif, on doit le récupérer de la session
// Si on n'a pas de mot de passe en session, rediriger vers l'inscription
if (!isset($_SESSION["password"])) {
    $_SESSION['message'] = 'Session expirée. Veuillez recommencer l\'inscription.';
    header('Location: inscription.php');
    exit();
}

$password = $_SESSION["password"]; // Mot de passe déjà haché dans inscription.php

// Autres données du formulaire
$sexe = isset($_POST["sexe"]) ? strip_tags($_POST["sexe"]) : '';
$telephone = isset($_POST["telephone"]) ? strip_tags($_POST["telephone"]) : '';
$interets = isset($_POST["interets"]) && is_array($_POST["interets"]) ? implode(",", $_POST["interets"]) : '';
$ville = isset($_POST["ville"]) ? strip_tags($_POST["ville"]) : '';
$adresse = isset($_POST["adresse"]) ? strip_tags($_POST["adresse"]) : '';
$code_postal = isset($_POST["postal"]) ? strip_tags($_POST["postal"]) : '';
$disponibilite = isset($_POST["disponibilite"]) ? strip_tags($_POST["disponibilite"]) : '';
$role = "1"; // Role par défaut (bénévole)
$handicap = isset($_POST["handicap_checkbox"]) ? 1 : 0;
$description_handicap = isset($_POST["description_handicap"]) ? strip_tags($_POST["description_handicap"]) : '';

try {
    // Vérification des doublons sur email et identifiant
    $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email OR identifiant = :identifiant";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':email' => $mail, 
        ':identifiant' => $identifiant
    ]);
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        // Insertion des données dans la base de données
        $sql = "INSERT INTO utilisateur (nom, prenom, email, identifiant, mot_de_passe, role, 
                tagUsers, telephone, adresse, ville, code_postal, date_inscription, handicap, description_handicap) 
                VALUES (:nom, :prenom, :email, :identifiant, :mot_de_passe, :role, 
                :tagUsers, :telephone, :adresse, :ville, :code_postal, NOW(), :handicap, :description_handicap)";
        
        $compte = $db->prepare($sql);
        $result = $compte->execute([
            'nom' => $nom,
            'prenom' => $prenom,
            'email' => $mail,
            'identifiant' => $identifiant,
            'mot_de_passe' => $password,
            'role' => $role,
            'tagUsers' => $interets,
            'telephone' => $telephone,
            'adresse' => $adresse,
            'ville' => $ville,
            'code_postal' => $code_postal,
            'handicap' => $handicap,
            'description_handicap' => $description_handicap
        ]);
        
        if ($result) {
            // Nettoyer la session après création réussie
            unset($_SESSION["password"]);
            
            $_SESSION['message'] = 'L\'utilisateur a bien été créé.';
            header('Location: connexion.php');
            exit();
        } else {
            $_SESSION['message'] = 'Erreur lors de la création du compte. Veuillez réessayer.';
            header('Location: inscription.php');
            exit();
        }
    } else {
        $_SESSION['message'] = 'Cet email ou identifiant est déjà utilisé.';
        header('Location: inscription.php');
        exit();
    }
} catch (Exception $e) {
    $_SESSION['message'] = 'Erreur : ' . $e->getMessage();
    header('Location: inscription.php');
    exit();
}
?>