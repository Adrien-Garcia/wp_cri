<?php

/**
 * Class SelectHelper
 *
 * @author eTech
 */
class SelectHelper extends MvcFormHelper
{

    /**
     * @param string $field_name
     * @param array  $options
     * @param mixed  $object
     *
     * @return string
     */
    public function select($field_name, $options = array(), $object)
    {
        $html = $this->before_input($field_name, $options);
        $html .= $this->select_tag($field_name, $options, $object);
        $html .= $this->after_input($field_name, $options);

        return $html;
    }

    /**
     * @param string $field_name
     * @param array  $options
     * @param mixed  $object
     *
     * @return string
     */
    public function select_tag($field_name, $options = array(), $object)
    {
        $defaults           = array(
            'empty' => false,
            'value' => null
        );
        $obj_name           = ( $object != null ) ? $object->__model_name : $options['model'];
        $options            = array_merge($defaults, $options);
        $options['options'] = empty( $options['options'] ) ? array() : $options['options'];
        $options['name']    = $field_name;
        $attributes_html    = ' name=data[' . $obj_name . '][' . $options['attr'] . ']';
        $html               = '<select' . $attributes_html . '>';
        if ($options['empty']) {
            $empty_name = is_string($options['empty']) ? $options['empty'] : '';
            $html .= '<option value="">' . $empty_name . '</option>';
        }
        foreach ($options['options'] as $key => $value) {
            $selected_attribute = ( ( $object != null ) && ( $object->$options['attr'] == $key ) ) ? ' selected="selected"' : '';
            $html .= '<option value="' . parent::esc_attr($key) . '"' . $selected_attribute . '>' . $value . '</option>';
        }
        $html .= '</select>';

        return $html;
    }

    /**
     * @param       $field_name
     * @param array $options
     *
     * @return string
     */
    private function before_input($field_name, $options)
    {
        $defaults = array(
            'before' => '<div>'
        );
        $options  = array_merge($defaults, $options);
        $html     = $options['before'];
        if (!empty( $options['label'] )) {
            $html .= '<label for="' . $options['id'] . '">' . $options['label'] . '</label>';
        }

        return $html;
    }

    /**
     * @param string $field_name
     * @param array  $options
     *
     * @return mixed
     */
    private function after_input($field_name, $options)
    {
        $defaults = array(
            'after' => '</div>'
        );
        $options  = array_merge($defaults, $options);
        $html     = $options['after'];

        return $html;
    }

}
