<?php

class QuestionsController extends MvcPublicController
{
    /**
     * Add question action
     */
    public function add()
    {
        $ret = CriPostQuestion() ? CONST_QUESTION_ACTION_SUCCESSFUL : CONST_QUESTION_ACTION_ERROR;
        echo json_encode($ret);

        die;
    }
}