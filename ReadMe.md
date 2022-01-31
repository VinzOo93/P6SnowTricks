installation
Se rendre dans le dossier à l'intérieur du repertoire de votre server local :

cd /wamp64/www lancer la commande : git clone https://github.com/VinzOo93/P6SnowTricks.git

pour installer les dépendances de symfony se rendre dans : cd P6SnowTricks composer install

Pour créer la base de donnée, vous devez d'abord configurer correctement le fichier .env, puis exécuter : et modifier cette variable avec le user, mot de passe et non de base de donnée MySQL

.env
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

info voir doc lien ci dessous https://symfony.com/doc/4.4/configuration.html
Pour créer la base de données, vous devez d'abord configurer correctement le fichier .env ou .env.local, puis exécuter :

php bin/console doctrine:database:create

et ensuite : php bin/console doctrine:schema:update --force

Lancer les fixtures : 

php bin/console doctrine:fixtures:load 

Import de la base donnée

Utilisation
lien acceder à l'index :

http://localhost/P6SnowTricks/home/

- Vous pouvez acceder aux détails de chaque trick en cliquant sur le nom dans la card.

- Vous pouvez inscrire ert vous logger 

- Une fois logger vous pouvez laisser une commentaire pour chaque trick et utiliser les Crud au besoin 

Bonne visite