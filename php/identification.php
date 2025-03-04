<?php
    include("include\connect_bdd.php");

    $user_id = strip_tags($_POST["ID"]);
    $user_pwd = strip_tags(sha1($_POST["password"]));

    $sql = "SELECT id_utilisateur, mot_de_passe ,email FROM utilisateur";
    $requete=$db->prepare($sql);
    $requete->execute();
    $identification = $requete->fetchAll();
    $connexion = false;

    foreach($identification as $row)
    {
        if ($row["id_utilisateur"] == $user_id && $row["mot_de_passe"] == $user_pwd)
        {
            $_SESSION["login"] = $row["id_utilisateur"];
            $_SESSION["password"] = $row["mot_de_passe"];
            //$_SESSION["id"] = $row["email"];
            $connexion = true;
            break;
        }

    }
    if ($connexion)
    {
        header("Location: dashboard.php");
        exit();   
    }    
    else{
        //Redirection sur l'ancienne page avec un texte d'erreur
        $_SESSION['message'] = 'Nom d\'utilisateur ou mot de passe incorrect.';
        header('Location: connexion.php');
        exit();
    }
