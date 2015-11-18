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

Le projet tient compte d'une variable d'environnement pour les appels � plusieurs fonctionnalit�s.
Ces fonctionnalit�s sont :
* l'envoi de mail de notification d'erreurs (destinataires diff�rents)
* la configuration de l'acc�s � la base Oracle (BD diff�rentes)

### Comment ?

Il suffit de configurer la variable d'environnement sur la configuration correspondante.

DEV = serveur de test du cridon
PREPROD = serveur de preprod du cridon
PROD = serveur de prod du cridon

Si l'environnement n'est pas pr�cis�, un fallback est pr�vu pour simuler l'environnement de dev (sauf pour l'acc�s � la DB, qui est diff�rent entre local et le serveur de DEV).

Pour ce faire, deux possibilit�s :
* Soit dans le fichier wp-config.php
```
setenv("DEV=DEV");
```
* Soit via la conf serveur, d�finir la variable serveur "ENV" :
```
$_SERVER('ENV');
```