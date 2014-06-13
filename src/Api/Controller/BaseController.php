<?php

namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;

class BaseController
{

    protected $app;
    protected $request;
    protected $response;
    protected $error = array(
        "status" => "success",
        "message" => "",
        "data" => array()
    );

    public function setSuccess($message, $data = array())
    {
        $this->error["status"] = "success";
        $this->error["message"] = empty ($message) ? "" : $message;
        $this->error["data"] = $data;
        return true;
    }

    public function setFailed($message, $data = array())
    {
        $this->error["status"] = "failure";
        $this->error["message"] = empty ($message) ? "" : $message;
        $this->error["data"] = $data;
        return false;
    }

    public function getError()
    {
        return $this->error;
    }

    public function json($message, $output = true)
    {
        if (!is_array($message) || !is_object($message)) {
            return false;
        }

        if ($output) {
            $this->app->json($message, 404);
        }

        return json_encode($output);
    }

    public function getUUId()
    {

    }

    public function debug($data, $html = true, $exit = true)
    {

        if ($html) {

            print_r("<textarea style='width:500px;height:300px'>");
            print_r($data);
            print_r("</textarea>");

        }

        if ($exit) {
            exit;
        }
    }
}