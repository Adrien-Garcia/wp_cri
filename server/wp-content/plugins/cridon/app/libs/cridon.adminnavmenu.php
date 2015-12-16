<?php

/**
 * Description of cridon.adminnavmenu.php
 *
 * @package wp_cridon
 * @author eTech
 * @contributor Joelio
 */
class CriAdminNavMenu
{

    /**
     * List of matiere
     *
     * @var array
     */
    protected $listMat = array();
    /**
     * Init action
     *
     * @throws Exception
     */
    public static function init() {
        $class = __CLASS__;
        new $class;
    }

    public function __construct() {
        // Abort if not on the nav-menus.php admin UI page - avoid adding elsewhere
        global $pagenow;
        if ( 'nav-menus.php' !== $pagenow )
            return;

        $this->listMat = $this->getListMat();

        $this->add_veille_meta_box();
        $this->add_archive_meta_box();
        $this->add_matiere_meta_box();
    }

    /**
     * Get list mat
     *
     * @return mixed
     * @throws Exception
     */
    protected function getListMat()
    {
        $matieres = mvc_model('Matiere')->find(array(
            'selects'    => array(
                'DISTINCT Matiere.id',
                'Matiere.label'
            ),
            'joins'     => array(
                'Veille' => array(
                    'table' => 'cri_veille',
                    'alias' => 'Veille',
                    'on' => 'Veille.id_matiere = Matiere.id'
                )
            ),
            'order' => 'Matiere.label'
        ));
        foreach($matieres as $matiere) {
            $matiere->veilles = array();
            $veilles = mvc_model('Veille')->find_by_id_matiere($matiere->id);
            if (count($veilles) > 0) {
                $matiere->veilles = $veilles;
            }
        }

        return $matieres;
    }

    /**
     * Adds the meta box container
     */
    public function add_matiere_meta_box(){
        add_meta_box(
            'matiere_meta_box'
            ,__( 'Matieres' )
            ,array( $this, 'render_matiere_box_content' )
            ,'nav-menus' // important !!!
            ,'side' // important, only side seems to work!!!
            ,'high'
        );
    }

    /**
     * Adds the meta box container
     */
    public function add_veille_meta_box(){
        add_meta_box(
            'veille_meta_box'
            ,__( 'Veilles Juridiques' )
            ,array( $this, 'render_veille_box_content' )
            ,'nav-menus' // important !!!
            ,'side' // important, only side seems to work!!!
            ,'high'
        );
    }

    /**
     * Adds the meta box container
     */
    public function add_archive_meta_box(){
        add_meta_box(
            'archive_meta_box'
            ,__( 'Archives Modèle Cridon' )
            ,array( $this, 'render_archive_box_content' )
            ,'nav-menus' // important !!!
            ,'side' // important, only side seems to work!!!
            ,'high'
        );
    }

    /**
     * Render Meta Box content
     */
    public function render_matiere_box_content() {
        // prepare vars
        $vars = array(
            'matieres' => $this->listMat
        );

        // render view
        CriRenderView('matiere_nav_menu', $vars);
    }

    /**
     * Render Meta Box content
     */
    public function render_veille_box_content() {
        // prepare vars
        $vars = array(
            'matieres' => $this->listMat
        );

        // render view
        CriRenderView('veille_nav_menu', $vars);
    }

    /**
     * Render Meta Box content
     */
    public function render_archive_box_content() {
        // prepare vars
        $vars = array(
            'flashes' => array(
                'title' => 'Flash Infos',
                'link' => MvcRouter::public_url(array('controller' => 'flashes', 'action' => 'index'))
            ),
            'veilles' => array(
                'title' => 'Veilles Juridiques',
                'link' => MvcRouter::public_url(array('controller' => 'veilles', 'action' => 'index'))
            ),
            'formations' => array(
                'title' => 'Formations',
                'link' => MvcRouter::public_url(array('controller' => 'formations', 'action' => 'index'))
            ),
            'vie_cridons' => array(
                'title' => 'Vie Cridon',
                'link' => MvcRouter::public_url(array('controller' => 'vie_cridons', 'action' => 'index'))
            ),
            'cahier_cridons' => array(
                'title' => 'Cahiers du Cridon',
                'link' => MvcRouter::public_url(array('controller' => 'cahier_cridons', 'action' => 'index'))
            ),
        );

        // render view
        CriRenderView('archive_nav_menu', $vars);
    }
}