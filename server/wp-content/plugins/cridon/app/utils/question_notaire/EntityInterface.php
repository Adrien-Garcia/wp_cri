<?php

/**
 *
 * This file is part of project 
 *
 * File name : EntityInterface.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

//Simple interface for entity
interface EntityInterface {
   public function setMvcModel( $model );
   public function getMvcModel();
}
