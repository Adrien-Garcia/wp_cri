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
        $object_id = !empty($this->object) && !empty($this->object->__id) ? $this->object->__id : null;
        $router_options = array('controller' => $options['controller'], 'action' => $options['action']);
        if ($object_id) {
            $router_options['id'] = $object_id;
        }
        $html = '<form action="'.MvcRouter::admin_url($router_options).'"';
        if( $options['enctype'] ){
            $html .= ' enctype="multipart/form-data"';
        }
        if ($options['public']) {
            $html .= ' method="post">';
        } else {
            $html .= ' method="post">';
        }
        
        if ($object_id) {
            $html .= '<input type="hidden" id="'.$this->input_id('hidden_id').'" name="'.$this->input_name('id').'" value="'.$object_id.'" />';
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
    private function get_type_from_sql_schema($schema) {
        switch($schema['type']) {
            case 'varchar':
                return 'text';
            case 'text':
                return 'textarea';
        
        }
        if ($schema['type'] == 'tinyint' && $schema['length'] == '1') {
            return 'checkbox';
        }
    }
    public function input($field_name, $options=array()) {
        if (!empty($this->schema[$field_name])) {
            $schema = $this->schema[$field_name];
            $type = $this->get_type_from_sql_schema($schema);
            $defaults = array(
                'type' => $type,
                'label' => MvcInflector::titleize($schema['field']),
                'value' => empty($this->object->$field_name) ? '' : $this->object->$field_name
            );
            if ($type == 'checkbox') {
                unset($defaults['value']);
            }
            $options = array_merge($defaults, $options);
            $options['type'] = empty($options['type']) ? 'text' : $options['type'];
            $html = $this->{$options['type'].'_input'}($field_name, $options);
            return $html;
        } else {
            MvcError::fatal('Field "'.$field_name.'" not found for use in a form input.');
            return '';
        }
    }
}
