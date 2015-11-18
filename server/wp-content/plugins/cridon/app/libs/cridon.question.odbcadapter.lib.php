<?php

/**
 * Description of cridon.question.odbcadapter.lib.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */

require_once 'cridon.odbcadapter.lib.php';

class CridonQuestionODBCAdapter extends CridonODBC
{

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
     * @var array
     */
    public $erpQuestList = array();

    /**
     * @var array
     */
    public $erpQuestData = array();

    /**
     * @var mixed
     */
    protected static $criquestodbcadapter;

    protected function __construct()
    {
        parent::__construct();
    }

    /**
     * Get instance
     *
     * @return CridonODBCAdapter|mixed
     */
    public static function getInstance()
    {
        if (!isset(self::$criquestodbcadapter))
        {
            self::$criquestodbcadapter = new self;
        }

        return self::$criquestodbcadapter;
    }

    /**
     * Prepare ODBC Data
     *
     * @return $this
     */
    public function prepareODBCData()
    {
        while ($data = odbc_fetch_array($this->results)) {

            if (isset( $data[self::QUEST_SREBPC] ) && intval($data[self::QUEST_SREBPC]) > 0) { // valid client_number
                // the only unique key available is the "client_number + num question"
                $uniqueKey = intval($data[self::QUEST_SREBPC]) . $data[self::QUEST_SRENUM];
                array_push($this->erpQuestList, $uniqueKey);

                // notaire data filter
                $this->erpQuestData[$uniqueKey] = $data;
            }
        }

        return $this;
    }

}