<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIText.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class UIText extends UIFields{
    private $placeholder;
    
    public function setPlaceholder( $placeholder ){
        $this->placeholder = $placeholder;
    }
    
    public function getPlaceholder(){
        return $this->placeholder;
    }
    
    public function create(){
        $id = ( empty( $this->getId() ) ) ? $this->getName() : $this->getId();
        $html = '<input type="text"';
        if( !empty( $this->placeholder ) ){
            $html .= ' placeholder="'.$this->placeholder.'"';
        }
        $html .= ' class="'.$this->getClass().'"';
        $html .= ' id="'.$id.'"';
        $html .= ' name="'.$this->getName().'"';
        $html .= ' />';
        return $html;
    }
}
