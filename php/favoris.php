<?php
// session_start();
// include 'connect_bdd.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VolumnTime - Favoris</title>
</head>
<body>
    <?php
    
    "SELECT mission.titre FROM favoris_mission JOIN mission ON favoris_mission.id_mission=mission.id_mission";
    
    ?>
</body>
</html>