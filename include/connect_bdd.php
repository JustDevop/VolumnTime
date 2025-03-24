<?php
    session_start();
    try{$db=new PDO('mysql:host=localhost;dbname=volumntime','hauleben','s7g9WG2$JwSC');}
    catch(Exception $e){ die('Erreur:' .$e->getMessage() );}
?>