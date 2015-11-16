wp_cridon
----------

# Installation

Connect to the virtual machine
```
vagrant ssh
```

Gulp installation :
```
cd /var/www/wp_maestro/server/gulp
ln -s /usr/lib/node_modules
```

Gulp compilation :
```
gulp
```

# Configuration

## Environnement

### Pour quoi ?

Le projet tient compte d'une variable d'environnement pour les appels à plusieurs fonctionnalités.
Ces fonctionnalités sont :
* l'envoi de mail de notification d'erreurs (destinataires différents)
* la configuration de l'accès à la base Oracle (BD différentes)

### Comment ?

Il suffit de configurer la variable d'environnement sur la configuration correspondante.

DEV = serveur de test du cridon
PREPROD = serveur de preprod du cridon
PROD = serveur de prod du cridon

Si l'environnement n'est pas précisé, un fallback est prévu pour simuler l'environnement de dev (sauf pour l'accès à la DB, qui est différent entre local et le serveur de DEV).

Pour ce faire, deux possibilités :
* Soit dans le fichier wp-config.php
```
setenv("DEV=DEV");
```
* Soit via la conf serveur, définir la variable serveur "ENV" :
```
$_SERVER('ENV');
```