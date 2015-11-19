<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIListChild.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class UIListChild extends UIFields{
    
    private $content;
    
    public function setContent( $content ){
        $this->content = $content;
    }
    
    public function create(){
        $parentId = $this->getId();
        $id = ( empty( $parentId ) ) ? $this->getName() : $this->getId();
        $html = '<li ';
        $html .= ' class="'.$this->getClass().'"';
        $html .= ' id="'.$id.'" >';
        $html .= $this->printContent( $this->content );
        $html .= ' </li>';
        return $html;
    }
}
