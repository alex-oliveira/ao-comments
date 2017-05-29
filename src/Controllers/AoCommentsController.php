<?php

namespace AoComments\Controllers;

use AoComments\Services\CommentService;
use AoScrud\Core\ScrudController;

class AoCommentsController extends ScrudController
{

    //------------------------------------------------------------------------------------------------------------------
    // DYNAMIC
    //------------------------------------------------------------------------------------------------------------------

    protected $dynamicClass;

    public function getDynamicClass()
    {
        return $this->dynamicClass;
    }

    //------------------------------------------------------------------------------------------------------------------
    // CONSTRUCTOR
    //------------------------------------------------------------------------------------------------------------------

    public function __construct(CommentService $service)
    {
        $this->service = $service->setDynamicClass($this->getDynamicClass());
    }

}