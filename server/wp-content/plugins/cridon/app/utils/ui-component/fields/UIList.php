<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIList.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class UIList extends UIFields{
    
    private $start = '<ul ';
    private $end   = '</ul>';
    private $content;
    
    public function setContent( $content ){
        $this->content = $content;
    }
    
    public function setStart( $start ){
        $this->start = $start;
    }
    
    public function setEnd( $end ){
        $this->end = $end;
    }
    
    public function create(){
        $id = ( empty( $this->getId() ) ) ? $this->getName() : $this->getId();
        $html = $this->start;
        $html .= ' class="'.$this->getClass().'"';
        $html .= ' id="'. $id .'">';
        $html .= $this->printContent( $this->content );
        $html .= $this->end;
        return $html;
    }
}
