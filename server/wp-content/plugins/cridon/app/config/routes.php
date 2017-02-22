<?php
/**
 * Route config for cridon
 * @package wp_cridon
 * @subpackage routes
 * @author Etech
 */

// Téléchargement des documents publics, toujours en dernier dans les routes pour éviter les problèmes des "/" dans les données cryptées
MvcRouter::public_connect('telechargement/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'documents', 'action' => 'publicDownload'));
MvcRouter::public_connect('documents/download/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'documents', 'action' => 'download'));

// rest
MvcRouter::public_connect('rest/login', array( 'controller' =>'logins','action' => 'login'));
MvcRouter::public_connect('rest/askquestion', array( 'controller' =>'questions','action' => 'add_json'));

/**
 * Ne pas supprimer l'ancienne regle de routage suivante
 * Utile pour les URL deja indéxées qui seront gerer via une redirection 301
 *
 * La suppression de ces lignes affiche une page 404 (ou autre chose que le format attendu) donc impossible d'effectuer une redirection 301 (test effectué en local)
 *
 */
// Debut bloc ancienne url
// mes questions
MvcRouter::public_connect('notaires/{:id:[\d]+}/questions', array('controller' => 'notaires', 'action' => 'questions'));
// mon profil
MvcRouter::public_connect('notaires/{:id:[\d]+}/profil', array('controller' => 'notaires', 'action' => 'profil'));
// regles de facturation
MvcRouter::public_connect('notaires/{:id:[\d]+}/facturation', array('controller' => 'notaires', 'action' => 'facturation'));

// mon dashboard
MvcRouter::public_connect('notaires/{:id:[\d]+}/contentdashboard', array('controller' => 'notaires', 'action' => 'contentdashboard'));
// mes questions
MvcRouter::public_connect('notaires/{:id:[\d]+}/contentquestions', array('controller' => 'notaires', 'action' => 'contentquestions'));
// mon profil
MvcRouter::public_connect('notaires/{:id:[\d]+}/contentprofil', array('controller' => 'notaires', 'action' => 'contentprofil'));
// regles de facturation
MvcRouter::public_connect('notaires/{:id:[\d]+}/contentfacturation', array('controller' => 'notaires', 'action' => 'contentfacturation'));
// regles show
MvcRouter::public_connect('notaires/{:id:[\d]+}', array('controller' => 'notaires', 'action' => 'show'));
// Fin bloc ancienne url

// nouvelle regle de routage notaire
MvcRouter::public_connect('notaires/questions', array('controller' => 'notaires', 'action' => 'questions'));
MvcRouter::public_connect('notaires/profil', array('controller' => 'notaires', 'action' => 'profil'));
MvcRouter::public_connect('notaires/facturation', array('controller' => 'notaires', 'action' => 'facturation'));
MvcRouter::public_connect('notaires/collaborateur', array('controller' => 'notaires', 'action' => 'collaborateur'));
MvcRouter::public_connect('notaires/cridonline', array('controller' => 'notaires', 'action' => 'cridonline'));
MvcRouter::public_connect('notaires/mesfactures', array('controller' => 'notaires', 'action' => 'mesfactures'));
MvcRouter::public_connect('notaires/mesreleves', array('controller' => 'notaires', 'action' => 'mesreleves'));
MvcRouter::public_connect('notaires/contentdashboard', array('controller' => 'notaires', 'action' => 'contentdashboard'));
MvcRouter::public_connect('notaires/contentquestions', array('controller' => 'notaires', 'action' => 'contentquestions'));
MvcRouter::public_connect('notaires/contentprofil', array('controller' => 'notaires', 'action' => 'contentprofil'));
MvcRouter::public_connect('notaires/contentfacturation', array('controller' => 'notaires', 'action' => 'contentfacturation'));
MvcRouter::public_connect('notaires/contentcollaborateur', array('controller' => 'notaires', 'action' => 'contentcollaborateur'));
MvcRouter::public_connect('notaires/contentmesfactures', array('controller' => 'notaires', 'action' => 'contentmesfactures'));
MvcRouter::public_connect('notaires/contentmesreleves', array('controller' => 'notaires', 'action' => 'contentmesreleves'));
MvcRouter::public_connect('notaires/contentprofil/gestion', array('controller' => 'notaires', 'action' => 'gestionetude'));
MvcRouter::public_connect('notaires/contentprofil/motdepasse', array('controller' => 'notaires', 'action' => 'gestionpassword'));
MvcRouter::public_connect('notaires/contentcollaborateur/gestion', array('controller' => 'notaires', 'action' => 'gestioncollaborateur'));
MvcRouter::public_connect('notaires/contentcridonline', array('controller' => 'notaires', 'action' => 'contentcridonline'));
MvcRouter::public_connect('notaires/contentcridonlinepromo', array('controller' => 'notaires', 'action' => 'contentcridonlinepromo'));//promo
MvcRouter::public_connect('notaires/contentcridonlineetape2', array('controller' => 'notaires', 'action' => 'contentcridonlineetape2'));
MvcRouter::public_connect('notaires/contentcridonlineetape2promo', array('controller' => 'notaires', 'action' => 'contentcridonlineetape2promo'));
MvcRouter::public_connect('notaires/souscriptionveille', array('controller' => 'notaires', 'action' => 'ajaxveillesubscription'));
MvcRouter::public_connect('notaires/souscriptionveillepromo', array('controller' => 'notaires', 'action' => 'ajaxveillesubscriptionpromo'));//promo
MvcRouter::public_connect('notaires/souscriptionnewsletter', array('controller' => 'notaires', 'action' => 'ajaxnewslettersubscription'));
MvcRouter::public_connect('notaires', array('controller' => 'notaires', 'action' => 'show'));

//RSS feed
MvcRouter::public_connect('medias/rss/actualites.xml', array( 'controller' =>'veilles','action' => 'feed'));
MvcRouter::public_connect('medias/rss/{:id:[\d]+}', array( 'controller' =>'veilles','action' => 'feedFilter'));

// archives routes
MvcRouter::public_connect('flashes', array('controller' => 'flashes', 'action' => 'index'));
MvcRouter::public_connect('formations', array('controller' => 'formations', 'action' => 'index'));
MvcRouter::public_connect('veilles', array('controller' => 'veilles', 'action' => 'index'));
MvcRouter::public_connect('cahier_cridons', array('controller' => 'cahier_cridons', 'action' => 'index'));
MvcRouter::public_connect('vie_cridons', array('controller' => 'vie_cridons', 'action' => 'index'));
MvcRouter::public_connect('matieres', array('controller' => 'matieres', 'action' => 'index'));

// wpmvc virtual_name routes
MvcRouter::public_connect('flashes/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'flashes', 'action' => 'show'));
MvcRouter::public_connect('calendrier-des-formations/{:id:[0-9-]+}', array('controller' => 'formations', 'action' => 'calendar'));
MvcRouter::public_connect('calendrier-des-formations', array('controller' => 'formations', 'action' => 'calendar'));
MvcRouter::public_connect('formations/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'formations', 'action' => 'show'));
MvcRouter::public_connect('formations/catalogue', array('controller' => 'formations', 'action' => 'catalog'));
MvcRouter::public_connect('formations/prochain-catalogue', array('controller' => 'formations', 'action' => 'catalognextyear'));
MvcRouter::public_connect('formations/publishnextyearcatalog', array('controller' => 'formations', 'action' => 'publishnextyearcatalog'));
MvcRouter::public_connect('veilles/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'veilles', 'action' => 'show'));
MvcRouter::public_connect('cahier_cridons/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'cahier_cridons', 'action' => 'show'));
MvcRouter::public_connect('vie_cridons/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'vie_cridons', 'action' => 'show'));
MvcRouter::public_connect('matieres/{:id:[a-zA-Z0-9=+~_-]+}', array('controller' => 'matieres', 'action' => 'show'));

// formations passees
MvcRouter::public_connect('formations-passees', array('controller' => 'formations', 'action' => 'past'));

//Ajax admin
MvcRouter::admin_ajax_connect(array('controller' => 'admin_documents', 'action' => 'search'));

