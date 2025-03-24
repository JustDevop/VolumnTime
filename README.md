TP-WEB
===

Bienvenue sur la plateform web pédagogique de l'Université de Rouen Normandie.

Dans le cadre de vos enseignements, vous disposez d'un espace d'hébergement Web nominatif de type LAMP (Linux Apache MySQL PHP).
Votre site personnel est consultable en accès libre depuis les réseaux informatiques internes de l'Université (WiFi et Filaire) et est soumis à authentification depuis l'extérieur (via votre compte multipass).

Vous disposez :
- d'un environnement de type LAMP: Linux Apache MySQL (MariaDB 10.6) PHP 8.2
- d'un espace de stockage de 5Go
- d'un compte et d'une base de données MySQL associée

# Accès Web

Votre site est consultable à l'adresse:
- http://hauleben.tpweb.univ-rouen.fr

Par défaut la page d'accueil de votre vhost est welcome.html. Ce fichier ne peut être supprimé ou modifié.
La configuration de votre vhost contient la directive "DirectoryIndex index.html index.php welcome.html" pour la page d'accueil.
Cette directive agit dans cet ordre de priorité: index.html index.php welcome.html"
Donc si vous déposer un fichier index.htmml ou index.php, celui-ci devient la page par défaut de votre site.
index.html étant prioritaire par rapport à index.php si les deux existent.

# Accès FTP
Pour accéder à votre espace d'hébergement, vous devez utiliser un client FTP (ex: FileZilla):
- adresse du serveur: ftp.tpweb.univ-rouen.fr
- identifiants: votre compte Multipass

Vos contenus Web (html, css, php, médias...) doivent être déposés dans le répertoire:  `public_html`
Ce dossier est un paramètre important de la configuration du serveur Web (DocumentRoot). Vous ne devez en aucun cas le supprimer.

# Accès MySQL
Pour gérer votre base de données MySQL, vous pouvez utiliser l'interface Web:
- https://sql.tpweb.univ-rouen.fr

Avec les informations suivantes:
- identifiant: usr-hauleben
- mot de passe: s7g9WG2$JwSC
- nom la base de données: db-hauleben
- adresse du serveur: localhost

Quelques remarques concernant votre base de données:
- vous disposez des privilèges SQL: `ALL PRIVILEGES` sur votre base y compris celui de la supprimer (prudence donc - voir l'avertissement ci-dessous !).
- Vous pouvez supprimer des tables sans aucuns soucis mais si vous supprimez votre base de données, vous serez bloqué et le service informatique devra la recréer.
- vous n'avez qu'une seule base de données. Pour éviter les conflits, il est conseillé d'utiliser une nomenclature propre à chacun de vos projets, en préfixant le nom de vos tables (ex: wp_xxxx pour un projet Wordpress).

# Respect de la législation en vigueur
Vous êtes tenu de respecter les différentes législations en vigueur. Notamment, et de manière non exhaustive :
- les lois Informatiques et Libertés
- les lois sur la Propriété Intellectuelle
- les lois sur le piratage informatique

# Chartes
Vous êtes tenu de respecter les différentes chartes en vigueur, notamment [celles de l'Université de Rouen](http://communaute-universitaire.univ-rouen.fr/chartes-588303.kjsp), et de RENATER.

# Support technique:
Pour toute demande d'assistance vous pouvez adresser un mail à [support-recherche@univ-rouen.fr](mailto:support-recherche@univ-rouen.fr)
