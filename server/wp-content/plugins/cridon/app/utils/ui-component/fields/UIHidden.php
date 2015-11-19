<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIHidden.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class UIHidden extends UIFields{
    public function create(){
        $parentId = $this->getId();
        $id = ( empty( $parentId ) ) ? $this->getName() : $this->getId();
        $html = '<input type="hidden" ';
        $html .= ' value="'.$this->getValue().'"';
        $html .= ' id="'.$id.'"';
        $html .= ' name="'.$this->getName().'"';
        $html .= ' />';
        return $html;
    }
}
