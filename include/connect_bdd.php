<?php
    session_start();
    try{$db=new PDO('mysql:host=localhost;dbname=volumntime','root','');}
    catch(Exception $e){ die('Erreur:' .$e->getMessage() );}
?>