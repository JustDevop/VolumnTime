<?php
    session_start();
<<<<<<< HEAD
    try{$db=new PDO('mysql:host=localhost;dbname=db-hauleben','usr-hauleben','s7g9WG2$JwSC');}
=======
    try{$db=new PDO('mysql:host=localhost;dbname=volumntime','hauleben','s7g9WG2$JwSC');}
>>>>>>> 5c2ac2d32d62dffd10f873f9f1a53a16f063de24
    catch(Exception $e){ die('Erreur:' .$e->getMessage() );}
?>