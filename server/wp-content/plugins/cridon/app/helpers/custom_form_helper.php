<?php

/**
 *
 * This file is part of project 
 *
 * File name : custom_form_helper.php
 * Project   : wp_cridon_prototype
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

class CustomFormHelper extends MvcFormHelper {
    
    /**
     * 
     * @param string $model_name
     * @param array $options
     * @return string
     */
    //@override
    public function create($model_name, $options=array()) {
        $defaults = array(
            'action' => $this->controller->action,
            'controller' => MvcInflector::tableize($model_name),
            'public' => false
        );
        $options = array_merge($defaults, $options);
        $this->model_name = $model_name;
        $this->object = MvcObjectRegistry::get_object($model_name);
        $this->model = MvcModelRegistry::get_model($model_name);
        $this->schema = $this->model->schema;
        //surcharge spécifique pour le cas d'un fichier
        if ($options['enctype']) {
            $object_id = !empty($this->object) && !empty($this->object->__id) ? $this->object->__id : null;
            $router_options = array('controller' => $options['controller'], 'action' => $options['action']);
            if ($object_id) {
                $router_options['id'] = $object_id;
            }
            $html = '<form action="'.MvcRouter::admin_url($router_options).'"';
            $html .= ' enctype="multipart/form-data"';

            if ($options['public']) {
                $html .= ' method="post">';
            } else {
                $html .= ' method="post">';
            }
            
            if ($object_id) {
                $html .= '<input type="hidden" id="'.$this->input_id('hidden_id').'" name="'.$this->input_name('id').'" value="'.$object_id.'" />';
            }
        }
        return $html;
    }
    
    public function file_input($field_name, $options=array()) {
        $defaults = array(
            'id' => $this->input_id($field_name),
            'name' => $this->input_name($field_name),
            'type' => 'text'
        );
        $options = array_merge($defaults, $options);
        $attributes_html = self::attributes_html($options, 'input');
        $html = $this->before_input($field_name, $options);
        $html .= '<input'.$attributes_html.' />';
        $html .= $this->after_input($field_name, $options);
        return $html;
    }    
    private function before_input($field_name, $options) {
        $defaults = array(
            'before' => '<div>'
        );
        $options = array_merge($defaults, $options);
        $html = $options['before'];
        if (!empty($options['label'])) {
            $html .= '<label for="'.$options['id'].'">'.$options['label'].'</label>';
        }
        return $html;
    }
    
    private function after_input($field_name, $options) {
        $defaults = array(
            'after' => '</div>'
        );
        $options = array_merge($defaults, $options);
        $html = $options['after'];
        return $html;
    }
    private function input_id($field_name) {
        return $this->model_name.MvcInflector::camelize($field_name);
    }
    
    private function input_name($field_name) {
        return 'data['.$this->model_name.']['.MvcInflector::underscore($field_name).']';
    }
}
