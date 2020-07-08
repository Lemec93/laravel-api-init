# laravel-api-init

INSTALLATION TRANCH BACK-END

1- Cloner le projet en local
`git clone https://gitlab.com/afrika-lawyer/paie-back.git`

2- Installer les paquets composer
`composer install`

3- Configurer la connexion à la base de données
    3.1- Copié le fichier .env.example et rénommé en .env
    3.2- Mettre les informations sur la base de données
    
4- Mettre la base de données à jour
`php artisan migrate:fresh`

5- Ajouter les enregistrements
`php artisan db:seed`

6- Installer passport ci necessaire
`php artisan passport:install`

7- Generer une cle pour passport
`php artisan key:generate`

8- Publication des docs du vendor
`php artisan vendor:publish`
