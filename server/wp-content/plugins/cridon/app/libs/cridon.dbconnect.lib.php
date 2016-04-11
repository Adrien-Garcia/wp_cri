<?php
/**
 * Interface describing how to retrieve data from a DB Connection
 * @package wp_cridon
 * @author JETPULP
 * @contributor Victor ALBERT
 */

interface DBConnect
{

    /****************** Table ZEXPNOTV Structure *******************/
    /**
     * @var string : category field in Output data
     */
    const NOTAIRE_CATEG      = 'BCGCOD_0';

    /**
     * @var string : client_number field in Output data
     */
    const NOTAIRE_NUMCLIENT  = 'BPCNUM_0';

    /**
     * @var string : crpcen field in Output data
     */
    const NOTAIRE_CRPCEN     = 'YCRPCEN_0';

    /**
     * @var string : Web Code field in Output data
     */
    const NOTAIRE_CODEWEB    = 'YCODWEB_0';

    /**
     * @var string : web_password field in Output data
     */
    const NOTAIRE_PWDWEB     = 'YMDPWEB_0';

    /**
     * @var string : tel_password field in Output data
     */
    const NOTAIRE_PWDTEL     = 'ZMDPTEL_0';

    /**
     * @var string : id_sigle field in Output data
     */
    const NOTAIRE_SIGLE      = 'BPRLOG_0';

    /**
     * @var string : office_name field in Output data
     */
    const NOTAIRE_OFFICENAME = 'BPRNAM_0';

    /**
     * @var string : status field in Output data
     */
    const NOTAIRE_STATUS  = 'YACTIF_0';

    /**
     * @var string : code_interlocuteur field in Output data
     */
    const NOTAIRE_INTERCODE  = 'CCNCRM_0';

    /**
     * @var string : id_civilite field in Output data
     */
    const NOTAIRE_CIVILIT    = 'CNTTTL_0';

    /**
     * @var string : first_name field in Output data
     */
    const NOTAIRE_FNAME      = 'CNTFNA_0';

    /**
     * @var string : last_name field in Output data
     */
    const NOTAIRE_LNAME      = 'CNTLNA_0';

    /**
     * @var string : tel field in Output data
     */
    const NOTAIRE_TEL      = 'TELNOT_0';

    /**
     * @var string : last_name field in Output data
     */
    const NOTAIRE_FAX        = 'FAXNOT_0';

    /**
     * @var string : mobile field in Output data
     */
    const NOTAIRE_PORTABLE   = 'CNTMOB_0';

    /**
     * @var string : email_adress field in Output data
     */
    const NOTAIRE_EMAIL      = 'WEBNOT_0';

    /**
     * @var string : id_fonction field in Output data
     */
    const NOTAIRE_FONC       = 'CNTFNC_0';

    /**
     * @var string : adress_1 field in Output data
     */
    const NOTAIRE_ADRESS1    = 'BPAADDLIG1_0';

    /**
     * @var string : adress_2 field in Output data
     */
    const NOTAIRE_ADRESS2    = 'BPAADDLIG2_0';

    /**
     * @var string : adress_3 field in Output data
     */
    const NOTAIRE_ADRESS3    = 'BPAADDLIG3_0';

    /**
     * @var string : cp field in Output data
     */
    const NOTAIRE_CP         = 'POSCOD_0';

    /**
     * @var string : city field in Output data
     */
    const NOTAIRE_CITY       = 'CTY_0';

    /**
     * @var string : office_email_adress_1 field in Output data
     */
    const NOTAIRE_MAIL1      = 'WEB_0';

    /**
     * @var string : office_email_adress_2 field in Output data
     */
    const NOTAIRE_MAIL2      = 'ZMAIL2_0';

    /**
     * @var string : office_email_adress_3 field in Output data
     */
    const NOTAIRE_MAIL3      = 'ZMAIL3_0';

    /**
     * @var string : date_modified field in Output data
     */
    const NOTAIRE_DATEMODIF  = 'UPDDAT_0';

    /**
     * @var string : office tel field in Output data
     */
    const NOTAIRE_OFFICETEL  = 'TEL_0';

    /**
     * @var string : office fax field in Output data
     */
    const NOTAIRE_OFFICEFAX  = 'FAX_0';

    /**
     * @var string : notary id on Website
     */
    const NOTAIRE_YIDNOT = 'YIDNOT_0';
    /****************** /Table ZEXPNOTV Structure *******************/

    /**
     * @var string : num question
     */
    const QUEST_SRENUM      = 'SRENUM_0';

    /**
     * @var string : client_number
     */
    const QUEST_SREBPC  = 'SREBPC_0';

    /**
     * @var string : interlocutor
     */
    const QUEST_SRECCN     = 'SRECCN_0';

    /**
     * @var string : Support id
     */
    const QUEST_YCODESUP    = 'YCODESUP_0';

    /**
     * @var string : primary matiere
     */
    const QUEST_YMATIERE     = 'YMATIERE_0';

    /**
     * @var string : secondary matiere
     */
    const QUEST_YMAT1     = 'YMAT1_0';

    /**
     * @var string : secondary matiere
     */
    const QUEST_YMAT2     = 'YMAT2_0';

    /**
     * @var string : secondary matiere
     */
    const QUEST_YMAT3     = 'YMAT3_0';

    /**
     * @var string : secondary matiere
     */
    const QUEST_YMAT4     = 'YMAT4_0';

    /**
     * @var string : secondary matiere
     */
    const QUEST_YMAT5     = 'YMAT5_0';

    /**
     * @var string : primary competence
     */
    const QUEST_ZCOMPETENC      = 'ZCOMPETENC_0';

    /**
     * @var string : secondary competence
     */
    const QUEST_ZCOMP1 = 'ZCOMP1_0';

    /**
     * @var string : secondary competence
     */
    const QUEST_ZCOMP2 = 'ZCOMP2_0';

    /**
     * @var string : secondary competence
     */
    const QUEST_ZCOMP3 = 'ZCOMP3_0';

    /**
     * @var string : secondary competence
     */
    const QUEST_ZCOMP4 = 'ZCOMP4_0';

    /**
     * @var string : secondary competence
     */
    const QUEST_ZCOMP5 = 'ZCOMP5_0';

    /**
     * @var string : status
     */
    const QUEST_STATUS  = 'YACTIF_0';

    /**
     * @var string : object
     */
    const QUEST_YRESUME  = 'YRESUME_0';

    /**
     * @var string : affectation
     */
    const QUEST_YSREASS    = 'YSREASS_0';

    /**
     * @var string : juriste principal
     */
    const QUEST_SREDET      = 'SREDET_0';

    /**
     * @var string :
     */
    const QUEST_YUSR1      = 'YUSR1_0';

    /**
     * @var string :
     */
    const QUEST_YUSR2      = 'YUSR2_0';

    /**
     * @var string :
     */
    const QUEST_YUSR3      = 'YUSR3_0';

    /**
     * @var string :
     */
    const QUEST_YUSR4      = 'YUSR4_0';

    /**
     * @var string :
     */
    const QUEST_YUSR5      = 'YUSR5_0';

    /**
     * @var string : date affectation
     */
    const QUEST_SREDATASS      = 'SREDATASS_0';

    /**
     * @var string : reponse souhatee
     */
    const QUEST_YRESSOUH        = 'YRESSOUH_0';

    /**
     * @var string : reponse reelle
     */
    const QUEST_SRERESDAT   = 'SRERESDAT_0';

    /**
     * @var string : secretaire
     */
    const QUEST_YUSER      = 'YUSER_0';

    /**
     * @var string : anomalie (1 = Non | 2 = Oui)
     */
    const QUEST_ZANOAMITEL       = 'ZANOAMITEL_0';

    /**
     * @var string : anomalie (1 = Non | 2 = Oui)
     */
    const QUEST_NOFAC_TEL       = 'ZTELNOFAC_0';

    /**
     * @var string : date de modification
     */
    const QUEST_UPDDAT    = 'UPDDAT_0';

    /**
     * @var string : heure de modification
     */
    const QUEST_ZUPDHOU    = 'ZUPDHOU_0';

    /**
     * @var string : identifiant interaction
     */
    const QUEST_ZIDQUEST    = 'ZIDQUEST_0';

    /**
     * @var string : valeur en point
     */
    const QUEST_YVALSRE     = 'YVALSRE_0';

    /****************** Export Table Temp Structure *******************/
    /**
     * @var string : id question - Site
     */
    const ZQUEST_ZIDQUEST_0  = 'ZIDQUEST_0';

    /**
     * @var string : statut de l'enregistrement (initialisé à 0 par le site puis maj par ERP )
     */
    const ZQUEST_ZTRAITEE_0  = 'ZTRAITEE_0';

    /**
     * @var string : N° question - X3
     */
    const ZQUEST_SRENUM_0  = 'SRENUM_0';

    /**
     * @var string : Numéro de client - Site
     */
    const ZQUEST_SREBPC_0  = 'SREBPC_0';

    /**
     * @var string : Interlocuteur (Notaire) - Site
     */
    const ZQUEST_SRECCN_0  = 'SRECCN_0';

    /**
     * @var string : Support de la question  - Site
     */
    const ZQUEST_YCODESUP_0  = 'YCODESUP_0';

    /**
     * @var string : Matière - SIte
     */
    const ZQUEST_YMATIERE_0  = 'YMATIERE_0';

    /**
     * @var string : Matière 1  - Site
     */
    const ZQUEST_YMAT_0  = 'YMAT_0';

    /**
     * @var string : Matière 2  - SIte
     */
    const ZQUEST_YMAT_1  = 'YMAT_1';

    /**
     * @var string : Matière 3  - SIte
     */
    const ZQUEST_YMAT_2  = 'YMAT_2';

    /**
     * @var string : Matière 4  - SIte
     */
    const ZQUEST_YMAT_3  = 'YMAT_3';

    /**
     * @var string : Matière 5  - SIte
     */
    const ZQUEST_YMAT_4  = 'YMAT_4';

    /**
     * @var string : Compétence (sous matière) - Site
     */
    const ZQUEST_ZCOMPETENC_0  = 'ZCOMPETENC_0';

    /**
     * @var string : Compétence 1 (sous matière) - Site
     */
    const ZQUEST_ZCOMP_0  = 'ZCOMP_0';

    /**
     * @var string : Compétence 2 (sous matière) - Site
     */
    const ZQUEST_ZCOMP_1  = 'ZCOMP_1';

    /**
     * @var string : Compétence 3 (sous matière) - Site
     */
    const ZQUEST_ZCOMP_2  = 'ZCOMP_2';

    /**
     * @var string : Compétence 4 (sous matière) - Site
     */
    const ZQUEST_ZCOMP_3  = 'ZCOMP_3';

    /**
     * @var string : Compétence 5 (sous matière) - Site
     */
    const ZQUEST_ZCOMP_4  = 'ZCOMP_4';

    /**
     * @var string : Objet de la question - Site
     */
    const ZQUEST_YRESUME_0  = 'YRESUME_0';

    /**
     * @var string : Affectation (Status de la question) - Site 
     */
    const ZQUEST_YSREASS_0  = 'YSREASS_0';

    /**
     * @var string : Flag erreur - 0 : pas d’erreur | 1 : erreur - X3
     */
    const ZQUEST_ZERR_0  = 'ZERR_0';

    /**
     * @var string : Message de l’erreur (vide par défaut) - X3
     */
    const ZQUEST_ZMESERR_0  = 'ZMESSERR_0';

    /**
     * @var string : Date de création de l’enregistrement (le moment où la Q est posée) - Site
     */
    const ZQUEST_CREDAT_0  = 'CREDAT_0';

    /**
     * @var string : Lien fichier complémentaire (contrainte EPR Oracle)
     */
    const ZQUEST_ZLIENS_0 = 'ZLIENS_0';

    /**
     * @var string : Lien fichier complémentaire (contrainte EPR Oracle)
     */
    const ZQUEST_ZLIENS_1 = 'ZLIENS_1';

    /**
     * @var string : Lien fichier complémentaire (contrainte EPR Oracle)
     */
    const ZQUEST_ZLIENS_2 = 'ZLIENS_2';

    /**
     * @var string : Lien fichier complémentaire (contrainte EPR Oracle)
     */
    const ZQUEST_ZLIENS_3 = 'ZLIENS_3';

    /**
     * @var string : Lien fichier complémentaire (contrainte EPR Oracle)
     */
    const ZQUEST_ZLIENS_4 = 'ZLIENS_4';

    /**
     * @var string : Contenu de la question
     */
    const ZQUEST_ZTXTQUEST_0 = 'ZTXTQUEST_0';


    /**
     * Get instance
     *
     * @return mixed Connector, such as CridonODBCAdapter
     */
    static function getInstance();

    /**
     * Connexion
     *
     * @return resource
     */
    function connection();

    /**
     * execute $sql query
     *
     * @param string $sql query
     * @return resource
     */
    function execute($sql);

    /**
     * fetch Data
     *
     * @return array|false
     */
    function fetchData();

    /**
     * Close the connection
     */
    public function closeConnection();
}
