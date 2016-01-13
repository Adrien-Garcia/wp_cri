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

Si l'environnement n'est pas précisé, un fallback est pr�vu pour simuler l'environnement de dev (sauf pour l'accès à la DB, qui est différent entre local et le serveur de DEV).

Pour ce faire, deux possibilités :
* Soit dans le fichier wp-config.php
```
setenv("DEV=DEV");
```
* Soit via la conf serveur, définir la variable serveur "ENV" :
```
$_SERVER('ENV');
```

# Choix d'implémentation

## Soldes des notaires

Le choix a été fait de conserver le fonctionnement imposé par le fichier de maj des soldes des notaires.
Lorsque le type de support vaut 0, il s'agit du nombre de points initiaux d'une étude.

Il doit donc y avoir autant de lignes avec support 0 que d'étude,
alors qu'il n'y a pas de ligne avec un support autre tant qu'il n'y a pas eu de consommation de points.