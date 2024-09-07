# Blog OpenClassrooms

![banner](https://github.com/user-attachments/assets/000bdfe6-a392-4a96-8ea5-7395207a34c7)

Projet de la formation ***Développeur d'application - PHP / Symfony***.

**Créez votre premier blog en PHP** - [Lien de la formation](https://openclassrooms.com/fr/paths/876-developpeur-dapplication-php-symfony)

## Contexte

Ça y est, vous avez sauté le pas ! Le monde du développement web avec PHP est à portée de main et vous avez besoin de visibilité pour pouvoir convaincre vos futurs employeurs/clients en un seul regard. 
Vous êtes développeur PHP, il est donc temps de montrer vos talents au travers d’un blog à vos couleurs.

## Descriptif du besoin 

Le projet est donc de développer votre blog professionnel. Ce site web se décompose en deux grands groupes de pages :
 
*   les pages utiles à tous les visiteurs
*   les pages permettant d’administrer votre blog

Voici la liste des pages qui devront être accessibles depuis votre site web :
 
*   la page d'accueil
*   la page listant l’ensemble des blogs posts
*   la page affichant un blog post
*   la page permettant d’ajouter un blog post
*   la page permettant de modifier un blog post
*   les pages permettant de modifier/supprimer un blog post
*   les pages de connexion/enregistrement des utilisateurs
 
Vous développerez une partie administration qui devra être accessible uniquement aux utilisateurs inscrits et validés.
Les pages d’administration seront donc accessibles sur conditions et vous veillerez à la sécurité de la partie administration.
Commençons par les pages utiles à tous les internautes.
 
Sur la page d’accueil il faudra présenter les informations suivantes :
 
*   Votre nom et votre prénom
*   Une photo et/ou un logo
*   Une phrase d’accroche qui vous ressemble
*   Un menu permettant de naviguer parmi l’ensemble des pages de votre site web
*   un formulaire de contact (à la soumission de ce formulaire, un e-mail avec toutes ces informations vous sera envoyé) avec les champs suivants :
      - **nom/prénom**
      - **e-mail de contact**
      - **message**
*   Un lien vers votre CV au format pdf
*   Et l’ensemble des liens vers les réseaux sociaux où l’on peut vous suivre (Github, LinkedIn, Twitter…)
 
Sur la page listant tous les blogs posts (du plus récent au plus ancien), il faut afficher les informations suivantes pour chaque blog post :
 
*   Le titre
*   La date de dernière modification
*   Le châpo
*   Un lien vers le blog post
 
Sur la page présentant le détail d’un blog post, il faut afficher les informations suivantes :
 
*   Le titre
*   Le chapô
*   Le contenu
*   L’auteur
*   La date de dernière mise à jour
*   Le formulaire permettant d’ajouter un commentaire (soumis pour validation)
*   Les listes des commentaires validés et publiés






## Installation du projet 

*   Cloner le projet

*   Télécharger les différentes librairies :

  *   require -dev :
      - "phpunit/phpunit": "*",
      - "squizlabs/php_codesniffer": "^3.9",
      - "robmorgan/phinx": "^0.16.0",
      - "fakerphp/faker": "^1.23",
      - "phpspec/prophecy-phpunit": "^2.2",
      - "dms/phpunit-arraysubset-asserts": "^0.5.0"
  *   require :
      - "nesbot/carbon": "*",
      - "guzzlehttp/psr7": "^2.6",
      - "http-interop/response-sender": "^1.0",
      - "twig/twig": "^3.9",
      - "php-di/php-di": "^7.0",
      - "ext-pdo": "*",
      - "pagerfanta/pagerfanta": "^4.5",
      - "franzl/whoops-middleware": "^2.0",
      - "intervention/image": "^3.7",
      - "mezzio/mezzio-fastroute": "^3.11",
      - "symfony/mailer": "^7.1",
      - "symfony/twig-bridge": "^7.1",
      - "ramsey/uuid": "^4.7"
      - "vlucas/phpdotenv": "^5.6"

```bash
composer install
```

*   Créer une bdd 'blog' et lancer les migrations et le seeding des tables
```bash
.\vendor\bin\phinx migrate
```
```bash
.\vendor\bin\phinx seed:run
```

*   Lancer le serveur sur un environnement de développement
```bash
$env:ENV="dev"; php -S localhost:8000 -d display_errors=1 -t public/
```

*   Ou sur un environnement de production (mise en place du cache)
```bash
php -S localhost:8000 -d display_errors=1 -t public/
```

*   Pensez à créer un fichier .env sur la base du .env.example fournis, en renseignant les informations de connexion BDD

*   Un compte Admin est créé automatiquement et vous avez la possibilité de créer un compte utilisateur simple
    - username : admin
    - password : admin


## Auteur

**Jonathan Dumont** - *OC-P5-Blog php* - [Joz](https://github.com/JozBLT)
