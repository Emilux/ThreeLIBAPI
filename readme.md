# API Projet ThreeLIB

## Qu'est ce que ThreeLIB ?

ThreeLIB est une application web permettant le stockage de modèles 3D en ligne.
L'api de ThreeLIB permet d'avoir accès aux liens personnels des modèles stockés sur ThreeLIB
depuis une requête HTTP et permet donc différente utilisation.

## Liens utiles
Lien d'accès à l'api :
https://api.emilien-fuchs.com/api

## Installation :

Installation des dépendences :

```
composer install
```
Génération de la base de données :

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```


## Configuration :
Il est nécessaire de créer une private et public key qui permettra de signer le JWT Token.

Exemple : 

Créer un dossier jwt dans dans le dossier config 

Générer la private key :
```
openssl genrsa -out config/jwt/private.pem -aes256 4096
```
Générer la public key
```
openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```


## .env de base :
```
APP_ENV=dev
APP_SECRET=

DATABASE_URL="mysql://root:@127.0.0.1:3306/threelibapi"

CORS_ALLOW_ORIGIN='^https?://(localhost|127\.0\.0\.1)(:[0-9]+)?$'

JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE={YOUR_PASSPHRASE}
```

## Utilisation de l'api

### Les endpoints :
/models/:id **[GET][PUT][DELETE]**

/models/ **[GET][POST]**

/users/:id **[GET][PUT][DELETE]**

/users/ **[GET]**

/login **[POST]**

/token/refresh **[POST]**

### Authentication :

Afin d'avoir l'accès à ses modèles depuis le endpoint **__/models/:id__** ou à ses informations personnels depuis le endpoint **__/users/:id__** 
il faut être connecté et donc posséder son JWT Token pour cela il faut envoyé ses informations de connexion
en requête POST à l'endpoint **__/login__**
au format json : 
```json
{
    "username":"yourusername",
    "password":"yourpassword"
}
```
Le serveur de l'api renverra alors un cookie contenant le jwt token et le refresh_token.
Ce cookie sera réutilisé pour avoir accès au contenu des différents endpoint (par exemple : ses modeles).

