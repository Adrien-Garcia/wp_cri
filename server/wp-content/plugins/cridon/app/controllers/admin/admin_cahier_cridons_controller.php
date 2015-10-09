<?php

class AdminCahierCridonsController extends MvcAdminController {
    
    var $default_columns = array('id', 'post' => array('label'=> 'Titre' ,'value_method' => 'post_edit_link'));
    public function index() {
        $this->init_default_columns();
        $this->process_params_for_search();
        $collection = $this->model->paginate($this->params);
        $this->set('objects', $collection['objects']);
        $this->set_pagination($collection);
        //Load custom helper
        $this->load_helper('AdminPost');
    }
    public function add()
    {
        //Default
        $this->redirect('post-new.php?cridon_type='.$this->trim( $this->name ),301);
    }
    public function edit()
    {
        //Default
        $object = MvcObjectRegistry::get_object($this->model->name);
        $this->redirect( $this->samplePostEditUrl($object, $this),301);
    }

    public function post_edit_link($object)
    {      
        $aOptionList = array(
                '__name'    => 'post_title'
        );
        $this->prepareData($aOptionList, $object->post);
        return empty($object->post) ? null : '<a href="'.$this->postEditUrl($object, $this).'" title="Edit">'.$object->post->__name.'</a>';
    }
    private function trim( $str ){
        return str_replace('admin_', '', $str);
    }
    private function postEditUrl( $object,$controller ){
        return admin_url( 'post.php?post='.$object->post_id.'&action=edit&cridon_type='.$this->trim($controller->name) );
    }
    private function samplePostEditUrl( $object,$controller ){
        return 'post.php?post='.$object->post_id.'&action=edit&cridon_type='.$this->trim($controller->name);
    }
    private function prepareData($aOptionList, $aData)
    {
        if (is_array($aData) && count($aData) > 0) {
            foreach ($aData as $oData) {
                foreach ($aOptionList as $sKey => $sVal) {
                    $oData->$sKey = $oData->$sVal;
                }
            }
        } elseif(is_object($aData)) {
            foreach ($aOptionList as $sKey => $sVal) {
                $aData->$sKey = $aData->$sVal;
            }
        }
    }
    
}

?>