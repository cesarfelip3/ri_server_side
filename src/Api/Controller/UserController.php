<?php

namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Api\Controller\BaseController;

use Model\User;
use Model\Alert;
use Model\Appointment;

class UserController extends BaseController
{

    public function __construct($request, $app)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = new Response();
    }

    public function addUser()
    {

        $email = $this->request->get("email", "");
        $token = $this->request->get("token", "");
        $firstname = $this->request->get("firstname", "");
        $lastname = $this->request->get("lastname", "");
        $username = $this->request->get("username", "");
        $fullname = $this->request->get("fullname", "");

        $data = array(
            "email" => $email,
            "token" => $token,
            "firstname" => $firstname,
            "lastname" => $lastname,
            "username" => $username,
            "fullname" => $fullname
        );

        if (empty ($token)) {
            return $this->setFailed("invalid user ID");
        }

        $user = new User();
        $uuid = "";
        $uuid = $user->userExistsByToken($token);
        if (!$uuid) {
            $uuid = $user->addUser($data);
            if (empty ($uuid)) {
                return $this->setFailed("add user wrong");
            }
        }

        return $this->setSuccess("", array ("user_uuid" => $uuid));
    }

    public function addAlert ()
    {
        $token = $this->request->get("token", "");
        $name = $this->request->get("name", "");
        $description = $this->request->get("description", "");
        $time = $this->request->get("time", 0);

        $time = intval ($time);
        if ($time == 0) {
            return $this->setFailed("The timestamp is invalid");
        }

        $data["name"] = $name;
        $data["description"] = $description;
        $data["time"] = $time;

        $user = new User();
        $user_uuid = $user->userExistsByToken($token);
        if (!$user_uuid) {
            return $this->setFailed("No user for this token = $token");
        }

        $data["user_uuid"] = $user_uuid;

        $alert = new Alert();

        if ($alert->addAlert()) {

        } else {
            return $this->setFailed("Wrong db operation");
        }

        return true;
    }

    public function addAppointment ()
    {
        $token = $this->request->get("token", "");
        $name = $this->request->get("name", "");
        $description = $this->request->get("description", "");
        $time = $this->request->get("time", 0);

        $time = intval ($time);
        if ($time == 0) {
            return $this->setFailed("The timestamp is invalid");
        }

        $data["name"] = $name;
        $data["description"] = $description;
        $data["time"] = $time;

        $user = new User();
        $user_uuid = $user->userExistsByToken($token);
        if (!$user_uuid) {
            return $this->setFailed("No user for this token = $token");
        }

        $data["user_uuid"] = $user_uuid;

        $appoint = new Appointment();

        if ($appint->addAppoint()) {

        } else {
            return $this->setFailed("Wrong db operation");
        }

        return true;
    }

}