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
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 )
    {
        // indexation des menus parents
        if ($depth == 0) {
            $this->offset++;
        }
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

        $classes = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;

        /**
         * Filter the CSS class(es) applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
         * @param object $item    The current menu item.
         * @param array  $args    An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth   Depth of menu item. Used for padding.
         */
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

        /**
         * Filter the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param object $item    The current menu item.
         * @param array  $args    An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li' . $id . $class_names .'>';

        $atts = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
        $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
        $atts['href']   = ! empty( $item->url )        ? $item->url        : '';

        /**
         * Filter the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title  Title attribute.
         *     @type string $target Target attribute.
         *     @type string $rel    The rel attribute.
         *     @type string $href   The href attribute.
         * }
         * @param object $item  The current menu item.
         * @param array  $args  An array of {@see wp_nav_menu()} arguments.
         * @param int    $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

        foreach($item->classes as $class) {
            if (preg_match('/analytics/', $class)) {
                $atts['class'] = $class;
            }
        }

        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        /** This filter is documented in wp-includes/post-template.php */
        $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;

        /**
         * Filter a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string $item_output The menu item's starting HTML output.
         * @param object $item        Menu item data object.
         * @param int    $depth       Depth of menu item. Used for padding.
         * @param array  $args        An array of {@see wp_nav_menu()} arguments.
         */
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
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
                case 2:
                case 3:
                    
                    break;
                case 4: // Bloc Commander
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
                case 5: // Bloc Connaitre
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