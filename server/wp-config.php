<?php

/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'wp_maestro');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'wp');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'wp');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'localhost');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         ' cq,[$o|K&(?8%F#A#(`kc}*(g a${+|m#Qe.KzdMA|`1MzIVKqP1<|-I@-|JZ:e');
define('SECURE_AUTH_KEY',  'zmLxeHs]tY/+E}Bqkq+xLd#Q#z4@ljsT&D5<}mRf]wTQS}sO>5~u]sTj3_f%m+@+');
define('LOGGED_IN_KEY',    'Nk<PT&4(SU HFp)*61|:4V`()4bR!<R5ly65SO-OOXjqago&I8oGe$yD  a3nu>F');
define('NONCE_KEY',        't+^=}.,QF+VT;Q5IQzige/cv|$4XyU+-grsrRY;Y(XZZG,hPK/s}vtXAJI0&sg|n');
define('AUTH_SALT',        '80LU&$^-|x_(IP$D>,Bd1D4M~)?;0PeRJ.s3)$FboV4_!tB6(cxf.]O*4OQzh%yP');
define('SECURE_AUTH_SALT', 'F>Q !LKFN3qN+wFSk+Uh3E6!xp-E<J`}`;]XnWTsnLC_43_[0ffr?/q+M9{!xTQQ');
define('LOGGED_IN_SALT',   'CuA8SOPC-FT#b7CR~>32QiJ_PzV~OUl)/^j*pf/E!k_yqS0TAqA[N-p5zC + 4^-');
define('NONCE_SALT',       'g p*oJH!f2bSNmJZ diEfM7Et^&GU_-s6`m*TBv5#u,S+)%iA9T5:?W+D8nqV~te');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/**
 * Langue de localisation de WordPress, par défaut en Anglais.
 *
 * Modifiez cette valeur pour localiser WordPress. Un fichier MO correspondant
 * au langage choisi doit être installé dans le dossier wp-content/languages.
 * Par exemple, pour mettre en place une traduction française, mettez le fichier
 * fr_FR.mo dans wp-content/languages, et réglez l'option ci-dessous à "fr_FR".
 */
define('WPLANG', 'fr_FR');

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', true); 

/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');
