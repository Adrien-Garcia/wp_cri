wp_cridon
===================

Wordpress version 4.3.6

URLs
-------------

Site du CRIDON Lyon (base de connaissance notariale et interface pour poser des questions au experts CRIDON)

* PROD : http://cridon-lyon.fr/
* PREPROD : http://cridon.preprod.jetpulp.hosting/
* DEV : http://cridon.dev.jetpulp.hosting/

----------

Pré-Requis
-------------

- Apache 2.4 
- MariaDB 10
- PHP 5.6
- OCI8 http://php.net/manual/en/book.oci8.php

> **Note:**
>
> Image docker : `registry.jetpulp.fr:5000/dev/php56-apache-oci8`

----------

<i class="fa fa-cog"></i>Installation - Mise en route
-------------

```
cd docker
bash start.sh
```

Présentation
-------------

Le site du Cridon est un espace dédié aux notaires pour poser des questions aux experts du Cridon.
C'est aussi un site de ressources documentaires, apportées par des publications régulières (Flashs infos, Cahiers du Cridon, Veilles) ainsi que par des contenus proposés par des partenaires (Crid'Online).
L'essentiel des données et fonctionnalités du site est réservé aux notaires authentifiés.

<i class="fa fa-picture"></i>Thème custom
-------------

* Thème responsive
* Compatibilité Navigateur vendu : >= IE 10

----------

<i class="fa fa-exchange"></i>Interfaces
-------------

### ERP

* Interfaces via crons php (`server/cron/*.php`)
* Interrogation de vues SQL et écritures dans des tables temporaires
* SGBD Oracle, interrogé via le connecteur **oci8**

Les crons d'import/export de contenus sont incrémentiels, en cas de non disponibilité de l'ERP, l'export suivant peut envoyer le contenu n'ayant pas été expédié auparavant (cf l'option WP `cronquestionupdate`).
La suppression de contenu se fait par contre par différentiel entre le contenu des vues et le contenu de la DB du site.

### Sinequa

Sinequa est un moteur de recherche intégré sur le site en iFrame.

Le contenu du site est directement indexé par le moteur (exposition la DB sur le serveur, Sinequa étant également installé sur le serveur).

Le site s'occupe simplement de transmettre l'authentification du notaire, ainsi que le niveau d'accès de son étude.

Sinequa est maintenu et géré par la société SWORD.

### Crid'Online

L'espace Crid'Online est accessible aux notaires connectés via la génération d'un lien dynamique. Ce lien est généré par l'appel d'uns script JS externe auquel sont transmis l'authentification du notaire, ainsi que le niveau d'accès de son étude.

La solution Crid'Online est maintenue et gérée par la société Wolters-Kluwer.


<i class="fa fa-cog"></i>Modules tiers installées
-------------

* WP MVC : Micro framework pour développer suivant le standard MVC dans WP.
* Visualizer: Charts and Graphs

----------

<i class="fa fa-cog"></i>Modules développés installées
-------------

* Cridon : Module (MVC) :
Le module suit les principes de `wp_mvc` pour ce qui est du routage.
Ce routage est utilisé pour les interfaces avec les applications mobiles.

L'ORM du plugin étant trop léger (requêtes multiples pour créer des relations), certaines requêtes ont été réécrites via un micro ORM spécifique.
L'existence de ces deux systèmes de requêtage fait qu'il est parfois difficile de trouver la syntaxe exacte pour récupérer du contenu.

L'avantage principal réside dans l'existence de `Model` ayant une table dédiée, facilitant l'interfaçage avec l'ERP.

Le module se charge aussi d'envoyer des emails "transactionnels" via le serveur SMTP du Cridon.


> **Note:**
> liens complémentaires sur les données de l'ERP, ayant permis d'aboutir à la modélisation actuelle :
> https://docs.google.com/spreadsheets/d/1LSwYUCAmQSH2RdSx8SAFoqGHeYYSKRZBQzqwCOyp9xs/edit?usp=sharing
> https://docs.google.com/spreadsheets/d/1U5inqKdEOfNAFp4Wh-amXlUe5NhoQWZD3KpDyYcMbVg/edit?usp=sharing
> https://drive.google.com/file/d/0ByRctBS_ixf5LXlpYmNyUEJzbE0/view?usp=sharing

<i class="fa fa-server"></i>Hébergement
-------------

Le projet est hebergé sur les serveurs du Cridon.

http://wikisi.addonline.local/index.php/Cat%C3%A9gorie:Cridon

<i class="fa fa-help"></i>Informations spécifiques complémentaires
--------------

* Victor ALBERT victor.albert@jetpulp.fr [Dev Referent]
* Renaud AMSELLEM renaud.amsellem@jetpulp.fr [Dev]
* Alexandra PETIT alexandra.petit@jetpulp.fr [Inte]
* Clément HORGUES clement.horgues@jetpulp.fr [Inte/Dev]
* Jordan LOUAPRE jordan.louapre@jetpulp.fr [PP]


# Autres informations relatives aux développements

## Environnements

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
setenv("ENV=DEV");
```
* Soit via la conf serveur, définir la variable serveur "ENV" :
```
$_SERVER('ENV');
```

## Debug des crons

Pour utiliser XDEBUG en CLI :

* Configurer PhpStorm : [ici](https://confluence.jetbrains.com/display/PhpStorm/Debugging+PHP+CLI+scripts+with+PhpStorm)
* Configuration dans le terminal : [ici](http://code-chronicle.blogspot.fr/2014/07/web-and-cli-debugging-with-phpstorm.html)

Exemple d'export de variable d'environnement ci-dessous :


```
export PHP_IDE_CONFIG="serverName=wp-cridon.username.jetpulp.dev"
export XDEBUG_CONFIG="remote_enable=1 remote_mode=req remote_port=9000 remote_host=10.0.2.1 remote_connect_back=0 idekey=PHPSTORM"
```

## Choix d'implémentation

### Soldes des notaires

Le choix a été fait de conserver le fonctionnement imposé par le fichier de maj des soldes des notaires.
Lorsque le type de support vaut 0, il s'agit du nombre de points initiaux d'une étude.

Il doit donc y avoir autant de lignes avec support 0 que d'étude,
alors qu'il n'y a pas de ligne avec un support autre tant qu'il n'y a pas eu de consommation de points.

## Migrations SQL

Ce Wordpress se base sur des Models custom, ayant chacun leur propre table. Cela permet de faire évoluer le model par étape, sans opération manuelle en BO.
On écrit pour ce faire, des migrations SQL disponibles dans le dossier idoine du plugin Cridon.

Ces migrations peuvent être exécutées en CLI dans le dossier `server` via la commande suivante :
````
php oneshot/executeMigrationsSQL.php
````

L'état des migrations est consultable dans la table `cri_plugin_migrations`.

Le suivi des numéros de migration (pour ne pas avoir deux fois le même numéro sur deux branches différentes) est compilé sur gitlab : 
````
http://git.jetpulp.hosting/php/wp_cridon/issues/1
````

## Paramétrage des constantes applicatives
```
wp-content/plugins/cridon/app/config/const.inc.php
wp-content/plugins/cridon/app/config/config.php
```

## Fichier de déclaration des fonctions specifiques utilisées en Front
```
wp-content/plugins/cridon/app/utils/functions.php
```

## Emplacement des Hook de personnalisation
```
wp-content/plugins/cridon/app/config/hook.inc.php
```
