<?php

/**
 *
 * This file is part of project 
 *
 * File name : UIFields.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class UIFields {
    private $name;
    private $id;
    private $class;
    private $value;
    
    /**
     * Set id
     * @param string $id
     */
    public function setId( $id ){
       $this->id = $id;
    }
    
    /**
     * Set name
     * @param string $name
     */
    public function setName( $name ){
        $this->name = $name;
    }
    
    /**
     * Set class
     * 
     * @param string $class
     */
    public function setClass( $class ){
        $this->class = $class;
    }
    
    /**
     * Get name
     * @return string
     */
    public function getName(){
        return $this->name;
    }
    
    /**
     * Get id
     * 
     * @return string
     */
    public function getId(){
        return $this->id;
    }
    
    /**
     * Get class
     * @return string
     */
    public function getClass(){
        return $this->class;
    }
    
    /**
     * Set value
     * @param mixed $value
     */
    public function setValue( $value ){
        $this->value = $value;
    }
    
    /**
     * Get value 
     * 
     * @return mixed
     */
    public function getValue(){
        return $this->value;
    }
    
    /**
     * Display content
     * 
     * @param mixed $content
     * @return mixed
     */
    protected function printContent( $content ){
        if( !empty( $content ) ){
            if( is_array( $content ) ){
                $html = '';
                foreach( $content as $c ){
                    $html .= $this->printContent( $c );
                }
                return $html;
            }else{
                if( is_object( $content ) ){
                    return $content->create();
                }else{
                    return $content;
                }
            }
        } 
    }
}
