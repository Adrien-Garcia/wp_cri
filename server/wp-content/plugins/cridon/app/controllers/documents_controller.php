<?php

class DocumentsController extends MvcPublicController
{

    public function importinitial()
    {
        $this->model->importInitial();
    }
}