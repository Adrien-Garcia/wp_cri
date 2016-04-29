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

# Utilisation

## Debug des crons

Pour utiliser XDEBUG en CLI :

* Configurer PhpStorm : [ici](https://confluence.jetbrains.com/display/PhpStorm/Debugging+PHP+CLI+scripts+with+PhpStorm)
* Configuration dans le terminal : [ici](http://code-chronicle.blogspot.fr/2014/07/web-and-cli-debugging-with-phpstorm.html)

Exemple d'export de variable d'environnement ci-dessous :


```
export PHP_IDE_CONFIG="serverName=wp-cridon.username.jetpulp.dev"
export XDEBUG_CONFIG="remote_enable=1 remote_mode=req remote_port=9000 remote_host=10.0.2.1 remote_connect_back=0 idekey=PHPSTORM"
```

# Choix d'implémentation

## Soldes des notaires

Le choix a été fait de conserver le fonctionnement imposé par le fichier de maj des soldes des notaires.
Lorsque le type de support vaut 0, il s'agit du nombre de points initiaux d'une étude.

Il doit donc y avoir autant de lignes avec support 0 que d'étude,
alors qu'il n'y a pas de ligne avec un support autre tant qu'il n'y a pas eu de consommation de points.

# Migrations SQL

Ce Wordpress se base sur des Models custom, ayant chacun leur propre table. Cela permet de faire évoluer le model par étape, sans opération manuelle en BO.
On écrit pour ce faire, des migrations SQL disponibles dans le dossier idoine du plugin Cridon.

Ces migrations peuvent être exécutées en CLI dans le dossier `server` via la commande suivante :
````
php oneshot/executeMigrationsSQL.php
````

L'état des migrations est consultable dans la table `cri_plugin_migrations`.

# Paramétrage des constantes applicatives
```
wp-content/plugins/cridon/app/config/const.inc.php
wp-content/plugins/cridon/app/config/config.php
```

# Fichier de déclaration des fonctions specifiques utilisées en Front 
```
wp-content/plugins/cridon/app/utils/functions.php
```

# Emplacement des Hook de personnalisation
```
wp-content/plugins/cridon/app/config/hook.inc.php
```