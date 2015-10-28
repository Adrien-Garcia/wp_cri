<?php

/**
 *
 * This file is part of project 
 *
 * File name : CridonPostParser.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

/**
 * Simple class which is generate an array contains object WP_Post
 */
class CridonPostParser {
    
    /**
     * Generate array of object WP_Post
     * 
     * @param array $data
     * @return array
     */
    public function generatePostArray( $data ){
        return $this->toPostArray( $data );
    }
    
    /**
     * Build array with object WP_Post
     * 
     * @param array $data
     * @return array
     */
    private function toPostArray( $data ){
        $result = array();
        foreach( $data as $v ){
            $result[] = $v->post;
        }
        return $result;
    }
}
