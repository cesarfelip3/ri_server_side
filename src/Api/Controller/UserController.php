<?php

namespace Api\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Api\Controller\BaseController;

use Model\User;
use Model\Alert;
use Model\Appointment;
use Model\File;

class UserController extends BaseController
{

    public function __construct($request, $app)
    {
        $this->app = $app;
        $this->request = $request;
        $this->response = new Response();
    }

    public function addUser($uploadFolder)
    {

        $email = $this->request->get("email", "");
        $username = $this->request->get("username", "");
        $dev_token = $this->request->get("dev_token", "");

        $data = array(
            "email" => $email,
            "username" => $username,
            "dev_token" => $dev_token
        );

        if (empty ($email)) {
            return $this->setFailed("invalid user email");
        }

        $user = new User();
        $uuid = "";
        $uuid = $user->userExistsByEmail($email);
        $userId = $uuid;

        if (empty($userId)) {

            $uuid = $user->addUser($data);
            if (empty ($uuid)) {
                return $this->setFailed("add user wrong");
            }

            $userId = $uuid;
        } else {

            $data['user_uuid'] = $uuid;
            $user->updateUser($data);
        }

        $user_uuid = $userId;

        $this->setSuccess("", array ("user_uuid"=>$user_uuid));
        return true;
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