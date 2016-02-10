<?php

class QuestionsController extends MvcPublicController
{
    /**
     * Add question action
     */
    public function add()
    {
        $ret = CONST_QUESTION_ACTION_SUCCESSFUL;
        $resp = CriPostQuestion();
        if (is_array($resp)) {
            if (is_array($resp['error']) && count($resp['error']) > 0) {
                $ret = $resp;
            } elseif(isset($resp['resume'])) {
                CriSendPostQuestConfirmation($resp);
            }
           // $ret
        } elseif(!$resp) {
            $ret = array(
                'error' => array( CONST_QUESTION_ACTION_ERROR )
            );
        }

        echo json_encode($ret);

        die;
    }
}