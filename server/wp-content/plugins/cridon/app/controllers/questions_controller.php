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
        if (is_array($resp) && isset($resp[0])) {
            $ret = $resp[0];
        } elseif(!$resp) {
            $ret = CONST_QUESTION_ACTION_ERROR;
        }

        echo json_encode($ret);

        die;
    }

    public function importinitial()
    {
        $this->model->importIntoCriQuestion();
    }
}