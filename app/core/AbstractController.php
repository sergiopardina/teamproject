<?php

namespace app\core;

use app\models\AdsModel;

abstract class AbstractController implements indexable
{
    /**
     * @var View
     */
    protected $view;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Validator
     */
    protected $validate;

    /**
     * AbstractController constructor
     */
    public function __construct($template)
    {
        $this->model = new AdsModel();
        $this->view = new View($template);
    }
}