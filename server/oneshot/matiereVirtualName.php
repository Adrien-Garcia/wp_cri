<?php
/**
 * Created by JETPULP.
 * User: valbert
 * Date: 12/12/2015
 * Time: 11:39
 */
// set utf-8 encoding
header('Content-type: text/plain; charset=utf-8');
if (!defined( 'WP_ADMIN' )) {
    define('WP_ADMIN', true);
}
// load WP Core
require_once '../wp-load.php';


/**
 * @var $model Matiere
 */
$model = mvc_model('Matiere');
// call import action
$matieres = $model->find();
foreach ( $matieres as $matiere ){
    $virtual = sanitize_title($matiere->label);
    $options = array(
        'virtual_name' => $virtual
    );
    $model->update($matiere->id, $options );
}

echo 'Ok';
