<?php
session_start();
include("../include/connect_bdd.php");

$id = strip_tags($_POST["ID"]);
$password = strip_tags(sha1($_POST["password"]));

try {
    $sql = "SELECT id_utilisateur, mot_de_passe, email FROM utilisateur WHERE id_utilisateur = :id_utilisateur AND mot_de_passe = :mot_de_passe";
    $requete = $db->prepare($sql);
    $requete->execute(array(
        'id_utilisateur' => $id,
        'mot_de_passe' => $password
    ));
    $identification = $requete->fetch();

    if ($identification) {
        $_SESSION["login"] = $identification["id_utilisateur"];
        $_SESSION["password"] = $identification["mot_de_passe"];
        // $_SESSION["id"] = $identification["email"];
        header("Location: dashboard.php");
        exit();
    } else {
        // Redirection sur l'ancienne page avec un texte d'erreur
        $_SESSION['message'] = 'Nom d\'utilisateur ou mot de passe incorrect.';
        header('Location: connexion.php');
        exit();
    }
} catch (Exception $e) {
    die('Erreur : ' . $e->getMessage());
}
?>