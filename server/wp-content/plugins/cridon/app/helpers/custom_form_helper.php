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
            'action' => !empty($this->controller) && !empty($this->controller->action) ? $this->controller->action : null,
            'controller' => MvcInflector::tableize($model_name),
            'public' => false
        );

        $options = array_merge($defaults, $options);
        $this->model_name = $model_name;
        $this->object = MvcObjectRegistry::get_object($model_name);
        $this->model = MvcModelRegistry::get_model($model_name);
        $this->schema = $this->model->schema;
        //surcharge spécifique pour le cas d'un fichier
        $object_id = !empty($this->object) && !empty($this->object->__id) ? $this->object->__id : null;
        $router_options = array('controller' => $options['controller'], 'action' => $options['action']);
        if ($object_id) {
            $router_options['id'] = $object_id;
        }
        $html = '<form action="'.MvcRouter::admin_url($router_options).'"';
        if ($options['enctype']) {
            $html .= ' enctype="multipart/form-data"';
        }

        $html .= ' method="post">';

        if ($object_id) {
            $html .= '<input type="hidden" id="'.$this->input_id('hidden_id').'" name="'.$this->input_name('id').'" value="'.$object_id.'" />';
        }
        return $html;
    }
    
    public function file_input($field_name, $options=array()) {
        $defaults = array(
            'id' => $this->input_id($field_name),
            'name' => $this->input_name($field_name),
            'type' => 'file'
        );
        $options = array_merge($defaults, $options);
        $attributes_html = self::attributes_html($options, 'input');
        $html = $this->before_input($field_name, $options);
        $html .= '<input'.$attributes_html.' />';
        $html .= $this->after_input($field_name, $options);
        return $html;
    }

    public function date_input($field_name, $options = array()) {
        $defaults = array(
            'id' => $this->input_id($field_name),
            'name' => $this->input_name($field_name),
            'type' => 'text',
            'class' => 'datepicker',
            'value' => date('d-m-Y')
        );
        $timestamp = strtotime($options['value']);
        $options['value'] = strftime('%d-%m-%G',$timestamp);
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

    public function select_tag($field_name, $options=array()) {
        $defaults = array(
            'empty' => false,
            'value' => null
        );

        $options = array_merge($defaults, $options);
        $options['options'] = empty($options['options']) ? array() : $options['options'];
        $options['name'] = $field_name;
        $attributes_html = self::attributes_html($options, 'select');
        $html = '<select'.$attributes_html.'>';
        if ($options['empty']) {
            $empty_name = is_string($options['empty']) ? $options['empty'] : '';
            $html .= '<option value="">'.$empty_name.'</option>';
        }
        $optGroup = false;
        $oldGroup = false;
        foreach ($options['options'] as $key => $value) {
            if (is_object($value)) {
                $key = $value->__id;
                $oldGroup = $optGroup;
                if (isset($value->__group) && ($value->__group !== $optGroup)) { // si group exist et différent du précédent
                    $optGroup = $value->__group;
                } else if (!isset($value->__group)) { // si pas de groupe
                    $optGroup = false;
                }
                $value = $value->__name;
            }
            if ($optGroup !== $oldGroup && $oldGroup) { // si groupe différent mais pas premier
                $html .= '</optgroup>';
            }
            if ($optGroup !== $oldGroup && $optGroup) { // si groupe différent et group existe
                $html .= '<optgroup label="'.$optGroup.'">';
                $hasGroup = true;
            }
            $selected_attribute = $options['value'] == $key ? ' selected="selected"' : '';
            $html .= '<option value="'.$this->esc_attr($key).'"'.$selected_attribute.'>'.$value.'</option>';
        }
        if ($optGroup) {
            $html .= '</optgroup>';
        }
        $html .= '</select>';
        return $html;
    }
}
