<?php

namespace Nutrition\Tests\data\controller;

use Nutrition\AbstractController;

class Controller extends AbstractController
{
    public function pageNumber()
    {
        echo $this->getPageNumber();
    }

    public function pageLimit()
    {
        echo $this->getPageLimit();
    }

    public function showError($app, $params)
    {
        $this->error($params['code']);
    }

    public function showErrorNotFound()
    {
        $this->errorNotFound();
    }

    public function showErrorForbidden()
    {
        $this->errorForbidden();
    }

    public function showErrorInternalServer()
    {
        $this->errorInternalServer();
    }
}