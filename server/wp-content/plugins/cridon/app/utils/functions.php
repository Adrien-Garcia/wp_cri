<?php

/**
 *
 * This file is part of project 
 *
 * File name : functions.php
 * Project   : wp_cridon
 *
 * @author Etech
 * @contributor Fabrice MILA
 *
 */

/**
 * Global function to find posts in table associate 
 * 
 * @global object $cri_container
 * @param array $options See options of query builder in models/query_builder.php 
 * @return array or object
 */
function criQueryPosts( $options = array() ){
    global $cri_container;
    $table = 'posts';
    $defaultOptions = array(
        'synonym' => 'p',
        'limit'   => false,
        'join'    => array(
            'veille' => array(
                'table'  => 'veille v',
                'column' => 'v.post_id = p.ID',
                'type'   => 'left'
            )
        )
    );
    $options = array_merge( $defaultOptions, $options );
    $query_builder = $cri_container->get( 'query_builder' );
    if( $options['limit'] ){
        return $query_builder->findOne( $table, $options,'p.ID' );
    }else{
        unset( $options['limit'] );
        return $query_builder->findAll( $table, $options,'p.ID' );
    }    
}
/**
 * Find a latest post by model
 * 
 * @param string $model Model name <b>without prefix, e.g: cri_veille</b>
 * @return null or object
 */
function criGetLastestPost( $model ){
    if( !is_string( $model ) && empty( $model ) ){
        return null;
    }
    $options = array(
        'fields' => 'p.post_title,p.post_date,p.post_content,'.$model[0].'.id as join_id',
        'limit' => 1,
        'join'  => array(
            $model => array(
                'table' => $model.' '.$model[0],
                'column' => $model[0].'.post_id = p.ID'
            )
        ),
        'conditions' => 'p.post_status = "publish"',
        'order' => 'DESC'
    );
    $result = criQueryPosts( $options );
    if( $result ){
        $latest = new stdClass();
        $latest->title   = $result->post_title;
        $latest->content = $result->post_content;
        $option = array(
            'controller' => $model.'s',
            'action'     => 'show',
            'id'         => $result->join_id
        );
        $latest->link = MvcRouter::public_url($option);
        return $latest;
    }
    return null;
}

/**
 * Form params setter (to be called on the login template)
 *
 * @param string $formAttributeId
 * @param string $loginAttributeId
 * @param string $passwdAttributeId
 * @param string $errorBlocAttributeId
 */
if (!function_exists('criSetLoginFormOptions')) {
    function criSetLoginFormOptions(
        $formAttributeId
        , $loginAttributeId
        , $passwdAttributeId
        , $errorBlocAttributeId
    ) {
        // all params are needed to be set
        if ($formAttributeId
            && $loginAttributeId
            && $passwdAttributeId
            && $errorBlocAttributeId
        ) {
            global $cri_container;

            $cri_container->setLoginFormId($formAttributeId);
            $cri_container->setLoginFieldId($loginAttributeId);
            $cri_container->setPasswordFieldId($passwdAttributeId);
            $cri_container->setErrorBlocId($errorBlocAttributeId);

            add_action('wp_enqueue_scripts', append_js_var());
        }
    }

    // hook for overridinf default login form  var
    function append_js_var()
    {
        global $cri_container;

        require_once ABSPATH . WPINC . '/pluggable.php';

        // only in front
        if (!is_admin()) {
        ?>
        <script type="text/javascript">
            var loginFormIdOverride = '<?php echo $cri_container->getLoginFormId() ?>',
                errorBlocIdOverride = '<?php echo $cri_container->getErrorBlocId() ?>',
                loginFieldIdOverride = '<?php echo $cri_container->getLoginFieldId() ?>',
                passwordFieldIdOverride = '<?php echo $cri_container->getPasswordFieldId() ?>';
        </script>
    <?php
        }
    }
}