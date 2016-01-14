<?php

/**
 * Description of cridon.customwalker.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */
class CriCustomWalker extends Walker_Nav_Menu
{
    /**
     * @var int : index des parents
     */
    protected $offset = 0;

    /**
     * @var string : lien page de destination associée au bloc d'image
     */
    protected $rechercherAssocPagelink;

    /**
     * @var string : titre associé au bloc d'image
     */
    protected $rechercherBlocTitle;

    /**
     * @var string
     */
    protected $rechercherBlocSubTitle;

    /**
     * @var string : lien page de destination associée au bloc d'image
     */
    protected $accederAssocPagelink;

    /**
     * @var string : titre associé au bloc d'image
     */
    protected $accederBlocTitle;

    /**
     * @var string : sous titre associé au bloc d'image
     */
    protected $accederBlocSubTitle;

    /**
     * @var string : lien page de destination associée au bloc d'image
     */
    protected $consulterAssocPagelink;

    /**
     * @var string : titre associé au bloc d'image
     */
    protected $consulterBlocTitle;

    /**
     * @var string : sous titre associé au bloc d'image
     */
    protected $consulterBlocSubTitle;

    public function __construct()
    {
        // cablage lien page de destination associée au bloc d'image
        $this->rechercherAssocPagelink = '/rechercher-dans-les-bases-de-connaissances/';
        $this->accederAssocPagelink    = mvc_public_url(array('controller' => 'veilles', 'action' => 'index'));
        $this->consulterAssocPagelink  = '';

        // cablage titre et sous titre de chaque bloc
        $this->rechercherBlocTitle    = __('Rechercher');
        $this->rechercherBlocSubTitle = __('dans les bases de connaissances');

        $this->accederBlocTitle    = __('Accéder');
        $this->accederBlocSubTitle = __('à ma veille juridique');

        $this->consulterBlocTitle    = __('Consulter');
        $this->consulterBlocSubTitle = __('un expert juridique');
    }

    /**
     * Surcharge Walker_Nav_Menu::start_el
     *
     * @param string $output
     * @param object $item
     * @param int    $depth
     */
    public function start_el( &$output, $item, $depth )
    {
        // indexation des menus parents
        if ($depth == 0) {
            $this->offset++;
        }
        parent::start_el($output, $item, $depth);
    }

    /**
     * Surcharge Walker_Nav_Menu::end_lvl
     *
     * @param string $output
     * @param int    $depth
     * @param array  $args
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        if ($depth == 0) {
            switch ($this->offset) {
                case 1: // Bloc Acceder
                    $output .= '<li>
                                    <div class="block rechercher js-home-block-link">
                                        <div class="content">
                                            <div class="h2">
                                                ' . $this->rechercherBlocTitle . '
                                                <span>' . $this->rechercherBlocSubTitle . '</span>
                                            </div>
                                            <a href="' . $this->rechercherAssocPagelink . '">+</a>
                                        </div>
                                    </div>
                                </li>';
                    break;
                case 2: // Bloc Commander
                    $output .= '<li>
                                    <div class="block acceder js-home-block-link">
                                        <div class="content">
                                            <div class="h2">
                                                ' . $this->accederBlocTitle . '
                                                <span>' . $this->accederBlocSubTitle . '</span>
                                            </div>
                                            <a href="' . $this->accederAssocPagelink . '">+</a>
                                        </div>
                                    </div>
                                </li>';
                    break;
                case 3: // Bloc Connaitre
                    $output .= '<li>
                                    <div class="block consulter layer-posez-question_open js-question-open" >
                                        <div class="content">
                                            <div class="h2">
                                                ' . $this->consulterBlocTitle . '
                                                <span>' . $this->consulterBlocSubTitle . '</span>
                                            </div>
                                            <a href="' . $this->consulterAssocPagelink . '">+</a>
                                        </div>
                                    </div>
                                </li>';
                    break;
            }
        }
        $output .= "$indent</ul>\n";
    }
}