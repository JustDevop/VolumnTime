<?php
    session_start();
    try{$db=new PDO('mysql:host=localhost;dbname=db-hauleben','usr-hauleben','s7g9WG2$JwSC');}
    catch(Exception $e){ die('Erreur:' .$e->getMessage() );}
?>