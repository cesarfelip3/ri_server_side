<?php

namespace Model;

class Model {

    public static $DB;
    protected $db;

    protected $error = array (
        "status" => "success",
        "message" => "",
        "data" => array ()
    );

    public function setSuccess ($message, $data=array())
    {
        $this->error["status"] = "success";
        $this->error["message"] = empty ($message) ? "" : $message;
        $this->error["data"] = $data;
        return true;
    }

    public function setFailed ($message, $data=array())
    {
        $this->error["status"] = "failure";
        $this->error["message"] = empty ($message) ? "" : $message;
        $this->error["data"] = $data;
        return false;
    }

    public function getError ()
    {
        return $this->error;
    }


}