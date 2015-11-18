<?php

/**
 *
 * This file is part of project 
 *
 * File name : UISpan.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class UISpan extends UIFields{
    private $text;
    
    public function setText( $text ){
        $this->text = $text;
    }
    
    public function create(){
        $html = '<span';
        $html .= ' class="'.$this->getClass().'"';
        $html .= '>'.$this->text;
        $html .= '</span>';
        return $html;
    }
}
