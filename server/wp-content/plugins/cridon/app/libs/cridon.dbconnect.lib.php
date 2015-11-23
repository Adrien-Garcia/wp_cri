<?php
/**
 * Interface describing how to retrieve data from a DB Connection
 * @package wp_cridon
 * @author JETPULP
 * @contributor Victor ALBERT
 */

interface DBConnect
{

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
     * @var string : anomalie (0 = Non | 1 = Oui)
     */
    const QUEST_ZANOAMITEL       = 'ZANOAMITEL_0';

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
    const QUEST_YVALSRE         = 'YVALSRE_0';

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
     * Get result
     *
     * @param string $sql query
     * @return resource
     */
    function getResults($sql);

    /**
     * fetch Data
     *
     * @return array|false
     */
    function fetchData();

    /**
     * Prepare count Data that can be retrieved
     *
     * @return $this
     */
    function countData();

    /**
     * Close the connection
     */
    public function closeConnection();
}