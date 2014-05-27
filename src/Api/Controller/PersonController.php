<?php

namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


use Api\Controller\BaseController;

class PersonController extends BaseController {

    public function __construct($request, $app)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = new Response();
    }

    public function addPerson ()
    {

    }

    public function addAlert ()
    {

    }

    public function addAppointment ()
    {

    }

} 