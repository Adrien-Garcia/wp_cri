<?php

/**
 *
 * This file is part of project 
 *
 * File name : UILink.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class UILink extends UIFields{
    private $href;
    private $content;
    private $text;
    
    public function setHref( $href ){
        $this->href = $href;
    }
    public function setText( $text ){
        $this->text = $text;
    }
    public function setContent( $content ){
        $this->content = $content;
    }
    
    public function create(){
        $href = ( empty($this->href ) ) ? '#' : $this->href;
        $parentId = $this->getId();
        $id = ( empty( $parentId ) ) ? $this->getName() : $this->getId();
        $html = '<a href="'.$href.'"';
        $html .= ' class="'.$this->getClass().'"';
        $html .= ' id="'.$id.'">';
        if( !empty( $this->content ) ){
            $html .= $this->printContent( $this->content );            
        }
        $html .= $this->text;
        $html .= ' </a>';
        return $html;
    }
}
