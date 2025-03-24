<?php
session_start();
include "../include/connect_bdd.php";

$id = strip_tags($_POST["ID"]);
$pwd = strip_tags(sha1($_POST["password"]));

try {
    $sql = "SELECT identifiant, mot_de_passe, id_utilisateur, role FROM utilisateur WHERE identifiant = :identifiant AND mot_de_passe = :mot_de_passe";
    $requete = $db->prepare($sql);
    $requete->execute([
        'identifiant' => $id,
        'mot_de_passe' => $pwd
    ]);
    $identification = $requete->fetch();
    

    if (!empty($identification)) {
        // Stockage des informations de l'utilisateur dans la session
        $_SESSION["identifiant"] = $identification["identifiant"];
        $_SESSION["role"] = $identification["role"];
        $_SESSION["id_utilisateur"] = $identification["id_utilisateur"];
        
        header("Location: ../index.php");
        exit();
    } else {
        // Redirection en cas d'échec de l'authentification
        $_SESSION['message'] = 'Nom d\'utilisateur ou mot de passe incorrect.';
        header('Location: connexion.php');
        exit();
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}