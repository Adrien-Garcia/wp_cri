<?php

/**
 * Description of cridon.gedparser.lib.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */
class CridonGedParser extends CridonCsvParser
{

    /**
     * @var int : index Nom du fichier PDF 
     */
    const INDEX_NOMFICHIER      = 0;

    /**
     * @var int : index N° Question
     */
    const INDEX_NUMQUESTION     = 1;

    /**
     * @var int : index Nombre de pages correspondant à la question 
     */
    const INDEX_NBPAGE          = 2;

    /**
     * @var int : index Nombre de pages du document PDF
     */
    const INDEX_NBPAGEDOC       = 3;

    /**
     * @var int : index Valeur CAB
     */
    const INDEX_VALCAB          = 4;

    /**
     * @var int : index N° CRPCEN de l'étude
     */
    const INDEX_CRPCEN          = 5;

    /**
     * @var int : index Nom du notaire
     */
    const INDEX_NOMNOTAIRE      = 6;

    /**
     * @var int : index Prénom du notaire 
     */
    const INDEX_PRENOMNOTAIRE   = 7;

    /**
     * @var int : index Matière de la question
     */
    const INDEX_MATIERE         = 8;

    /**
     * @var int : index Support de la question
     */
    const INDEX_SUPPORT         = 9;

    /**
     * @var int : index Date d'affectation de la question
     */
    const INDEX_DATEAFFECTION   = 10;

    /**
     * @var int : index Date de réponse
     */
    const INDEX_DATEREPONSE     = 11;

    /**
     * @var int : index Nom du juriste principal
     */
    const INDEX_NOMJURISTE      = 12;

    /**
     * @var int : index Objet de la question
     */
    const INDEX_OBJET           = 13;
}